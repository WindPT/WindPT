<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwCronDoClearOnline.php 18771 2012-09-27 07:47:26Z gao.wanggao $ 
 * @package 
 */
Wind::import('SRV:cron.srv.base.AbstractCronBase');

class PwCronDoClearPeers extends AbstractCronBase{
	
	public function run($cronId) {
        $fids = range(12, 23); // An array of thread ids for PT torrent
        date_default_timezone_set('Asia/Shanghai');
        foreach ($fids as $fid) {
            $topics = Wekit::load('forum.PwThread')->getThreadByFid($fid, 0);
            foreach ($topics as $topic) {
                if ($topic['special'] != 'torrent') continue;
                $torrent = Wekit::load('EXT:torrent.service.dao.PwTorrentDao')->getTorrentByTid($topic['tid']);
                $peers = Wekit::load('EXT:torrent.service.PwTorrentPeer')->getTorrentPeerByTorrent($torrent['id']);
                foreach ($peers as $peer) 
                    if (strtotime($peer['last_action']) < strtotime('-30 minute')) Wekit::load('EXT:torrent.service.PwTorrentPeer')->deleteTorrentPeer($peer['id']);
            }
        }
	}
}
?>