# Simple BitTorrent Tracker

## Routes

```
 -------------------------------------------------- -------- -------- ------ -----------------------------------
  Name                                               Method   Scheme   Host   Path
 -------------------------------------------------- -------- -------- ------ -----------------------------------
  espend_bit_torrent_tracker_announce                GET        ANY      ANY    /announce
  espend_bit_torrent_tracker_proxy_rewrite_torrent   GET|POST   ANY      ANY    /proxy/rewrite-torrent
  espend_bit_torrent_tracker_proxy_announce          GET        ANY      ANY    /proxy/announce
 -------------------------------------------------- -------- -------- ------ -----------------------------------
```

## Rewrite

`/proxy/rewrite-torrent` rewrite torrent to use internal announce url. origin url is base64 encoded as `origin` query string 

```
Before:
http://torrent.ubuntu.com:6969/announce

After: 
/proxy/announce?origin=aHR0cDovL3RvcnJlbnQudWJ1bnR1LmNvbTo2OTY5L2Fubm91bmNl
```
