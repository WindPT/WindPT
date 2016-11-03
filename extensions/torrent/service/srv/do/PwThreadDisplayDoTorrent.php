<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:forum.srv.threadDisplay.do.PwThreadDisplayDoBase');

class PwThreadDisplayDoTorrent extends PwThreadDisplayDoBase
{
    public $user = null;
    public $tid;
    public $torrent;

    public function __construct($tid, PwUserBo $user)
    {
        $this->tid  = $tid;
        $this->user = $user;
        $this->getData();
    }

    public function getData()
    {
        $torrent = $this->_getTorrentService()->getTorrentByTid($this->tid);

        Wind::import('EXT:torrent.service.srv.helper.PwUtils');

        $torrent['seeders']   = $torrent['seeders'] + 1;
        $torrent['size']      = PwUtils::readableDataTransfer($torrent['size']);
        $torrent['info_hash'] = PwUtils::readableHash($torrent['info_hash']);
        $torrent['list']      = $this->_getTorrentFileService()->getTorrentFileByTorrentId($torrent['id']);

        if (is_array($torrent['list'])) {
            foreach ($torrent['list'] as $key => $value) {
                $torrent['list'][$key]['size'] = PwUtils::readableDataTransfer($value['size']);
            }
        }

        $seeder = $leecher = 0;

        $peers = $this->_getTorrentPeerService()->getTorrentPeerByTorrentId($torrent['id']);

        if (is_array($peers)) {
            foreach ($peers as $peer) {
                if ($peer['seeder'] == 1) {
                    $seeder++;
                } else {
                    $leecher++;
                }
            }
        }

        $torrent['seeder']  = ($seeder == 0) ? '断种' : $seeder;
        $torrent['leecher'] = $leecher;

        $this->torrent = $torrent;
    }

    public function createHtmlBeforeContent($read)
    {
        if ($read['pid'] == 0 && isset($this->torrent)) {
            PwHook::template('displayReadTorrentHtml', 'EXT:torrent.template.read_injector_before_torrent', true, $this->torrent);
        }
    }

    private function _getTorrentService()
    {
        return Wekit::load('EXT:torrent.service.PwTorrent');
    }

    private function _getTorrentFileService()
    {
        return Wekit::load('EXT:torrent.service.PwTorrentFile');
    }

    private function _getTorrentPeerService()
    {
        return Wekit::load('EXT:torrent.service.PwTorrentPeer');
    }
}
