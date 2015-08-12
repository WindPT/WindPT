<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:forum.srv.threadDisplay.do.PwThreadDisplayDoBase');

class PwThreadDisplayDoTorrentSide extends PwThreadDisplayDoBase
{

    public function __construct()
    {
    }

    public function createHtmlAfterUserInfo($user, $read)
    {
        if (!in_array('threadside', Wekit::C('site', 'app.torrent.showuserinfo'))) {
            return null;
        }
        $torrents = Wekit::load('EXT:torrent.service.dao.PwTorrentDao')->fetchTorrentByUid($user['uid']);
        $histories = Wekit::load('EXT:torrent.service.dao.PwTorrentHistoryDao')->fetchTorrentHistoryByUid($user['uid']);

        $posted = count($torrents);

        foreach ($histories as $history) {
            $downloaded_total += $history['downloaded'];
            $uploaded_total += $history['uploaded'];
        }

        $downloaded_total = floor($downloaded_total / 1048567);
        $uploaded_total = floor($uploaded_total / 1048567);

        if ($downloaded_total != 0) {
            $rotio = round($uploaded_total / $downloaded_total, 2);
        } else {
            $rotio = 'Inf.';
        }

        echo '<div id="PTInfo">下载： ' . $downloaded_total . ' M<br>上传： ' . $uploaded_total . ' M<br>分享率： ' . $rotio . '<br>发布： ' . $posted . '</div>';
    }
}
