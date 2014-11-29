<?php
defined('WEKIT_VERSION') || exit('Forbidden');
Wind::import('EXT:torrent.service.srv.do.PwPostDoTorrent');
class PwTorrentPostInjector extends PwBaseHookInjector {
    public function run() {
        return new PwPostDoTorrent($this->bp);
    }
    public function doadd() {
        $wikilink = (array)$this->getInput('wikilink', 'post');
        return new PwPostDoTorrent($this->bp, 0, $wikilink);
    }
}