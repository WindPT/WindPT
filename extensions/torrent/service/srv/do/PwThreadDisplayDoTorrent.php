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
        $this->tid = $tid;
        $this->user = $user;
        $this->getData();
    }

    public function getData()
    {
        $torrent = $this->_getTorrentService()->getTorrentByTid($this->tid);

        Wind::import('EXT:torrent.service.srv.helper.PwUtils');

        $torrent['info_hash'] = PwUtils::readableHash($torrent['info_hash']);
        $torrent['files'] = $this->_getTorrentFileService()->getTorrentFileByTorrentId($torrent['id']);
        $torrent['finished'] = $this->_getTorrentHistoryService()->fetchTorrentHistoryByTorrentId($torrent['id']);
        $torrent['finished'] = array_filter($torrent['finished'], function ($var) {
            return $var['left'] == 0;
        });

        if (is_array($torrent['files'])) {
            foreach ($torrent['files'] as $key => $value) {
                $torrent['files'][$key]['size'] = PwUtils::readableDataTransfer($value['size']);
            }
        }

        $seeder = $leecher = 0;

        $torrent['peers'] = $this->_getTorrentPeerService()->fetchTorrentPeerByTorrentId($torrent['id']);

        if (is_array($torrent['peers'])) {
            foreach ($torrent['peers'] as &$peer) {
                if ($peer['seeder'] == 1) {
                    $seeder++;
                } else {
                    $leecher++;
                }
                if ($peer['connectable'] == 1) {
                    if ($peer['left'] > 0) {
                        $peer['color'] = 'navy';
                    } else {
                        $peer['color'] = 'green';
                    }
                } else {
                    $peer['color'] = 'red';
                }

                $peer['uploaded'] = PwUtils::readableDataTransfer($peer['uploaded']);
                $peer['downloaded'] = PwUtils::readableDataTransfer($peer['downloaded']);
                $peer['percent'] = ($torrent['size'] - $peer['left']) / $torrent['size'] * 100 .'%';
            }
        }

        $torrent['size'] = PwUtils::readableDataTransfer($torrent['size']);
        $torrent['seeder'] = ($seeder == 0) ? '断种' : $seeder;
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

    private function _getTorrentHistoryService()
    {
        return Wekit::load('EXT:torrent.service.PwTorrentHistory');
    }

    private function _getTorrentPeerService()
    {
        return Wekit::load('EXT:torrent.service.PwTorrentPeer');
    }
}
