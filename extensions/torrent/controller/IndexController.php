<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:user.bo.PwUserBo');
Wind::import('EXT:torrent.service.srv.helper.PwPasskey');
Wind::import('EXT:torrent.service.srv.helper.PwBencode');
Wind::import('EXT:torrent.service.srv.helper.PwUpdateInfo');
Wind::import('EXT:torrent.service.srv.helper.PwAnnounce');
Wind::import('EXT:torrent.service.dm.PwTorrentDm');
Wind::import('EXT:torrent.service.dm.PwTorrentPeerDm');
Wind::import('EXT:torrent.service.dm.PwTorrentHistoryDm');

class IndexController extends PwBaseController
{
    private $passkey;

    /*
    public function beforeAction($handlerAdapter) {
    parent::beforeAction($handlerAdapter);
    }
     */

    public function run()
    {
        exit('WindPT private BitTorrent tracker');
    }

    public function updateInfoAction()
    {
        if (Wekit::C('site', 'app.torrent.titlegen.ifopen') > 0) {
            $t_type = $this->getInput('t_type', 'post');
            $w_type = $this->getInput('w_type', 'post');
            $wikilink = $this->getInput('wikilink', 'post');
            //$paras = $this->getInput('paras', 'post');
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

            switch ($t_type) {
                case '1':
                    // 书籍
                    $url = 'https://api.douban.com/v2/book/' . $wikilink;
                    if (!empty(Wekit::C('site', 'app.torrent.titlegen.douban'))) {
                        $url .= '?apikey=' . Wekit::C('site', 'app.torrent.titlegen.douban');
                    }

                    $result = json_decode(PwUpdateInfo::curl($url));
                    $title = '[' . $result->pubdate . ']';     // 年份
                    $title .= '[' . $result->title . ']';     // 标题

                    // 子标题
                    if ($result->subtitle) {
                        $title .= '[' . $result->subtitle . ']';
                    }

                    // 作者
                    if ($result->author) {
                        $title .= '[';
                        foreach ($result->author as $author) {
                            $title .= $author . ' / ';
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

                        $result = json_decode(PwUpdateInfo::curl($url));
                        $title = '[' . $result->countries[0] . ']';     // 国别
                        $title .= '[' . $result->year . ']';     // 年份
                        $title .= '[' . $result->title . ']';     // 影片中文名

                        // 又名
                        $title .= '[';
                        foreach ($result->aka as $aka) {
                            if ($aka != $result->title) {
                                $title .= $aka . ' / ';
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
                        foreach ($result->genres as $genre) {
                            $title .= $genre . ' / ';
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
                        $content = '[img]' . $result->images->medium . '[/img]<br />' . $result->summary;
                    } elseif ($w_type == 2) {
                        // IMDB
                        $url = 'http://omdbapi.com/?i=' . $wikilink;
                        $result = json_decode(PwUpdateInfo::curl($url));
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

                    $result = json_decode(PwUpdateInfo::curl($url));
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
        $seeder = ($left > 0) ? 'no' : 'yes';

        // Check if a BitTorrent client
        $allowedClients = $this->_getTorrentAgentDS()->fetchTorrentAgent();
        foreach ($allowedClients as $allowedClient) {
            if (!preg_match($allowedClient['agent_pattern'], $agent)) {
                continue;
            }

            if ($allowedClient['peer_id_pattern'] == '' || preg_match($allowedClient['peer_id_pattern'], $peerId)) {
                $allowed = true;
            }

            break;
        }
        if (!$allowed) {
            PwAnnounce::showError('This a a bittorrent application and can\'t be loaded into a browser!');
        }

        // Verify passkey
        $user = $this->_getTorrentUserDS()->getTorrentUserByPasskey($passkey);
        if (!$user) {
            PwAnnounce::showError('Invalid passkey! Re-download the torrent file!');
        }

        // Check if user was banned
        $userBan = Wekit::load('SRV:user.dao.PwUserBanDao')->getBanInfo($user['uid']);
        if ($userBan) {
            PwAnnounce::showError('User was banned!');
        }

        // Get torrent information by infoHash
        $torrent = $this->_getTorrentDS()->getTorrentByInfoHash($infoHash);
        if (!$torrent) {
            PwAnnounce::showError('Torrent not registered with this tracker!');
        }

        // Get peers list
        $peers = PwAnnounce::getPeersByTorrentId($torrent['id'], $self['peer_id']);

        // Get this peer
        $self = $this->_getTorrentPeerDS()->getTorrentPeerByTorrentAndUid($torrent['id'], $user['uid']);

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
                    $status = ($left > 0) ? 'do' : 'done';
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
            $status = ($left > 0) ? 'do' : 'done';
        }

        // Update user's history with this torrent
        $history = $this->_getTorrentHistoryDs()->getTorrentHistoryByTorrentAndUid($torrent['id'], $user['uid']);
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
            unset($uploaded_add);
            unset($downloaded_add);
            unset($uploaded_total);
            unset($downloaded_total);
        }

        // Update user's credits
        if (Wekit::C('site', 'app.torrent.creditifopen') == 1) {
            $changed = 0;
            $WindApi = WindidApi::api('user');
            $pwUser = Wekit::load('user.PwUser');
            $crdtits = $WindApi->getUserCredit($user['uid']);
            $_credits = Wekit::C('site', 'app.torrent.credits');
            $user_torrents = count($this->_getTorrentDS()->fetchTorrentByUid($user['uid']));
            $histories = $this->_getTorrentHistoryDs()->fetchTorrentHistoryByUid($user['uid']);
            foreach ($histories as $history) {
                $downloaded_total += $history['downloaded'];
                $uploaded_total += $history['uploaded'];
            }
            unset($histories);
            if ($downloaded_total != 0) {
                $rotio_total = round($uploaded_total / $downloaded_total, 2);
            } else {
                $rotio_total = 1;
            }

            $timeUsed = time() - strtotime($self['started']);
            $symbol = array('%downloaded%', '%downloaded_total%', '%uploaded%', '%uploaded_total%', '%rotio%', '%rotio_total%', '%time%', '%credit%', '%torrents%');
            $numbers = array(intval($downloaded), intval($downloaded_total), intval($uploaded), intval($uploaded_total), intval($rotio), intval($rotio_total), intval($timeUsed), 0, intval($user_torrents));
            foreach ($_credits as $key => $value) {
                if ($value['enabled'] != '1') {
                    continue;
                }

                $numbers[7] = intval($crdtits['credit' . $key]);
                $exp = str_replace($symbol, $numbers, $value['func']);
                $credit_c = PwAnnounce::cal($exp);
                $changes[$key] = $credit_c;
                $changed++;
            }
            if ($changed) {
                Wind::import('SRV:credit.bo.PwCreditBo');
                $creditBo = PwCreditBo::getInstance();
                $creditBo->sets($user['uid'], $changes);
                $creditBo->addLog('pt_tracker', $changes, new PwUserBo($user['uid']));
            }
        }

        // Update peer
        $torrent = PwAnnounce::updatePeerCount($torrent, $peers);
        $dm = new PwTorrentDm($torrent['id']);
        $dm->setSeeders($torrent['seeders'])->setLeechers($torrent['leechers'])->setLastAction(Pw::time2str(Pw::getTime(), 'Y-m-d H:i:s'));
        $this->_getTorrentDS()->updateTorrent($dm);

        // Output peers list to client
        $peer_string = PwAnnounce::buildWaitTime($torrent);
        $peer_string = PwAnnounce::buildPeerList($peers, $compact, $no_peer_id, $peer_string);

        header('Content-Type: text/plain; charset=utf-8');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');

        exit($peer_string);
    }

    public function downloadAction()
    {
        // Get the torrent file
        $id = $this->getInput('id');
        $result = $this->check();
        if ($result instanceof PwError) {
            $this->showError($result->getError());
        }

        $file = WEKIT_PATH . '../torrent/' . $id . '.torrent';
        if (!file_exists($file)) {
            $this->showError('种子文件不存在！');
        }

        // Change announce to user's private announce
        $bencode = new PwBencode();
        $dictionary = $bencode->doDecodeFile($file);
        $passkey = PwPasskey::getPassKey($this->loginUser->uid);
        $dictionary['value']['announce'] = $bencode->doDecode($bencode->doEncodeString(WindUrlHelper::createUrl('/app/torrent/index/announce?passkey=' . $passkey)));

        // Generate file name
        $torrent = $this->_getTorrentDS()->getTorrent($id);
        $torrentnameprefix = Wekit::C('site', 'app.torrent.torrentnameprefix');
        if ($torrentnameprefix == '') {
            $torrentnameprefix = Wekit::C('site', 'info.name');
        }

        $torrentnameprefix = '[' . $torrentnameprefix . '][';

        // Send torrent file to broswer
        header('Content-Description: File Transfer');
        header('Content-type: application/octet-streamn');
        header('Content-Disposition: attachment; filename="' . $torrentnameprefix . rawurlencode($torrent['save_as']) . '].torrent"; charset=utf-8');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');

        exit($bencode->doEncode($dictionary));
    }

    private function check()
    {
        if (!$this->loginUser->uid) {
            return new PwError('必须登录才能进行本操作！');
        }
        $userBan = Wekit::load('SRV:user.dao.PwUserBanDao')->getBanInfo($this->loginUser->uid);
        if ($userBan) {
            return new PwError('用户处于封禁期！');
        }
        return true;
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
}
