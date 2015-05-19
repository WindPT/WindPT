WindPT
======

A plugin for PHPWind 9.0 to build a private BitTorrent tracker.

This project is still in early stage, DO NOT use it on production environment, pull requests are welcome.

If you find any bugs or mistakes, please let me know by submitting a issue or making a pull request to `dev` branch.

You should create a directory named `torrent` under the root of PHPWind manually if php ** does not have permission to `write` under the root ** .

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

##Donate us
###PayPal
[me@7in0.me](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ABKLA5Z5MFL6Q)
###Alipay
![me@7in0.me](https://tfsimg.alipay.com/images/mobilecodec/T1tJtfXlxlXXXXXXXX)

[me@7in0.me](https://qr.alipay.com/aezw455od9facaie21)
###Okpay
[OK141453389](https://www.okpay.com/process.html?ok_receiver=OK141453389&ok_item_1_name=Donate&ok_currency=USD&ok_item_1_type=donation)
###Bitcoin
[coinbase/kinosang](https://www.coinbase.com/kinosang)

##License
GNU GENERAL PUBLIC LICENSE Version 2

More info see [LICENSE](LICENSE)
