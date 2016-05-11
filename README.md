WindPT
======

![WindPT Logo](extensions/torrent/res/images/WindPT.png)

**A plugin for PHPWind 9 to build a private BitTorrent tracker.**

[![Downloads](https://img.shields.io/github/downloads/labs7in0/WindPT/total.svg)](https://github.com/labs7in0/WindPT/releases)
[![Releases](https://img.shields.io/github/release/labs7in0/WindPT.svg)](https://github.com/labs7in0/WindPT/releases/latest)
[![Releases Downloads](https://img.shields.io/github/downloads/labs7in0/WindPT/latest/total.svg)](https://github.com/labs7in0/WindPT/releases/latest)

This project is still under development, pull requests and issues are welcome.

This extension is designed to work with PHPWind 9.x, and `local search` plugin (a 3rd extension) is required by the theme bundled with this extension.

## HOWTO

* You should create a directory named `torrent` with mask 0755 under the root of PHPWind manually if php have no permission to `write`.
* This extension and its bundled theme should be enabled manually on the Dashboard of PHPWind.
* Do not forget to add cron jobs on the Dashboard of PHPWind if you need the crons bundled in.

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

### Protection

To protect your site, you can use `Site Preference` at `/admin.php?m=config&c=config` to choose which user group can access your site.

You should modify `src/applications/bbs/controller/filter/PwGlobalFilter.php` to allow tracker pass-by the global filter.

Find:

```php
if ($config['visit.state'] > 0)
```

Replace with

```php
if ($request['mca'] != 'app/index/announce' && $config['visit.state'] > 0)
```

## Donate us

[Donate us](https://7in0.me/#donate)

## License

GNU GENERAL PUBLIC LICENSE Version 2

More info see [LICENSE](LICENSE)
