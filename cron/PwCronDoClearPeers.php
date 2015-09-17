<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:cron.srv.base.AbstractCronBase');

class PwCronDoClearPeers extends AbstractCronBase
{
    public function run($cronId)
    {
        $torrents = Wekit::load('EXT:torrent.service.PwTorrent')->fetchTorrent();
        foreach ($torrents as $torrent) {
            $peers = Wekit::load('EXT:torrent.service.PwTorrentPeer')->getTorrentPeerByTorrent($torrent['id']);
            foreach ($peers as $peer) {
                if (strtotime($peer['last_action']) < strtotime('-' . Wekit::C('site', 'app.torrent.cron.peertimeout') . ' minute')) {
                    Wekit::load('EXT:torrent.service.PwTorrentPeer')->deleteTorrentPeer($peer['id']);
                }
            }
        }
    }
}
