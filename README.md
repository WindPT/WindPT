WindPT
======

![WindPT Logo](extensions/torrent/res/images/WindPT.png)

**A plugin for PHPWind 9 to build a private BitTorrent tracker with Front-end.**

[![Releases](https://img.shields.io/github/release/kinosang/WindPT.svg)](https://github.com/kinosang/WindPT/releases/latest)

Pull requests and issues are welcome.

There's an implementation in Golang of the tracker (only the tracker, it depends on WindPT as Front-end) on [kinosang/WindTurbine](https://github.com/kinosang/WindTurbine)

This extension is designed to work with PHPWind 9.x, and `local search` plugin (a 3rd extension) is required by the theme bundled with this extension.

## HOWTO

* You should create a directory named `torrent` with mask 0755 under the root of PHPWind manually if php have no permission to `write`.
* This extension and its bundled theme should be enabled manually on the Dashboard of PHPWind.
* Do not forget to add cron jobs on the Dashboard of PHPWind if you need the crons bundled in.
* Change all `credit*` fields in `pw_user_data` and `pw_windid_user_data` to `double`.

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

## License

GNU GENERAL PUBLIC LICENSE Version 2

More info see [LICENSE](LICENSE)
