WindPT
======

![WindPT Logo](extensions/torrent/res/images/WindPT.png)

**A plugin for PHPWind 9 to build a Private BitTorrent tracker with Front-end.**

[![StyleCI](https://styleci.io/repos/18007787/shield?style=flat&branch=master)](https://styleci.io/repos/18007787)
[![Releases](https://img.shields.io/github/release/kinosang/WindPT.svg)](https://github.com/kinosang/WindPT/releases/latest)

Pull requests and issues are welcome.

This extension **DOES NOT** provide a tracker server, you must install [kinosang/WindTurbine](https://github.com/kinosang/WindTurbine) for that.

There's a theme *MORE SUITABLE* than the default one of PHPWind 9 on [kinosang/PHPWind-WindPT-Theme](https://github.com/kinosang/PHPWind-WindPT-Theme).

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
