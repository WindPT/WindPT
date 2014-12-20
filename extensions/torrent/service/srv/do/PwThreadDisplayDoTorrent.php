<?php
defined('WEKIT_VERSION') || exit('Forbidden');
Wind::import('SRV:forum.srv.threadDisplay.do.PwThreadDisplayDoBase');
class PwThreadDisplayDoTorrent extends PwThreadDisplayDoBase
{
    public $user = null;
    public $tid;
    public $torrent;
    public function __construct($tid, PwUserBo $user) {
        $this->tid = $tid;
        $this->user = $user;
        $this->getData();
    }
    public function getData() {
        $torrent = $this->_getTorrentService()->getTorrentByTid($this->tid);
        $torrent['seeders'] = $torrent['seeders'] + 1;
        $torrent['size'] = $this->formatSize($torrent['size']);
        $torrent['info_hash'] = $this->formatHash($torrent['info_hash']);
        $torrent['list'] = $this->_getTorrentFileService()->getTorrentFileByTorrent($torrent['id']);
        if (isset($torrent['list'])) {
            foreach ($torrent['list'] as $key => $value) {
                $torrent['list'][$key]['size'] = $this->formatSize($value['size']);
            }
        }
        $peers = $this->_getTorrentPeerService()->getTorrentPeerByTorrent($torrent['id']);
        $seeder = $leecher = 0;
        foreach ($peers as $peer) {
            if ($peer['seeder'] == 'yes') {
                $seeder++;
            } else {
                $leecher++;
            }
        }
        $torrent['seeder'] = ($seeder==0)?'断种':$seeder;
        $torrent['leecher'] = $leecher;
        $this->torrent = $torrent;
    }
    public function createHtmlBeforeContent($read) {
        if ($read['pid'] == 0 && isset($this->torrent)) {
            PwHook::template('displayReadTorrentHtml', 'EXT:torrent.template.read_injector_before_torrent', true, $this->torrent);
        }
    }
    private function formatHash($hash) {
        return preg_replace_callback('/./s', create_function('$matches', 'return sprintf("%02x", ord($matches[0]));'), str_pad($hash, 20));
    }
    private function formatSize($bytes) {
        if ($bytes < 1000 * 1024) return number_format($bytes / 1024, 2) . " KB";
        elseif ($bytes < 1000 * 1048576) return number_format($bytes / 1048576, 2) . " MB";
        elseif ($bytes < 1000 * 1073741824) return number_format($bytes / 1073741824, 2) . " GB";
        elseif ($bytes < 1000 * 1099511627776) return number_format($bytes / 1099511627776, 3) . " TB";
        else return number_format($bytes / 1125899906842624, 3) . " PB";
    }
    private function _getTorrentService() {
        return Wekit::load('EXT:torrent.service.PwTorrent');
    }
    private function _getTorrentFileService() {
        return Wekit::load('EXT:torrent.service.PwTorrentFile');
    }
    private function _getTorrentPeerService() {
        return Wekit::load('EXT:torrent.service.PwTorrentPeer');
    }
}
