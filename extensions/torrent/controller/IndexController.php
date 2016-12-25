<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('EXT:torrent.service.srv.helper.PwUtils');

class IndexController extends PwBaseController
{
    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);
    }

    public function run()
    {
        header('Location: '.WindUrlHelper::createUrl('/'));

        exit();
    }

    public function announceAction()
    {
        exit('d14:failure reason42:Bundled announcement processor deprecated.e');
    }

    public function downloadAction()
    {
        $id = $this->getInput('id');
        $passkey = $this->getInput('passkey');

        if (!$this->loginUser->uid && empty($passkey)) {
            $this->showError('download.fail.login.not');
        } elseif (is_string($passkey)) {
            $user = $this->_getTorrentUserService()->getTorrentUserByPasskey($passkey);
            if (empty($user)) {
                $this->showError('download.fail.login.not');
            } else {
                $uid = $user['uid'];
            }
        } else {
            $uid = $this->loginUser->uid;
            $passkey = PwUtils::getPassKey($uid);
        }

        $userBan = $this->_getUserBanService()->getBanInfo($uid);
        if ($userBan) {
            $this->showError('ban');
        }

        $file = WEKIT_PATH.'../torrents/'.$id.'.torrent';
        if (!file_exists($file)) {
            $this->showError('data.error');
        }

        $torrent = $this->_getTorrentService()->getTorrent($id);

        // Check if torrent was removed
        $topic = $this->_getThreadService()->getThread($torrent['tid']);
        if ($topic['disabled'] > 0 && !(in_array($user['groupid'], array(3, 4, 5)) || $topic['created_userid'] == $user['uid'])) {
            $this->showError('BBS:forum.thread.disabled');
        }

        // Change announce to user's private announce
        $bencode = Wekit::load('EXT:torrent.service.srv.helper.PwBencode');
        $dictionary = $bencode->doDecodeFile($file);

        $dictionary['value']['announce'] = $bencode->doDecode($bencode->doEncodeString(PwUtils::getTrackerUrl($passkey)));

        // Generate file name
        $torrentnameprefix = Wekit::C('site', 'app.torrent.torrentnameprefix');
        if ($torrentnameprefix == '') {
            $torrentnameprefix = Wekit::C('site', 'info.name');
        }

        $filename = rawurlencode('['.$torrentnameprefix.']['.$torrent['save_as'].']');

        // Send torrent file to broswer
        header('Content-Description: File Transfer');
        header('Content-type: application/octet-streamn');
        header('Content-Disposition: attachment; charset=utf-8; filename="'.$filename.'.torrent"; filename*=UTF-8\'\''.$filename.'.torrent');
        header('Content-Transfer-Encoding: binary');

        exit($bencode->doEncode($dictionary));
    }

    public function rssAction()
    {
        $passkey = $this->getInput('passkey');

        $user = $this->_getTorrentUserService()->getTorrentUserByPasskey($passkey);
        if (empty($user)) {
            $this->showError('login.not');
        }

        $userBan = $this->_getUserBanService()->getBanInfo($user['uid']);
        if ($userBan) {
            $this->showError('ban');
        }

        header('Content-Type: application/xml; charset=utf-8');

        echo '<rss version="2.0">';
        echo '<channel>';
        echo '<title>WindPT Torrents</title>';
        echo '<link>'.Wekit::C('site', 'info.url').'</link>';
        echo '<description>'.Wekit::C('site', 'info.name').' Powered by WindPT</description>';
        echo '<language>zh-cn</language>';
        echo '<copyright>Copyright (c) '.Wekit::C('site', 'info.name').' '.date('Y').', all rights reserved</copyright>';
        echo '<pubDate>'.date('D, d M Y H:i:s O').'</pubDate>';
        echo '<generator>WindPT RSS Generator</generator>';
        echo '<ttl>60</ttl>';

        $tagLists = $this->_getBuildLikeService()->getTagsByUid($user['uid']);
        if ($tagid > 0) {
            $logids = $this->_getBuildLikeService()->getLogidsByTagid($tagid, 0, false);
            $logLists = $this->_getBuildLikeService()->getLogLists($logids);
        } else {
            $logLists = $this->_getBuildLikeService()->getLogList($user['uid'], 0, false);
        }

        if (is_array($logLists)) {
            foreach ($logLists as $likeLog) {
                $likeContent = $this->_getLikeContentService()->getLikeContent($likeLog['likeid']);

                $topic = $this->_getThreadService()->getThread($likeContent['fromid']);

                if ($topic['special'] != 'torrent') {
                    continue;
                }

                if ($topic['disabled'] > 0 && !(in_array($user['groupid'], array(3, 4, 5)) || $topic['created_userid'] == $user['uid'])) {
                    continue;
                }

                $forum = $this->_getForumService()->getForum($topic['fid']);
                $torrent = $this->_getTorrentService()->getTorrentByTid($topic['tid']);

                echo '<item>';
                echo '<title><![CDATA['.$torrent['save_as'].']]></title>';
                echo '<link><![CDATA['.WindUrlHelper::createUrl('/bbs/read/run?tid='.$topic['tid']).']]></link>';
                echo '<pubDate>'.date('D, d M Y H:i:s O', $topic['created_time']).'</pubDate>';
                echo '<description><![CDATA['.$topic['subject'].']]></description>';
                echo '<enclosure type="application/x-bittorrent" length="'.$torrent['size'].'" url="'.str_replace('&', '&amp;', WindUrlHelper::createUrl('/app/torrent/index/download?id='.$torrent['id'].'&passkey='.$passkey)).'" />';
                echo '<author><![CDATA['.$topic['created_username'].']]></author>';
                echo '<category domain="'.WindUrlHelper::createUrl('/bbs/thread/run?fid='.$topic['fid']).'"><![CDATA['.$forum['name'].']]></category>';
                echo '</item>';
            }
        }

        echo '</channel>';
        echo '</rss>';

        exit();
    }

    private function _getUserBanService()
    {
        return Wekit::load('user.PwUserBan');
    }

    private function _getForumService()
    {
        return Wekit::load('forum.PwForum');
    }

    private function _getThreadService()
    {
        return Wekit::load('forum.PwThread');
    }

    private function _getTorrentService()
    {
        return Wekit::load('EXT:torrent.service.PwTorrent');
    }

    private function _getTorrentUserService()
    {
        return Wekit::load('EXT:torrent.service.PwTorrentUser');
    }

    private function _getBuildLikeService()
    {
        return Wekit::load('like.srv.PwBuildLikeService');
    }

    private function _getLikeContentService()
    {
        return Wekit::load('like.PwLikeContent');
    }
}
