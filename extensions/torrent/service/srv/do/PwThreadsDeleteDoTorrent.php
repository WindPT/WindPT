<?php
defined('WEKIT_VERSION') or exit(403);

/**
 * 删除一个帖子时，调用
 *
 * @author 7IN0SAN9 <me@7in0.me>
 * @copyright http://7in0.me
 * @license http://7in0.me
 */
class PwThreadsDeleteDoTorrent
{
    
    /**
     * @param int $id 帖子tid
     * @return void
     */
    public function appDo($id) {
        $torrentDao = Wekit::load('EXT:torrent.service.dao.PwTorrentDao');
        $fileDao = Wekit::load('EXT:torrent.service.dao.PwTorrentFileDao');

        $torrent = $torrentDao->fetchTorrentByTid($id);
        $files = $fileDao->getTorrentFileByTorrent($torrent['id']);
        foreach ($files as $file)
            $fileDao->deleteTorrentFile($file['id']);
        $torrentDa->deleteTorrent($torrent['id']);
        @unlink('../../../../../torrent/' . $torrent['id'] . '.torrent');
    }
}
?>