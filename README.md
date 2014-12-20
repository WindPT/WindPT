WindPT
======

A plugin for PHPWind 9.0 to build a private BitTorrent tracker.

Still under developing...

You should create a directory named `torrent` under the root of PHPWind manually.

```
/
|- cron
|  |- PwCronDoClearPeers.php      // Cron script for cleaning up peers without activity for a long time
|  |- PwCronDoClearTorrents.php   // Cron script for cleaning up torrents without activity for a long time
|  =
|- extensions
|  |- torrent <dir>                // extension
|  =
|- themes_site
|  |- pt <dir>                     // theme
=  =
```