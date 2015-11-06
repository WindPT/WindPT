<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('EXT:torrent.service.srv.helper.PwUtils');

class PwSpaceProfileDoTorrent
{
    public function appDo($space)
    {
        $PwThread = Wekit::load('forum.PwThread');
        $showuserinfo = Wekit::C('site', 'app.torrent.showuserinfo');

        if (is_array($showuserinfo) && !in_array('profile', $showuserinfo)) {
            return;
        }

        $user = Wekit::load('EXT:torrent.service.PwTorrentUser')->getTorrentUserByUid($space->spaceUid);

        $this->histories = Wekit::load('EXT:torrent.service.PwTorrentHistory')->fetchTorrentHistoryByUid($space->spaceUid);
        $this->torrents = $PwThread->getThreadByUid($space->spaceUid);


        $this->passkey = $user['passkey'];

        if (is_array($this->histories)) {
            $PwTorrent = Wekit::load('EXT:torrent.service.PwTorrent');
            foreach ($this->histories as $key => $history) {
                $downloaded_total += $history['downloaded'];
                $uploaded_total += $history['uploaded'];

                $torrent = $PwTorrent->getTorrent($history['torrent']);
                $thread = $PwThread->getThread($torrent['tid']);

                if ($thread) {
                    $this->histories[$key]['subject'] = $thread['subject'];
                } else {
                    unset($this->histories[$key]);
                }
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
