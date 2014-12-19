<?php
defined('WEKIT_VERSION') || exit('Forbidden');
class PwTorrentReadInjector extends PwBaseHookInjector {
    public function run() {
        Wind::import('EXT:torrent.service.srv.do.PwThreadDisplayDoTorrent');
        return new PwThreadDisplayDoTorrent($this->bp->tid, $this->bp->user);
    }
}