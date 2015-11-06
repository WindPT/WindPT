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
            $histories = Wekit::load('EXT:torrent.service.PwTorrentHistory')->fetchTorrentHistoryByUid($user->uid);

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

            if (is_array($histories)) {
                foreach ($histories as $history) {
                    $downloaded_total += $history['downloaded'];
                    $uploaded_total += $history['uploaded'];
                }
            }

            if ($downloaded_total != 0) {
                $rotio = round($uploaded_total / $downloaded_total, 2);
            } else {
                $rotio = 'Inf.';
            }

            echo '<a href="' . WindUrlHelper::createUrl('space/profile/run?uid=' . $user->uid) . '">下载：' . $leeching . ' 做种：' . $seeding . ' 分享率：' . $rotio . '</a>';
        }
    }
}
