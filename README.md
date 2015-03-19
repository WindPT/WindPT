WindPT
======

A plugin for PHPWind 9.0 to build a private BitTorrent tracker.

This project is still in early stage, DO NOT use it on production environment, pull requests are welcome.

If you find any bugs or mistakes, please let me know by submitting a issue or making a pull request to `dev` branch.

You should create a directory named `torrent` under the root of PHPWind manually.

```
/
|- cron
|  |- PwCronDoClearPeers.php      // Cron script for cleaning up peers not active for a long time
|  |- PwCronDoClearTorrents.php   // Cron script for cleaning up torrents not active for a long time
|  =
|- extensions
|  |- torrent <dir>                // extension
|  =
|- themes_site
|  |- pt <dir>                     // theme
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
