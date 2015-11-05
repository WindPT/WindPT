<?php

defined('WEKIT_VERSION') || exit('Forbidden');

class IndexController extends PwBaseController
{
    private $passkey;

    public function run()
    {
        header('Location: ' . WindUrlHelper::createUrl('/'));

        exit();
    }

    public function updateInfoAction()
    {
        if (Wekit::C('site', 'app.torrent.titlegen.ifopen') > 0) {
            $t_type = $this->getInput('t_type', 'post');
            $w_type = $this->getInput('w_type', 'post');
            $wikilink = $this->getInput('wikilink', 'post');
            $paras_se = $this->getInput('se', 'post');
            $paras_rip = $this->getInput('rip', 'post');
            $paras_resolution = $this->getInput('resolution', 'post');
            $paras_sub = $this->getInput('sub', 'post');
            $paras_format = $this->getInput('format', 'post');
            $paras_status = $this->getInput('status', 'post');
            $paras_bps = $this->getInput('bps', 'post');
            $paras_platform = $this->getInput('platform', 'post');
            $paras_name = $this->getInput('name', 'post');
            $paras_oname = $this->getInput('oname', 'post');
            $paras_lang = $this->getInput('lang', 'post');

            if (!$this->loginUser->uid) {
                $this->showError('必须登录才能进行本操作！');
            }

            // Check if user was banned
            $userBan = Wekit::load('SRV:user.PwUserBan')->getBanInfo($this->loginUser->uid);
            if ($userBan) {
                $this->showError('用户处于封禁期！');
            }

            Wind::import('EXT:torrent.service.srv.helper.PwUtils');

            switch ($t_type) {
                case '1':
                    // 书籍
                    $url = 'https://api.douban.com/v2/book/' . $wikilink;
                    if (!empty(Wekit::C('site', 'app.torrent.titlegen.douban'))) {
                        $url .= '?apikey=' . Wekit::C('site', 'app.torrent.titlegen.douban');
                    }

                    $result = json_decode(PwUtils::curl($url));
                    $title = '[' . $result->pubdate . ']';     // 年份
                    $title .= '[' . $result->title . ']';     // 标题

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
                    $content = '[img]' . $result->image . '[/img]<br />' . $result->summary;
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

                        $title = '[' . $result->countries[0] . ']';     // 国别
                        $title .= '[' . $result->year . ']';     // 年份
                        $title .= '[' . $result->title . ']';     // 影片中文名

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

                        $title .= '[' . $paras_rip . ']';     // 压制
                        $title .= '[' . $paras_resolution . ']';     // 分辨率
                        $title .= '[' . $paras_sub . ']';     // 字幕
                        $title .= '[' . $paras_format . ']';     // 格式

                        // 状态
                        if ($paras_status) {
                            $title .= '[' . $paras_status . ']';
                        }

                        $wikilink = $result->alt;
                        $content = '[img]' . $result->images->large . '[/img]<br />' . $result->summary;
                    } elseif ($w_type == 2) {
                        // IMDB
                        $url = 'http://omdbapi.com/?i=' . $wikilink;
                        $result = json_decode(PwUtils::curl($url));
                        $title = '[' . $result->Country . ']';     // 国别
                        $title .= '[' . $result->Year . ']';     // 年份
                        $title .= '[' . $result->Title . ']';     // 影片名

                        // 季度、集数
                        if ($paras_se) {
                            $title .= '[' . $paras_se . ']';
                        }

                        // 类型
                        $title .= '[' . str_replace(', ', ' ', $result->Genre) . ']';

                        $title .= '[' . $paras_rip . ']';     // 压制
                        $title .= '[' . $paras_resolution . ']';     // 分辨率
                        $title .= '[' . $paras_sub . ']';     // 字幕
                        $title .= '[' . $paras_format . ']';     // 格式

                        // 状态
                        if ($paras_status) {
                            $title .= '[' . $paras_status . ']';
                        }

                        $wikilink = 'http://www.imdb.com/title/' . $wikilink;
                        $content = '[img]' . $result->Poster . '[/img]<br />' . $result->Plot;
                    }

                    break;

                case '3':
                    // 音乐
                    $url = 'https://api.douban.com/v2/music/' . $wikilink;
                    if (!empty(Wekit::C('site', 'app.torrent.titlegen.douban'))) {
                        $url .= '?apikey=' . Wekit::C('site', 'app.torrent.titlegen.douban');
                    }

                    $result = json_decode(PwUtils::curl($url));
                    $title = '[' . $result->attrs->pubdate . ']';     // 年份
                    $title .= '[' . $result->attrs->title . ']';     // 标题
                    $title .= '[' . $result->attrs->singer . ']';     // 艺人
                    $title .= '[' . $paras_format . ']';     // 格式
                    $title .= '[' . $paras_bps . ']';     // 码率

                    $wikilink = $result->alt;
                    $content = '[img]' . $result->image . '[/img]<br />' . $result->summary;
                    break;

                case '4':
                    // 软件
                    $title = '[' . $paras_platform . ']';     // 平台
                    $title .= '[' . $paras_name . ']';     // 中文名

                    // 原名
                    if ($paras_oname) {
                        $title .= '[' . $paras_oname . ']';
                    }
                    $title .= '[' . $paras_lang . ']';     // 语言
                    $title .= '[' . $paras_format . ']';     // 格式
                    break;

                case '5':
                    // 其他
                    $title .= '[' . $paras_name . ']';     // 中文名

                    // 原名
                    if ($paras_oname) {
                        $title .= '[' . $paras_oname . ']';
                    }
                    $title .= '[' . $paras_lang . ']';     // 语言
                    $title .= '[' . $paras_format . ']';     // 格式
                    break;
            }

            echo json_encode(array('title' => $title, 'wikilink' => $wikilink, 'content' => $content));
        }

