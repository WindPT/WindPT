WindPT
======

A plugin for PHPWind 9.0 to build a private BitTorrent tracker.

This project is still in early stage, DO NOT use it on production environment, pull requests are welcome.

If you find any bugs or mistakes, please let me know by submitting a issue or making patches and pr to `dev` branch.

** You should create a directory named `torrent` under the root of PHPWind manually if php have no permission to `write` under the root ** .

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
