<?php

defined('WEKIT_VERSION') || exit('Forbidden');

class PwHeaderInfoDoTorrent
{
    public function appDo()
    {
        $showuserinfo = Wekit::C('site', 'app.torrent.showuserinfo');

        if (is_array($showuserinfo) && !in_array('headerinfo', $showuserinfo)) {
            return;
        }

        $user = Wekit::getLoginUser();

        if ($user) {
            $peers = Wekit::load('EXT:torrent.service.PwTorrentPeer')->fetchTorrentPeerByUid($user->uid);

            $seeding = $leeching = 0;
            if (is_array($peers)) {
                foreach ($peers as $peer) {
                    if ($peer['seeder'] == 'yes') {
                        $seeding++;
                    } else {
                        $leeching++;
                    }
                }
            }

            echo '<a href="' . WindUrlHelper::createUrl('space/profile/run?uid=' . $user->uid) . '">下载：' . $leeching . ' 做种：' . $seeding . '</a>';
        }
    }
}
