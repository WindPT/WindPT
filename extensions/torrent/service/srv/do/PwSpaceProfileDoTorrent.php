<?php

defined('WEKIT_VERSION') || exit('Forbidden');

class PwSpaceProfileDoTorrent
{
    public function appDo($space)
    {
        if (!in_array('profile', Wekit::C('site', 'app.torrent.showuserinfo'))) {
            return '';
        }
        $user = Wekit::load('EXT:torrent.service.dao.PwTorrentUserDao')->getTorrentUserByUid($space->{'spaceUid'});
        $torrents = Wekit::load('EXT:torrent.service.dao.PwTorrentDao')->fetchTorrentByUid($space->{'spaceUid'});
        $histories = Wekit::load('EXT:torrent.service.dao.PwTorrentHistoryDao')->fetchTorrentHistoryByUid($space->{'spaceUid'});

        $passkey = $user['passkey'];

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

        echo '<div class="space_profile"><h3><strong>PT个人信息</strong></h3>';
        if ($space->{'visitUid'} == $space->{'spaceUid'}) {
            echo '<dl class="cc"><dt>Passkey：</dt><dd><span id="passkey" style="background-color:rgb(51,51,51); color:rgb(51,51,51);">' . $passkey . '</span>&nbsp;<button id="btnToggle" onclick="if ($(\'#btnToggle\').text() == \'显示\') {$(\'#passkey\').css(\'background\', \'white\'); $(\'#btnToggle\').text(\'隐藏\');} else {$(\'#passkey\').css(\'background\', \'rgb(51,51,51)\');$(\'#btnToggle\').text(\'显示\');}">显示</button></dd></dl>';
            echo '<dl class="cc"><dt>订阅地址：</dt><dd><a href="' . WindUrlHelper::createUrl('/app/torrent/index/rss?uid=' . $space->{'spaceUid'} . '&passkey=' . $passkey) . '">RSS 链接（请勿泄露）</a></dd></dl>';
        }

        echo '<dl class="cc"><dt>下载：</dt><dd>' . $downloaded_total . ' M</dd></dl>';
        echo '<dl class="cc"><dt>上传：</dt><dd>' . $uploaded_total . ' M</dd></dl>';
        echo '<dl class="cc"><dt>分享率：</dt><dd>' . $rotio . '</dd></dl>';
        echo '<dl class="cc"><dt>发布：</dt><dd>' . $posted . '</dd></dl>';
        echo '</div>';
    }
}
