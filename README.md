WindPT
======

![WindPT Logo](extensions/torrent/res/images/WindPT.png)

**A plugin for PHPWind 9 to build a Private BitTorrent tracker with Front-end.**

[![StyleCI](https://styleci.io/repos/18007787/shield?style=flat&branch=master)](https://styleci.io/repos/18007787)
[![GitHub release](https://img.shields.io/github/release/WindPT/WindPT.svg)](https://github.com/WindPT/WindPT/releases/latest)
[![GitHub tag](https://img.shields.io/github/tag/WindPT/WindPT.svg)](https://github.com/WindPT/WindPT/releases)
[![Gitter](https://img.shields.io/gitter/room/WindPT/Lobby.svg)](https://gitter.im/WindPT/Lobby)

**[中文说明](README_CN.md)**

Pull requests and issues are welcome.

This extension **DOES NOT** provide a tracker server, you must install [WindPT/WindTurbine](https://github.com/WindPT/WindTurbine) for that.

There's a theme *MORE SUITABLE* than the default one of PHPWind 9 on [WindPT/PHPWind-WindPT-Theme](https://github.com/WindPT/PHPWind-WindPT-Theme).

## HOWTO

* This extension should be enabled manually on the Dashboard of PHPWind.
* Do not forget to add cron jobs on the Dashboard of PHPWind if you need the crons bundled in.
* You should create a directory named `torrents` with mask 0755 under the root of PHPWind manually if PHP have no permission to `write`.

```
/
|- cron <dir> // cron job [put files in PHPWind/src/service/cron/srv/do/]
|  |- PwCronDoClearPeers.php
|  |- PwCronDoClearTorrents.php
|  =
|- extensions
|  |- torrent <dir> // extension [put directory in PHPWind/src/extensions/]
=  =
```

## Donate us

[Donate us](https://7in0.me/#donate)

## License

GNU GENERAL PUBLIC LICENSE Version 2

More info see [LICENSE](LICENSE)
