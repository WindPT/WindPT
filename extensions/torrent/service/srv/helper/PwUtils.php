<?php

defined('WEKIT_VERSION') || exit('Forbidden');

class PwUtils
{
    public static function curl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    public static function readableDataTransfer($bytes)
    {
        if ($bytes < 1048576) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes < 1073741824) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes < 1099511627776) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes < 1125899906842624) {
            return number_format($bytes / 1099511627776, 3) . ' TB';
        } else {
            return number_format($bytes / 1125899906842624, 3) . ' PB';
        }
    }

    public static function readableHash($hash)
    {
        return preg_replace_callback('/./s', create_function('$matches', 'return sprintf("%02x", ord($matches[0]));'), str_pad($hash, 20));
    }

    public static function getPassKey($uid)
    {
        Wind::import('EXT:torrent.service.dm.PwTorrentUserDm');
        $torrentUserDs = Wekit::load('EXT:torrent.service.PwTorrentUser');

        $user = new PwUserBo($uid, true);

        $torrentUser = $torrentUserDs->getTorrentUserByUid($uid);

        $passkey = $torrentUser['passkey'];

        if (empty($passkey)) {
            $passkey = self::makePassKey($user);

            $dm = new PwTorrentUserDm($uid);
            $dm->setPassKey($passkey);
            $torrentUserDs->addTorrentUser($dm);
        } elseif (strlen($passkey) != 40) {
            $passkey = self::makePassKey($user);

            $dm = new PwTorrentUserDm($uid);
            $dm->setPassKey($passkey);
            $torrentUserDs->updateTorrentUser($dm);
        }

        return $passkey;
    }

    public static function makePassKey($user)
    {
        $passkey = sha1($user->uid . $user->username . Pw::getTime());

        $u = Wekit::load('EXT:torrent.service.PwTorrentUser')->getTorrentUserByPasskey($passkey);

        if (!empty($u)) {
            return self::makePassKey($user);
        } else {
            return $passkey;
        }
    }

    public static function getTrackerUrl($passkey)
    {
        if (Wekit::C('site', 'app.torrent.trackerserver') == '') {
            return WindUrlHelper::createUrl('/app/torrent/index/announce?passkey=' . $passkey);
        } else {
            return sprintf(Wekit::C('site', 'app.torrent.trackerserver'), $passkey);
        }
    }
}
