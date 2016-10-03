<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:forum.srv.threadList.do.PwThreadListDoBase');

class PwThreadListDoTorrent extends PwThreadListDoBase
{
    public function __construct()
    {}

    public function bulidThread($thread)
    {
        if (isset($thread['special']) && $thread['special'] == 'torrent' && Wekit::C('site', 'theme.site.default') == 'pt' && !empty(Wekit::C('site', 'app.torrent.showpeers'))) {
            $torrent = Wekit::load('EXT:torrent.service.PwTorrent')->getTorrentByTid($thread['tid']);

            $seeder = $leecher = 0;

            $peers = Wekit::load('EXT:torrent.service.PwTorrentPeer')->getTorrentPeerByTorrentId($torrent['id']);

            if (is_array($peers)) {
                foreach ($peers as $peer) {
                    if ($peer['seeder'] == 1) {
                        $seeder++;
                    } else {
                        $leecher++;
                    }
                }
            }

            $thread['seeder']  = $seeder;
            $thread['leecher'] = $leecher;
        }
        return $thread;
    }
}
