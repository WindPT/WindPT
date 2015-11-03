WindPT
======

![WindPT Logo](extensions/torrent/res/images/WindPT.png)

**A plugin for PHPWind 9 to build a private BitTorrent tracker.**

[![Releases Downloads](https://img.shields.io/github/downloads/labs7in0/WindPT/latest/total.svg)](https://github.com/labs7in0/WindPT/releases/latest)

This project is still in early stage, DO NOT use it on production environment, pull requests are welcome.

This extension is designed to work with PHPWind 9.x, and `local search` plugin (a 3rd extension) is required by the theme bundled with this extension.

If you find any bugs or mistakes, please let me know by submitting issues or making pull requests.

* You should create a directory named `torrent` with mask 0755 under the root of PHPWind manually if php have no permission to `write`.
* This extension and its bundled theme should be enabled manually on the Dashboard of PHPWind.

```
/
|- cron                            // cron job [put in PHPWind/src/service/cron/srv/do]
|  |- PwCronDoClearPeers.php
|  |- PwCronDoClearTorrents.php
|  =
|- extensions
|  |- torrent <dir>                // extension [put in PHPWind/src/extensions/]
|  =
|- themes_site
|  |- pt <dir>                     // theme [put in PHPWind/themes/site/]
=  =
```

## Donate us

[Donate us](https://7in0.me/#donate)

##License

GNU GENERAL PUBLIC LICENSE Version 2

More info see [LICENSE](LICENSE)
