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

class PwCronDoClearTorrents extends AbstractCronBase
{
    
    public function delete_thread($topic) {
        $tid = $topic['tid'];
        $fid = $topic['fid'];
        $subject = $topic['subject'];
        $created_userid = $topic['created_userid'];
        
        Wind::import('SRV:forum.dm.PwTopicDm');
        Wind::import('SRV:recycle.dm.PwTopicRecycleDm');
        
        $dm = new PwTopicRecycleDm();
        $dm->setTid($tid)->setFid($fid)->setOperateTime(time())->setOperateUsername('system')->setReason('长期断种');
        var_dump(Wekit::load('recycle.PwTopicRecycle')->add($dm));
        
        $dm = new PwTopicDm($tid);
        $dm->setDisabled(2)->setTopped(0)->setDigest(0);
        Wekit::load('forum.PwThread')->updateThread($dm);
        
        $api = WindidApi::api('message');
        $api->send($created_userid, '您的种子 ' . $subject . ' 因长期断种已被系统自动移入回收站，如有异议请尽快联系管理员，管理员将根据相关规定决定恢复或彻底删除。', 1);
    }
    
    public function run($cronId) {
        $fids = range(12, 23); // An array of thread ids for PT torrent
        date_default_timezone_set('Asia/Shanghai');
        foreach ($fids as $fid) {
            $topics = Wekit::load('forum.PwThread')->getThreadByFid($fid, 0);
            foreach ($topics as $topic) {
                if ($topic['special'] != 'torrent') continue;
                $torrent = Wekit::load('EXT:torrent.service.dao.PwTorrentDao')->getTorrentByTid($topic['tid']);
                if (strtotime($torrent['last_action']) < strtotime('-10 day')) $this->delete_thread($topic);
            }
        }
    }
}
?>