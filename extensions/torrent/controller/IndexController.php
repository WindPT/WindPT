<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('EXT:torrent.service.srv.helper.PwUtils');

class IndexController extends PwBaseController
{
    private $passkey;

    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);
    }

    public function run()
    {
        header('Location: ' . WindUrlHelper::createUrl('/'));

        exit();
    }

    public function getBindAction()
    {
        exit(json_encode(Wekit::C('site', 'app.torrent.typebind')));
    }

    public function updateInfoAction()
    {
        if (Wekit::C('site', 'app.torrent.titlegen.enabled') > 0) {
            $t_type           = $this->getInput('t_type', 'post');
            $w_type           = $this->getInput('w_type', 'post');
            $wikilink         = $this->getInput('wikilink', 'post');
            $paras_se         = $this->getInput('se', 'post');
            $paras_rip        = $this->getInput('rip', 'post');
            $paras_resolution = $this->getInput('resolution', 'post');
            $paras_sub        = $this->getInput('sub', 'post');
            $paras_format     = $this->getInput('format', 'post');
            $paras_state      = $this->getInput('state', 'post');
            $paras_bps        = $this->getInput('bps', 'post');
            $paras_platform   = $this->getInput('platform', 'post');
            $paras_name       = $this->getInput('name', 'post');
            $paras_oname      = $this->getInput('oname', 'post');
            $paras_lang       = $this->getInput('lang', 'post');
            $paras_publisher  = $this->getInput('publisher', 'post');

            switch ($t_type) {
                case '1':
                    // 书籍
                    $url = 'https://api.douban.com/v2/book/' . $wikilink;
                    if (!empty(Wekit::C('site', 'app.torrent.titlegen.douban'))) {
                        $url .= '?apikey=' . Wekit::C('site', 'app.torrent.titlegen.douban');
                    }

                    $result = json_decode(PwUtils::curl($url));
                    $title  = '[' . $result->pubdate . ']'; // 年份
                    $title .= '[' . $result->title . ']'; // 标题

                    // 子标题
                    if ($result->subtitle) {
                        $title .= '[' . $result->subtitle . ']';
                    }

                    // 作者
                    if ($result->author) {
                        $title .= '[';
                        $i = 0;
                        foreach ($result->author as $author) {
                            if ($i <= 2) {
                                $title .= $author . ' / ';
                                $i++;
                            }
                        }
                        $title = substr($title, 0, strlen($title) - 3);
                        $title .= ']';
                    }

                    $wikilink = $result->alt;
                    $content  = '[img]' . $result->image . '[/img]<br />' . $result->summary;
                    break;

                case '2':
                case '21':
                    // 影视
                    if ($w_type == 12) {
                        // 豆瓣
                        $url = 'https://api.douban.com/v2/movie/subject/' . $wikilink;

                        if (!empty(Wekit::C('site', 'app.torrent.titlegen.douban'))) {
                            $url .= '?apikey=' . Wekit::C('site', 'app.torrent.titlegen.douban');
                        }

                        $result = json_decode(PwUtils::curl($url));

                        $title = '[' . $result->countries[0] . ']'; // 国别
                        $title .= '[' . $result->year . ']'; // 年份
                        $title .= '[' . $result->title . ']'; // 影片中文名

                        // 又名
                        $title .= '[';
                        $i = 0;
                        foreach ($result->aka as $aka) {
                            if ($i <= 2 && $aka != $result->title) {
                                $title .= $aka . ' / ';
                                $i++;
                            }
                        }
                        $title = substr($title, 0, strlen($title) - 3);
                        $title .= ']';

                        // 季度、集数
                        if ($paras_se) {
                            $title .= '[' . $paras_se . ']';
                        }

                        // 类型
                        $title .= '[';
                        $i = 0;
                        foreach ($result->genres as $genre) {
                            if ($i <= 2) {
                                $title .= $genre . ' / ';
                                $i++;
                            }
                        }
                        $title = substr($title, 0, strlen($title) - 3);
                        $title .= ']';

                        $title .= '[' . $paras_rip . ']'; // 压制
                        $title .= '[' . $paras_resolution . ']'; // 分辨率
                        $title .= '[' . $paras_sub . ']'; // 字幕
                        $title .= '[' . $paras_format . ']'; // 格式

                        // 状态
                        if ($paras_state) {
                            $title .= '[' . $paras_state . ']';
                        }

                        $wikilink = $result->alt;
                        $content  = '[img]' . $result->images->large . '[/img]<br />' . $result->summary;
                    } elseif ($w_type == 2) {
                        // IMDB
                        $url    = 'http://omdbapi.com/?i=' . $wikilink;
                        $result = json_decode(PwUtils::curl($url));
                        $title  = '[' . $result->Country . ']'; // 国别
                        $title .= '[' . $result->Year . ']'; // 年份
                        $title .= '[' . $result->Title . ']'; // 影片名

                        // 季度、集数
                        if ($paras_se) {
                            $title .= '[' . $paras_se . ']';
                        }

                        // 类型
                        $title .= '[' . str_replace(', ', ' ', $result->Genre) . ']';

                        $title .= '[' . $paras_rip . ']'; // 压制
                        $title .= '[' . $paras_resolution . ']'; // 分辨率
                        $title .= '[' . $paras_sub . ']'; // 字幕
                        $title .= '[' . $paras_format . ']'; // 格式

                        // 状态
                        if ($paras_state) {
                            $title .= '[' . $paras_state . ']';
                        }

                        if (!empty(Wekit::C('site', 'app.torrent.titlegen.omdb'))) {
                            $content = '[img]' . 'http://img.omdbapi.com/?i=' . $wikilink . '&apikey=' . Wekit::C('site', 'app.torrent.titlegen.omdb') . '&h=640[/img]<br />' . $result->Plot;
                        } else {
                            $content = $result->Plot;
                        }

                        $wikilink = 'http://www.imdb.com/title/' . $wikilink;
                    }

                    break;

                case '3':
                    // 音乐
                    $url = 'https://api.douban.com/v2/music/' . $wikilink;
                    if (!empty(Wekit::C('site', 'app.torrent.titlegen.douban'))) {
                        $url .= '?apikey=' . Wekit::C('site', 'app.torrent.titlegen.douban');
                    }

                    $result = json_decode(PwUtils::curl($url));
                    $title  = '[' . $result->attrs->pubdate . ']'; // 年份
                    $title .= '[' . $result->attrs->title . ']'; // 标题
                    $title .= '[' . $result->attrs->singer . ']'; // 艺人
                    $title .= '[' . $paras_format . ']'; // 格式
                    $title .= '[' . $paras_bps . ']'; // 码率

                    $wikilink = $result->alt;
                    $content  = '[img]' . $result->image . '[/img]<br />' . $result->summary;
                    break;

                case '4':
                    // 软件
                    $title = $paras_publisher ? '[' . $paras_publisher . ']' : '';
                    $title .= '[' . $paras_platform . ']'; // 平台
                    $title .= '[' . $paras_name . ']'; // 中文名

                    // 原名
                    if ($paras_oname) {
                        $title .= '[' . $paras_oname . ']';
                    }
                    $title .= '[' . $paras_lang . ']'; // 语言
                    $title .= '[' . $paras_format . ']'; // 格式
                    break;

                case '5':
                    // 其他
                    $title = $paras_publisher ? '[' . $paras_publisher . ']' : '';
                    $title .= '[' . $paras_name . ']'; // 中文名

                    // 原名
                    if ($paras_oname) {
                        $title .= '[' . $paras_oname . ']';
                    }
                    $title .= '[' . $paras_lang . ']'; // 语言
                    $title .= '[' . $paras_format . ']'; // 格式
                    break;
            }

            echo json_encode(array('title' => $title, 'wikilink' => $wikilink, 'content' => $content));
        }

        exit();
    }

    public function announceAction()
    {
        exit('d14:failure reason42:Bundled announcement processor deprecated.e');
    }

    public function downloadAction()
    {
        $id      = $this->getInput('id');
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
            $uid     = $this->loginUser->uid;
            $passkey = PwUtils::getPassKey($uid);
        }

        $userBan = $this->_getUserBanService()->getBanInfo($uid);
        if ($userBan) {
            $this->showError('ban');
        }

        $file = WEKIT_PATH . '../torrents/' . $id . '.torrent';
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
        $bencode    = Wekit::load('EXT:torrent.service.srv.helper.PwBencode');
        $dictionary = $bencode->doDecodeFile($file);

        $dictionary['value']['announce'] = $bencode->doDecode($bencode->doEncodeString(PwUtils::getTrackerUrl($passkey)));

        // Generate file name
        $torrentnameprefix = Wekit::C('site', 'app.torrent.torrentnameprefix');
        if ($torrentnameprefix == '') {
            $torrentnameprefix = Wekit::C('site', 'info.name');
        }

        $filename = rawurlencode('[' . $torrentnameprefix . '][' . $torrent['save_as'] . ']');

        // Send torrent file to broswer
        header('Content-Description: File Transfer');
        header('Content-type: application/octet-streamn');
        header('Content-Disposition: attachment; charset=utf-8; filename="' . $filename . '.torrent"; filename*=UTF-8\'\'' . $filename . '.torrent');
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
        echo '<link>' . Wekit::C('site', 'info.url') . '</link>';
        echo '<description>' . Wekit::C('site', 'info.name') . ' Powered by WindPT</description>';
        echo '<language>zh-cn</language>';
        echo '<copyright>Copyright (c) ' . Wekit::C('site', 'info.name') . ' ' . date('Y') . ', all rights reserved</copyright>';
        echo '<pubDate>' . date('D, d M Y H:i:s O') . '</pubDate>';
        echo '<generator>WindPT RSS Generator</generator>';
        echo '<ttl>60</ttl>';

        $tagLists = $this->_getBuildLikeService()->getTagsByUid($user['uid']);
        if ($tagid > 0) {
            $logids   = $this->_getBuildLikeService()->getLogidsByTagid($tagid, 0, false);
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

                $forum   = $this->_getForumService()->getForum($topic['fid']);
                $torrent = $this->_getTorrentService()->getTorrentByTid($topic['tid']);

                echo '<item>';
                echo '<title><![CDATA[' . $torrent['save_as'] . ']]></title>';
                echo '<link><![CDATA[' . WindUrlHelper::createUrl('/bbs/read/run?tid=' . $topic['tid']) . ']]></link>';
                echo '<pubDate>' . date('D, d M Y H:i:s O', $topic['created_time']) . '</pubDate>';
                echo '<description><![CDATA[' . $topic['subject'] . ']]></description>';
                echo '<enclosure type="application/x-bittorrent" length="' . $torrent['size'] . '" url="' . str_replace('&', '&amp;', WindUrlHelper::createUrl('/app/torrent/index/download?id=' . $torrent['id'] . '&passkey=' . $passkey)) . '" />';
                echo '<author><![CDATA[' . $topic['created_username'] . ']]></author>';
                echo '<category domain="' . WindUrlHelper::createUrl('/bbs/thread/run?fid=' . $topic['fid']) . '"><![CDATA[' . $forum['name'] . ']]></category>';
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

    private function _getTorrentPeerService()
    {
        return Wekit::load('EXT:torrent.service.PwTorrentPeer');
    }

    private function _getTorrentUserService()
    {
        return Wekit::load('EXT:torrent.service.PwTorrentUser');
    }

    private function _getTorrentAgentService()
    {
        return Wekit::load('EXT:torrent.service.PwTorrentAgent');
    }

    private function _getTorrentHistoryService()
    {
        return Wekit::load('EXT:torrent.service.PwTorrentHistory');
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