        exit();
    }

    public function announceAction()
    {
        $passkey = $this->getInput('passkey');
        $infoHash = $this->getInput('info_hash');
        $peerId = $this->getInput('peer_id');
        $event = $this->getInput('event');
        $port = $this->getInput('port');
        $downloaded = $this->getInput('downloaded');
        $uploaded = $this->getInput('uploaded');
        $left = $this->getInput('left');
        $compact = $this->getInput('compact');
        $no_peer_id = $this->getInput('no_peer_id');
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $ip = Wind::getComponent('request')->getClientIp();
        $status = ($left > 0) ? 'do' : 'done';
        $seeder = ($left > 0) ? 'no' : 'yes';

        Wind::import('EXT:torrent.service.srv.helper.PwAnnounce');

        // Check if a BitTorrent client
        $allowed = false;

        $allowedClients = $this->_getTorrentAgentDS()->fetchTorrentAgent();

        if (is_array($allowedClients)) {
            foreach ($allowedClients as $allowedClient) {
                if (!preg_match($allowedClient['agent_pattern'], $agent)) {
                    continue;
                }

                if ($allowedClient['peer_id_pattern'] == '' || preg_match($allowedClient['peer_id_pattern'], $peerId)) {
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
        $user = $this->_getTorrentUserDS()->getTorrentUserByPasskey($passkey);
        if (!$user) {
            PwAnnounce::showError('Invalid passkey! Re-download the torrent file!');
        }

        // Check if user was banned
        $userBan = Wekit::load('SRV:user.PwUserBan')->getBanInfo($user['uid']);
        if ($userBan) {
            PwAnnounce::showError('User was banned!');
        }

        // Get torrent information by infoHash
        $torrent = $this->_getTorrentDS()->getTorrentByInfoHash($infoHash);
        if (!$torrent) {
            PwAnnounce::showError('Torrent not registered with this tracker!');
        }

        // Check if torrent was removed
        $topic = Wekit::load('forum.PwThread')->getThread($torrent['tid']);
        if ($topic['disabled'] > 0 && !in_array($user['groupid'], array(3, 4, 5))) {
            PwAnnounce::showError('Torrent removed!');
        }

        // Get peers list
        $peers = PwAnnounce::getPeersByTorrentId($torrent['id'], $self['peer_id']);

        // Get this peer
        $self = $this->_getTorrentPeerDS()->getTorrentPeerByTorrentAndUid($torrent['id'], $user['uid']);

        Wind::import('EXT:torrent.service.dm.PwTorrentPeerDm');

        if (!empty($self)) {
            // Check if already started
            if ($ip != $self['ip']) {
                PwAnnounce::showError('You have already started downloading this torrent!');
            }

            $dm = new PwTorrentPeerDm($self['id']);
            switch ($event) {
                case '':
                case 'started':
                    $dm->setIp($ip)->setPort($port)->setUploaded($uploaded)->setDownloaded($downloaded)->setToGo($left)->setPrevAction(Pw::time2str(Pw::getTime(), 'Y-m-d H:i:s'))->setLastAction(Pw::time2str(Pw::getTime(), 'Y-m-d H:i:s'))->setSeeder($seeder)->setAgent($agent);
                    $this->_getTorrentPeerDS()->updateTorrentPeer($dm);
                    break;
                case 'stopped':
                    $this->_getTorrentPeerDS()->deleteTorrentPeer($self['id']);
                    $status = 'stop';
                    break;
                case 'completed':
                    $dm->setFinishedAt(Pw::getTime())->setIp($ip)->setPort($port)->setUploaded($uploaded)->setDownloaded($downloaded)->setToGo($left)->setPrevAction(Pw::time2str(Pw::getTime(), 'Y-m-d H:i:s'))->setLastAction(Pw::time2str(Pw::getTime(), 'Y-m-d H:i:s'))->setSeeder($seeder)->setAgent($agent);
                    $this->_getTorrentPeerDS()->updateTorrentPeer($dm);
                    $status = 'done';
                    break;
                default:
                    PwAnnounce::showError('Invalid event from client!');
            }
        } else {
            $sockres = @pfsockopen($ip, $port, $errno, $errstr, 5);
            if ($errno == '111') {
                $connectable = 'no';
            } else {
                $connectable = 'yes';
            }
            @fclose($sockres);

            $dm = new PwTorrentPeerDm();
            $dm->setTorrent($torrent['id'])->setUserid($user['uid'])->setPeerId($peerId)->setIp($ip)->setPort($port)->setConnectable($connectable)->setUploaded($uploaded)->setDownloaded($downloaded)->setToGo($left)->setStarted(Pw::time2str(Pw::getTime(), 'Y-m-d H:i:s'))->setLastAction(Pw::time2str(Pw::getTime(), 'Y-m-d H:i:s'))->setSeeder($seeder)->setAgent($agent)->setPasskey($passkey);
            $this->_getTorrentPeerDS()->addTorrentPeer($dm);
            $self = $this->_getTorrentPeerDS()->getTorrentPeerByTorrentAndUid($torrent['id'], $user['uid']);
        }

        // Update user's history about this torrent
        $history = $this->_getTorrentHistoryDs()->getTorrentHistoryByTorrentAndUid($torrent['id'], $user['uid']);

        Wind::import('EXT:torrent.service.dm.PwTorrentHistoryDm');

        if (!$history) {
            $dm = new PwTorrentHistoryDm();
            $dm->setUid($user['uid'])->setTorrent($torrent['id'])->setUploaded($uploaded)->setDownloaded($downloaded);
            $this->_getTorrentHistoryDs()->addTorrentHistory($dm);
            if ($downloaded != 0) {
                $rotio = round($uploaded / $downloaded, 2);
            } else {
                $rotio = 1;
            }
        } else {
            $uploaded_add = max(0, $uploaded - $history['uploaded_last']);
            $downloaded_add = max(0, $downloaded - $history['downloaded_last']);

            $uploaded_total = $history['uploaded'] + $uploaded_add;
            $downloaded_total = $history['downloaded'] + $downloaded_add;

            if ($downloaded_total != 0) {
                $rotio = round($uploaded_total / $downloaded_total, 2);
            } else {
                $rotio = 1;
            }

            $dm = new PwTorrentHistoryDm($history['id']);
            $dm->setUid($user['uid'])->setTorrent($torrent['id'])->setUploaded($uploaded_total)->setUploadedLast($uploaded)->setDownloaded($downloaded_total)->setDownloadedLast($downloaded)->setStatus($status);
            $this->_getTorrentHistoryDs()->updateTorrentHistory($dm);
            $uploaded = $uploaded_add;
            $downloaded = $downloaded_add;
        }

        // Update user's credits
        if (Wekit::C('site', 'app.torrent.creditifopen') == 1) {
            $changed = 0;
            $WindApi = WindidApi::api('user');
            $pwUser = Wekit::load('user.PwUser');
            $crdtits = $WindApi->getUserCredit($user['uid']);
            $user_torrents = count($this->_getTorrentDS()->fetchTorrentByUid($user['uid']));
            $histories = $this->_getTorrentHistoryDs()->fetchTorrentHistoryByUid($user['uid']);

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

            $timeUsed = time() - strtotime($self['started']);

            $m = Wekit::load('EXT:torrent.service.srv.helper.PwEvalmath');
            $m->e('downloaded       = ' . intval($downloaded));
            $m->e('downloaded_total = ' . intval($downloaded_total));
            $m->e('uploaded         = ' . intval($uploaded));
            $m->e('uploaded_total   = ' . intval($uploaded_total));
            $m->e('rotio            = ' . intval($rotio));
            $m->e('rotio_total      = ' . intval($rotio_total));
            $m->e('time             = ' . intval($timeUsed));
            $m->e('torrents         = ' . intval($user_torrents));

            $_credits = Wekit::C('site', 'app.torrent.credits');

            if (is_array($_credits)) {
                foreach ($_credits as $key => $value) {
                    if ($value['enabled'] != '1') {
                        continue;
                    }

                    $m->e('credit = ' . intval($crdtits['credit' . $key]));

                    $changes[$key] = intval($m->e($exp));
                    $changed++;
                }
            }

            if ($changed) {
                Wind::import('SRV:credit.bo.PwCreditBo');
                Wind::import('SRV:user.bo.PwUserBo');
                $creditBo = PwCreditBo::getInstance();
                $creditBo->sets($user['uid'], $changes);
                $creditBo->addLog('pt_tracker', $changes, new PwUserBo($user['uid']));
            }
        }

        // Update peer
        Wind::import('EXT:torrent.service.dm.PwTorrentDm');
        $torrent = PwAnnounce::updatePeerCount($torrent, $peers);
        $dm = new PwTorrentDm($torrent['id']);
        $dm->setSeeders($torrent['seeders'])->setLeechers($torrent['leechers'])->setLastAction(Pw::time2str(Pw::getTime(), 'Y-m-d H:i:s'));
        $this->_getTorrentDS()->updateTorrent($dm);

        // Output peers list to client
        $peer_string = PwAnnounce::buildPeerList($torrent, $peers, $compact, $no_peer_id);

        exit($peer_string);
    }

    public function downloadAction()
    {
        $id = $this->getInput('id');
        $passkey = $this->getInput('passkey');

        if (!$this->loginUser->uid && empty($passkey)) {
            $this->showError('必须登录才能进行本操作！');
        } elseif (is_string($passkey)) {
            $user = $this->_getTorrentUserDS()->getTorrentUserByPasskey($passkey);
            if (empty($user)) {
                $this->showError('Passkey 错误！');
            } else {
                $uid = $user['uid'];
            }
        } else {
            Wind::import('EXT:torrent.service.srv.helper.PwPasskey');

            $uid = $this->loginUser->uid;
            $passkey = PwPasskey::getPassKey($uid);
        }

        $userBan = Wekit::load('SRV:user.PwUserBan')->getBanInfo($uid);
        if ($userBan) {
            $this->showError('用户处于封禁期！');
        }

        $file = WEKIT_PATH . '../torrent/' . $id . '.torrent';
        if (!file_exists($file)) {
            $this->showError('种子文件不存在！');
        }

        $torrent = $this->_getTorrentDS()->getTorrent($id);

        // Check if torrent was removed
        $topic = Wekit::load('forum.PwThread')->getThread($torrent['tid']);
        if ($topic['disabled'] > 0 && !in_array($user['groupid'], array(3, 4, 5))) {
            $this->showError('种子已被删除！');
        }

        // Change announce to user's private announce
        $bencode = Wekit::load('EXT:torrent.service.srv.helper.PwBencode');
        $dictionary = $bencode->doDecodeFile($file);
        $dictionary['value']['announce'] = $bencode->doDecode($bencode->doEncodeString(WindUrlHelper::createUrl('/app/torrent/index/announce?passkey=' . $passkey)));

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
        $id = $this->getInput('id');
        $unsub = $this->getInput('unsub');

        if (!$this->loginUser->uid) {
            $this->showError('必须登录才能进行本操作！');
        }

        $userBan = Wekit::load('SRV:user.PwUserBan')->getBanInfo($this->loginUser->uid);
        if ($userBan) {
            $this->showError('用户处于封禁期！');
        }

        $torrent = Wekit::load('EXT:torrent.service.PwTorrent')->getTorrent($id);
        if (empty($torrent)) {
            $this->showError('种子文件不存在！');
        }

        $torrent = $this->_getTorrentSubscribeDs()->getTorrentSubscribeByUidAndTorrent($this->loginUser->uid, $id);
        if (!empty($torrent)) {
            if ($unsub == 'true') {
                $this->_getTorrentSubscribeDs()->deleteTorrentSubscribe($torrent['id']);
                exit('{"status":0}');
            } else {
                exit('{"status":1, "message":"已订阅该种子！"}');
            }
        }

        Wind::import('EXT:torrent.service.dm.PwTorrentSubscribeDm');

        $dm = new PwTorrentSubscribeDm();
        $dm->setUid($this->loginUser->uid)->setTorrent($id);
        $this->_getTorrentSubscribeDs()->addTorrentSubscribe($dm);

        header('Location: ' . $_SERVER['HTTP_REFERER']);

        exit('{"status":0}');
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

        $torrents = $this->_getTorrentSubscribeDs()->getTorrentSubscribeByUid($this->loginUser->uid);

        $this->setTheme('space', $space->space['space_style']);

        $this->setOutput($space, 'space');
        $this->setOutput($torrents, 'torrents');

        // seo设置
        Wind::import('SRV:seo.bo.PwSeoBo');
        $seoBo = PwSeoBo::getInstance();
        $lang = Wind::getComponent('i18n');
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

        $user = $this->_getTorrentUserDS()->getTorrentUserByPasskey($passkey);
        if (empty($user)) {
            $this->showError('Passkey 错误！');
        }

        $userBan = Wekit::load('SRV:user.PwUserBan')->getBanInfo($user['uid']);
        if ($userBan) {
            $this->showError('用户处于封禁期！');
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

        $torrents = $this->_getTorrentSubscribeDs()->getTorrentSubscribeByUid($this->loginUser->uid);

        if (is_array($torrents)) {
            foreach ($torrents as $torrent) {
                if ($torrent['disabled'] > 0 && !in_array($user['groupid'], array(3, 4, 5))) {
                    continue;
                }
                echo '<item>';
                echo '<title><![CDATA[' . $torrent['filename'] . ']]></title>';
                echo '<link><![CDATA[' . WindUrlHelper::createUrl('/bbs/read/run?tid=' . $torrent['tid']) . ']]></link>';
                echo '<pubDate>' . date('D, d M Y H:i:s O', $torrent['created_time']) . '</pubDate>';
                echo '<description><![CDATA[' . $torrent['subject'] . ']]></description>';
                echo '<enclosure type="application/x-bittorrent" length="' . $torrent['size'] . '" url="' . str_replace('&', '&amp;', WindUrlHelper::createUrl('/app/torrent/index/download?id=' . $torrent['torrent'] . '&passkey=' . $passkey)) . '" />';
                echo '<author><![CDATA[' . $torrent['created_username'] . ']]></author>';
                echo '<category domain="' . WindUrlHelper::createUrl('/bbs/thread/run?fid=' . $torrent['fid']) . '"><![CDATA[' . $torrent['name'] . ']]></category>';
                echo '</item>';
            }
        }

        echo '</channel>';
        echo '</rss>';

        exit();
    }

    private function _getTorrentDS()
    {
        return Wekit::load('EXT:torrent.service.PwTorrent');
    }

    private function _getTorrentPeerDS()
    {
        return Wekit::load('EXT:torrent.service.PwTorrentPeer');
    }

    private function _getTorrentUserDS()
    {
        return Wekit::load('EXT:torrent.service.PwTorrentUser');
    }

    private function _getTorrentAgentDS()
    {
        return Wekit::load('EXT:torrent.service.PwTorrentAgent');
    }

    private function _getTorrentHistoryDs()
    {
        return Wekit::load('EXT:torrent.service.PwTorrentHistory');
    }

    private function _getTorrentSubscribeDs()
    {
        return Wekit::load('EXT:torrent.service.PwTorrentSubscribe');
    }
}
