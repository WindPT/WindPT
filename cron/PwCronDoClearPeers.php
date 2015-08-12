<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:cron.srv.base.AbstractCronBase');

class PwCronDoClearPeers extends AbstractCronBase
{

    public function run($cronId)
    {
        $fids = Wekit::C('site', 'app.torrent.pt_threads');
        if (empty($fids)) {
            return null;
        }

        foreach ($fids as $fid) {
            $topics = Wekit::load('forum.PwThread')->getThreadByFid($fid, 0);
            foreach ($topics as $topic) {
                if ($topic['special'] != 'torrent' || $topic['disabled'] > 0) {
                    continue;
                }

                $torrent = Wekit::load('EXT:torrent.service.dao.PwTorrentDao')->getTorrentByTid($topic['tid']);
                $peers = Wekit::load('EXT:torrent.service.PwTorrentPeer')->getTorrentPeerByTorrent($torrent['id']);
                foreach ($peers as $peer) {
                    if (strtotime($peer['last_action']) < strtotime('-' . Wekit::C('site', 'app.torrent.cron.peertimeout') . ' minute')) {
                        Wekit::load('EXT:torrent.service.PwTorrentPeer')->deleteTorrentPeer($peer['id']);
                    }
                }

            }
        }
    }
}
