<?php
defined('WEKIT_VERSION') or exit(403);

class PwThreadsDeleteDoTorrent
{
    
    /**
     * @param int $id 帖子tid
     * @return void
     */
    public function appDo($id) {
        $torrentDao = Wekit::load('EXT:torrent.service.dao.PwTorrentDao');
        $fileDao = Wekit::load('EXT:torrent.service.dao.PwTorrentFileDao');

        $torrent = $torrentDao->getTorrentByTid($id);
        $files = $fileDao->getTorrentFileByTorrent($torrent['id']);
        foreach ($files as $file)
            $fileDao->deleteTorrentFile($file['id']);
        $torrentDao->deleteTorrent($torrent['id']);
        @unlink(WEKIT_PATH . '../torrent/' . $torrent['id'] . '.torrent');
    }
}
?>