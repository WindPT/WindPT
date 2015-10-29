<?php

defined('WEKIT_VERSION') || exit('Forbidden');

class PwPasskey
{
    public static function getPassKey($uid)
    {
        Wind::import('EXT:torrent.service.dm.PwTorrentUserDm');

        $user = new PwUserBo($uid, true);

        $torrentUserDs = Wekit::load('EXT:torrent.service.PwTorrentUser');
        $torrentUser = $torrentUserDs->getTorrentUserByUid($uid);

        $user->passkey = $torrentUser['passkey'];

        if (!$user->passkey) {
            $user->passkey = self::makePassKey($user);
            $dm = new PwTorrentUserDm();
            $dm->setUid($uid)->setPassKey($user->passkey);
            $torrentUserDs->addTorrentUser($dm);
        } elseif (strlen($user->passkey) != 40) {
            $user->passkey = self::makePassKey($user);
            $dm = new PwTorrentUserDm($uid);
            $dm->setUid($uid)->setPassKey($user->passkey);
            $torrentUserDs->updateTorrentUser($dm);
        }

        return $user->passkey;
    }

    public static function makePassKey($user)
    {
        return sha1($user->username . Pw::time2str(Pw::getTime(), 'Y-m-d H:i:s') . $user->info['password']);
    }
}
