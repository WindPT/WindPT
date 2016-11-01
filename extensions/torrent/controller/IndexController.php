<?php

defined('WEKIT_VERSION') || exit('Forbidden');

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

            Wind::import('EXT:torrent.service.srv.helper.PwUtils');

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
        $passkey    = $this->getInput('passkey');
        $infoHash   = $this->getInput('info_hash');
        $peerId     = $this->getInput('peer_id');
        $event      = $this->getInput('event');
        $port       = $this->getInput('port');
        $downloaded = $this->getInput('downloaded');
        $uploaded   = $this->getInput('uploaded');
        $left       = $this->getInput('left');
        $compact    = $this->getInput('compact');
        $no_peer_id = $this->getInput('no_peer_id');
        $agent      = $_SERVER['HTTP_USER_AGENT'];
        $ip         = Wind::getComponent('request')->getClientIp();
        $seeder     = ($left > 0) ? 0 : 1;

        Wind::import('EXT:torrent.service.srv.helper.PwAnnounce');

        // Check if a BitTorrent client
        $allowed = false;

        $allowedClients = $this->_getTorrentAgentService()->fetchTorrentAgent();

        if (is_array($allowedClients)) {
            foreach ($allowedClients as $allowedClient) {
                if (!preg_match('/' . $allowedClient['agent_pattern'] . '/', $agent)) {
                    continue;
                }

                if ($allowedClient['peer_id_pattern'] == '' || preg_match('/' . $allowedClient['peer_id_pattern'] . '/', $peerId)) {
                    $allowed = true;
                }

                break;
            }
        }

        if (!$allowed) {
            header('Location: ' . WindUrlHelper::createUrl('/'));
            PwAnnounce::showError('This a a bittorrent application and can\'t be loaded into a browser!');
        }

        header('Content-Type: text/plain; charset=utf-8');
        header('Pragma: no-cache');

        // Verify passkey
        $user = $this->_getTorrentUserService()->getTorrentUserByPasskey($passkey);
        if (!$user) {
            PwAnnounce::showError('Invalid passkey! Re-download the torrent file!');
        }

        // Check if user was banned
        $userBan = $this->_getUserBanService()->getBanInfo($user['uid']);
        if ($userBan) {
            PwAnnounce::showError('User was banned!');
        }

        // Get torrent information by infoHash
        $torrent = $this->_getTorrentService()->getTorrentByInfoHash($infoHash);
        if (!$torrent) {
            PwAnnounce::showError('Torrent not registered with this tracker!');
        }

        // Check if torrent was removed
        $topic = $this->_getThreadService()->getThread($torrent['tid']);
        if ($topic['disabled'] > 0 && !(in_array($user['groupid'], array(3, 4, 5)) || $topic['created_userid'] == $user['uid'])) {
            PwAnnounce::showError('Torrent removed!');
        }

        // Get this peer
        $self = $this->_getTorrentPeerService()->getTorrentPeerByTorrentIdAndUid($torrent['id'], $user['uid']);

        // Get peers list
        $peers = PwAnnounce::getPeersByTorrentId($torrent['id'], $self['peer_id']);

        Wind::import('EXT:torrent.service.dm.PwTorrentPeerDm');

        if (!empty($self)) {
            // Check if already started
            if ($ip != $self['ip']) {
                PwAnnounce::showError('You have already started downloading this torrent!');
            }

            $state = 'started';
            $dm    = new PwTorrentPeerDm($self['id']);
            switch ($event) {
                case '':
                case 'started':
                    $dm->setIp($ip)->setPort($port)->setUploaded($uploaded)->setDownloaded($downloaded)->setLeft($left)->setLastAction(Pw::time2str(Pw::getTime(), 'Y-m-d H:i:s'))->setSeeder($seeder)->setAgent($agent);
                    $this->_getTorrentPeerService()->updateTorrentPeer($dm);
                    break;
                case 'stopped':
                    $this->_getTorrentPeerService()->deleteTorrentPeer($self['id']);
                    $state = 'stopped';
                    break;
                case 'completed':
                    $dm->setFinishedAt(Pw::time2str(Pw::getTime(), 'Y-m-d H:i:s'))->setIp($ip)->setPort($port)->setUploaded($uploaded)->setDownloaded($downloaded)->setLeft($left)->setLastAction(Pw::time2str(Pw::getTime(), 'Y-m-d H:i:s'))->setSeeder($seeder)->setAgent($agent);
                    $this->_getTorrentPeerService()->updateTorrentPeer($dm);
                    break;
                default:
                    PwAnnounce::showError('Invalid event from client!');
            }
        } else {
            $sockres = @pfsockopen($ip, $port, $errno, $errstr, 5);
            if ($errno == '111') {
                $connectable = 0;
            } else {
                $connectable = 1;
            }
            @fclose($sockres);

            $dm = new PwTorrentPeerDm();
            $dm->setTorrentId($torrent['id'])->setUid($user['uid'])->setPeerId($peerId)->setIp($ip)->setPort($port)->setConnectable($connectable)->setUploaded($uploaded)->setDownloaded($downloaded)->setLeft($left)->setStartedAt(Pw::time2str(Pw::getTime(), 'Y-m-d H:i:s'))->setLastAction(Pw::time2str(Pw::getTime(), 'Y-m-d H:i:s'))->setSeeder($seeder)->setAgent($agent);
            $this->_getTorrentPeerService()->addTorrentPeer($dm);
            $self = $this->_getTorrentPeerService()->getTorrentPeerByTorrentIdAndUid($torrent['id'], $user['uid']);
        }

        // Update user's history about this torrent
        $history = $this->_getTorrentHistoryService()->getTorrentHistoryByTorrentIdAndUid($torrent['id'], $user['uid']);

        Wind::import('EXT:torrent.service.dm.PwTorrentHistoryDm');

        if (!$history) {
            $dm = new PwTorrentHistoryDm();
            $dm->setUid($user['uid'])->setTorrentId($torrent['id'])->setUploaded($uploaded)->setDownloaded($downloaded)->setLeft($left);
            $this->_getTorrentHistoryService()->addTorrentHistory($dm);
            if ($downloaded != 0) {
                $rotio = round($uploaded / $downloaded, 2);
            } else {
                $rotio = 1;
            }
        } else {
            $uploaded_add   = max(0, $uploaded - $history['uploaded_last']);
            $downloaded_add = max(0, $downloaded - $history['downloaded_last']);

            $uploaded_total   = $history['uploaded'] + $uploaded_add;
            $downloaded_total = $history['downloaded'] + $downloaded_add;

            if ($downloaded_total != 0) {
                $rotio = round($uploaded_total / $downloaded_total, 2);
            } else {
                $rotio = 1;
            }

            $dm = new PwTorrentHistoryDm($history['id']);
            $dm->setUid($user['uid'])->setTorrentId($torrent['id'])->setUploaded($uploaded_total)->setUploadedLast($uploaded)->setDownloaded($downloaded_total)->setDownloadedLast($downloaded)->setLeft($left)->setState($state);
            $this->_getTorrentHistoryService()->updateTorrentHistory($dm);
        }

        // Count Peers
        $torrent = PwAnnounce::updatePeerCount($torrent, $peers);

        // Update user's credits
        if ($seeder == 1) {
            $changed       = 0;
            $WindApi       = WindidApi::api('user');
            $crdtits       = $WindApi->getUserCredit($user['uid']);
            $user_torrents = count($this->_getTorrentService()->fetchTorrentByUid($user['uid']));
            $histories     = $this->_getTorrentHistoryService()->fetchTorrentHistoryByUid($user['uid']);

            if (is_array($histories)) {
                foreach ($histories as $history) {
                    $downloaded_total += $history['downloaded'];
                    $uploaded_total += $history['uploaded'];
                }
            }

            if ($downloaded_total != 0) {
                $rotio_total = round($uploaded_total / $downloaded_total, 2);
            } else {
                $rotio_total = 1;
            }

            $userpeers = $this->_getTorrentPeerService()->fetchTorrentPeerByUid($user['uid']);
            if (is_array($userpeers)) {
                foreach ($userpeers as $p) {
                    if ($p['seeder'] == 1) {
                        $seeding++;
                    } else {
                        $leeching++;
                    }
                }
            }

            $tAlive   = (time() - strtotime($torrent['created_at'])) / 86400;
            $timeUsed = time() - strtotime($self['started_at']);
            $timeLa   = time() - strtotime($self['last_action']);

            $m = Wekit::load('EXT:torrent.service.srv.helper.PwEvalmath');
            $m->e('alive            = ' . round($tAlive));
            $m->e('seeders          = ' . round($torrent['seeders']));
            $m->e('leechers         = ' . round($torrent['leechers']));
            $m->e('size             = ' . round($torrent['size']));
            $m->e('seeding          = ' . round($seeding));
            $m->e('leeching         = ' . round($leeching));
            $m->e('downloaded_add   = ' . round($downloaded_add));
            $m->e('downloaded_total = ' . round($downloaded_total));
            $m->e('uploaded_add     = ' . round($uploaded_add));
            $m->e('uploaded_total   = ' . round($uploaded_total));
            $m->e('rotio            = ' . round($rotio));
            $m->e('rotio_total      = ' . round($rotio_total));
            $m->e('time             = ' . round($timeUsed));
            $m->e('time_la          = ' . round($timeLa));
            $m->e('torrents         = ' . round($user_torrents));

            $_credits = Wekit::C('site', 'app.torrent.credits');

            if (is_array($_credits)) {
                foreach ($_credits as $key => $value) {
                    if ($value['enabled'] != '1') {
                        continue;
                    }

                    $m->e('credit = ' . round($crdtits['credit' . $key]));

                    $c = round($m->e($value['exp']));

                    if ($c >= 1) {
                        $changes[$key] = $c;
                        $changed++;
                    }
                }
            }

            if ($changed) {
                Wind::import('SRV:credit.bo.PwCreditBo');
                Wind::import('SRV:user.bo.PwUserBo');
                $creditBo = PwCreditBo::getInstance();
                $creditBo->addLog('app_torrent', $changes, new PwUserBo($user['uid']), array(
                    'torrent' => $topic['subject'],
                ));
                $creditBo->sets($user['uid'], $changes);
            }
        }

        // Update peer
        Wind::import('EXT:torrent.service.dm.PwTorrentDm');
        $dm = new PwTorrentDm($torrent['id']);
        $dm->setSeeders($torrent['seeders'])->setLeechers($torrent['leechers'])->setUpdatedAt(Pw::time2str(Pw::getTime(), 'Y-m-d H:i:s'));
        $this->_getTorrentService()->updateTorrent($dm);

        // Output peers list to client
        $peer_string = PwAnnounce::buildPeerList($torrent, $peers, $compact, $no_peer_id);

        exit($peer_string);
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
            Wind::import('EXT:torrent.service.srv.helper.PwPasskey');

            $uid     = $this->loginUser->uid;
            $passkey = PwPasskey::getPassKey($uid);
        }

        $userBan = $this->_getUserBanService()->getBanInfo($uid);
        if ($userBan) {
            $this->showError('ban');
        }

        $file = WEKIT_PATH . '../torrent/' . $id . '.torrent';
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

        Wind::import('EXT:torrent.service.srv.helper.PwAnnounce');
        $dictionary['value']['announce'] = $bencode->doDecode($bencode->doEncodeString(PwAnnounce::getTrackerUrl($passkey)));

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

    public function subscribeAction()
    {
        $id    = $this->getInput('id');
        $unsub = $this->getInput('unsub');

        if (!$this->loginUser->uid) {
            $this->showError('login.not');
        }

        $userBan = $this->_getUserBanService()->getBanInfo($this->loginUser->uid);
        if ($userBan) {
            $this->showError('ban');
        }

        $torrent = $this->_getTorrentService()->getTorrent($id);
        if (empty($torrent)) {
            $this->showError('data.error');
        }

        $torrent = $this->_getTorrentSubscribeService()->getTorrentSubscribeByUidAndTorrent($this->loginUser->uid, $id);
        if (!empty($torrent)) {
            if ($unsub == 'true') {
                $this->_getTorrentSubscribeService()->deleteTorrentSubscribe($torrent['id']);
                $this->showMessage('TAG:del.success');
            } else {
                $this->showError('BBS:like.fail.already.liked');
            }
        }

        Wind::import('EXT:torrent.service.dm.PwTorrentSubscribeDm');

        $dm = new PwTorrentSubscribeDm();
        $dm->setUid($this->loginUser->uid)->setTorrentId($id);
        $this->_getTorrentSubscribeService()->addTorrentSubscribe($dm);

        $this->showMessage('success');
    }

    public function myAction()
    {
        Wind::import('SRV:space.bo.PwSpaceModel');

        $spaceUid = $this->loginUser->uid;

        $space = new PwSpaceModel($spaceUid);

        if (!$space->space['uid']) {
            $this->showError('login.not');
        }

        $space->setTome($spaceUid, $this->loginUser->uid);
        $space->setVisitUid($this->loginUser->uid);

        $torrents = $this->_getTorrentSubscribeService()->getTorrentSubscribeByUid($this->loginUser->uid);

        $this->setTheme('space', $space->space['space_style']);

        $this->setOutput($space, 'space');
        $this->setOutput($torrents, 'torrents');
        $this->setOutput('my', 'src');

        // seo设置
        Wind::import('SRV:seo.bo.PwSeoBo');
        $seoBo = PwSeoBo::getInstance();
        $lang  = Wind::getComponent('i18n');
        $seoBo->setCustomSeo(
            $lang->getMessage('SEO:space.profile.run.title',
                array($space->spaceUser['username'], $space->space['space_name'])), '',
            $lang->getMessage('SEO:space.profile.run.description',
                array($space->spaceUser['username'])));
        Wekit::setV('seo', $seoBo);
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

        $torrents = $this->_getTorrentSubscribeService()->getTorrentSubscribeByUid($this->loginUser->uid);

        if (is_array($torrents)) {
            foreach ($torrents as $torrent) {
                if ($torrent['disabled'] > 0 && !(in_array($user['groupid'], array(3, 4, 5)) || $topic['created_userid'] == $user['uid'])) {
                    continue;
                }

                echo '<item>';
                echo '<title><![CDATA[' . $torrent['filename'] . ']]></title>';
                echo '<link><![CDATA[' . WindUrlHelper::createUrl('/bbs/read/run?tid=' . $torrent['tid']) . ']]></link>';
                echo '<pubDate>' . date('D, d M Y H:i:s O', $torrent['created_time']) . '</pubDate>';
                echo '<description><![CDATA[' . $torrent['subject'] . ']]></description>';
                echo '<enclosure type="application/x-bittorrent" length="' . $torrent['size'] . '" url="' . str_replace('&', '&amp;', WindUrlHelper::createUrl('/app/torrent/index/download?id=' . $torrent['torrent_id'] . '&passkey=' . $passkey)) . '" />';
                echo '<author><![CDATA[' . $torrent['created_username'] . ']]></author>';
                echo '<category domain="' . WindUrlHelper::createUrl('/bbs/thread/run?fid=' . $torrent['fid']) . '"><![CDATA[' . $torrent['name'] . ']]></category>';
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

    private function _getTorrentSubscribeService()
    {
        return Wekit::load('EXT:torrent.service.PwTorrentSubscribe');
    }
}
