<?php

namespace espend\BitTorrent\TrackerBundle\Controller;

use espend\BitTorrent\TrackerBundle\Response\FailureResponse;
use espend\BitTorrent\TrackerBundle\Response\TrackerResponse;
use espend\BitTorrent\TrackerBundle\Torrent\AnnounceUtil;
use GuzzleHttp\Exception\RequestException;
use PHP\BitTorrent\Decoder;
use PHP\BitTorrent\Torrent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ProxyController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function proxyAnnounceAction(Request $request)
    {
        if (!$request->query->has('origin')) {
            return new FailureResponse('Missing origin');
        }

        $query = $request->query->all();
        unset($query['origin']);

        $origin = base64_decode($request->get('origin'));

        $origin = AnnounceUtil::mergeUrlQuery($origin, $query);

        // proxy headers
        $headers = [];
        foreach(['Accept-Encoding', 'User-Agent'] as $header) {
            if($request->headers->has($header)) {
                $headers[$header] = $request->headers->get($header);
            }
        }

        try {
            $response = $this->get('espend_bit_torrent_tracker.guzzle_http.client')->get($origin, [
                'headers' => $headers,
            ]);
        } catch (RequestException $e) {
            return new FailureResponse($e->getMessage());
        }

        $content = (string)$response->getBody();

        try {
            $decoded = (new Decoder())->decode($content);
        } catch (\InvalidArgumentException $e) {
            return new FailureResponse('Invalid remote response');
        }

        return new TrackerResponse($decoded);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function rewriteTorrentAction(Request $request)
    {
        /** @var FormInterface $form */
        $form = $this->createFormBuilder()
            ->add('file', FileType::class)
            ->add('submit', SubmitType::class)
            ->getForm();

        if (!$request->isMethod('POST')) {
            return $this->render('@espendBitTorrentTracker/Proxy/rewrite-torrent.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        $form->handleRequest($request);

        /** @var UploadedFile $file */
        $file = $form->get('file')->getData();

        $torrent = Torrent::createFromTorrentFile($file->getPathname());
        $this->get('espend_bit_torrent_tracker.torrent.announce_rewriter')->rewriteAnnounceUrls($torrent);

        $filename = tempnam(sys_get_temp_dir(), 'torrent');
        $torrent->save($filename);

        return (new BinaryFileResponse($filename, 200, ['Content-Type' => 'application/x-bittorrent']))
            ->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $file->getClientOriginalName())
            ->deleteFileAfterSend(true);
    }
}
