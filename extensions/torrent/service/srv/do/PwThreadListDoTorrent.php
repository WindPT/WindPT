<?php
defined('WEKIT_VERSION') or exit(403);
Wind::import('SRV:forum.srv.threadList.do.PwThreadListDoBase');

class PwThreadListDoTorrent extends PwThreadListDoBase
{
    
    public function __construct() {
    }
    
    public function bulidThread($thread) {
        if (isset($thread['special']) && $thread['special'] == 'torrent') {
            $torrent = Wekit::load('EXT:torrent.service.PwTorrent')->getTorrentByTid($thread['tid']);
            $peers = Wekit::load('EXT:torrent.service.PwTorrentPeer')->getTorrentPeerByTorrent($torrent['id']);
            foreach ($peers as $peer) {
                if ($peer['seeder'] == 'yes') {
                    $seeder[] = $peer;
                } else {
                    $leecher[] = $peer;
                }
            }
            $thread['seeder'] = count($seeder);
            $thread['leecher'] = count($leecher);
        }
        return $thread;
    }
}
?>