<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:cron.srv.base.AbstractCronBase');
Wind::import('SRV:forum.dm.PwTopicDm');
Wind::import('SRV:recycle.dm.PwTopicRecycleDm');

class PwCronDoClearTorrents extends AbstractCronBase
{
    private function deleteThread($topic)
    {
        $tid = $topic['tid'];
        $fid = $topic['fid'];
        $subject = $topic['subject'];
        $created_userid = $topic['created_userid'];

        $dm = new PwTopicRecycleDm();
        $dm->setTid($tid)->setFid($fid)->setOperateTime(time())->setOperateUsername('system')->setReason('长期断种');
        Wekit::load('recycle.PwTopicRecycle')->add($dm);

        $dm = new PwTopicDm($tid);
        $dm->setDisabled(2)->setTopped(0)->setDigest(0);
        Wekit::load('forum.PwThread')->updateThread($dm);

        $api = WindidApi::api('message');
        $api->send($created_userid, '您的种子 '.$subject.' 因长期断种已被系统自动移入回收站，如有异议请尽快联系管理员，管理员将根据相关规定决定恢复或彻底删除。', 1);
    }

    public function run($cronId)
    {
        $torrentimeout = Wekit::C('site', 'app.torrent.cron.torrentimeout');

        if ($torrentimeout < 1) {
            return;
        }

        $torrents = Wekit::load('EXT:torrent.service.PwTorrent')->fetchTorrent();

        if (!is_array($torrents)) {
            return;
        }

        foreach ($torrents as $torrent) {
            $topic = Wekit::load('forum.PwThread')->getThread($torrent['tid']);

            if (is_array($topic) && $topic['disabled'] > 0) {
                continue;
            }

            if (strtotime($torrent['updated_at']) < strtotime('-'.$torrentimeout.' day')) {
                $this->deleteThread($topic);
            }
        }
    }
}
