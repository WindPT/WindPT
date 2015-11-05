<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('EXT:torrent.service.srv.helper.PwUtils');

class PwSpaceProfileDoTorrent
{
    public function appDo($space)
    {
        $showuserinfo = Wekit::C('site', 'app.torrent.showuserinfo');

        if (is_array($showuserinfo) && !in_array('profile', $showuserinfo)) {
            return;
        }

        $user = Wekit::load('EXT:torrent.service.PwTorrentUser')->getTorrentUserByUid($space->spaceUid);
        $histories = Wekit::load('EXT:torrent.service.PwTorrentHistory')->fetchTorrentHistoryByUid($space->spaceUid);

        $this->torrents = Wekit::load('EXT:torrent.service.PwTorrent')->fetchTorrentByUid($space->spaceUid);


        $this->passkey = $user['passkey'];

        if (is_array($histories)) {
            foreach ($histories as $history) {
                $downloaded_total += $history['downloaded'];
                $uploaded_total += $history['uploaded'];
            }
        }

        if ($downloaded_total != 0) {
            $this->rotio = round($uploaded_total / $downloaded_total, 2);
        } else {
            $this->rotio = 'Inf.';
        }

        $this->downloaded_total = PwUtils::readableDataTransfer($downloaded_total);
        $this->uploaded_total = PwUtils::readableDataTransfer($uploaded_total);

        PwHook::template('displayProfileTorrentHtml', 'EXT:torrent.template.profile_injector_after_content', true, $this);
    }
}
