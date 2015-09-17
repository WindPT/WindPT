<?php

defined('WEKIT_VERSION') || exit('Forbidden');

class PwThreadsDeleteDoTorrent
{
    public function appDo($id)
    {
        $torrentDs = Wekit::load('EXT:torrent.service.PwTorrent');
        $fileDs = Wekit::load('EXT:torrent.service.PwTorrentFile');

        $torrent = $torrentDs->getTorrentByTid($id);
        $files = $fileDs->getTorrentFileByTorrent($torrent['id']);
        foreach ($files as $file) {
            $fileDs->deleteTorrentFile($file['id']);
        }

        $torrentDs->deleteTorrent($torrent['id']);
        @unlink(WEKIT_PATH . '../torrent/' . $torrent['id'] . '.torrent');
    }
}
