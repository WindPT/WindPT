<?php
defined('WEKIT_VERSION') or exit(403);
Wind::import('SRV:user.bo.PwUserBo');
Wind::import('EXT:torrent.service.srv.helper.PwBencode');
Wind::import('EXT:torrent.service.srv.helper.PwAnnounce');
Wind::import('EXT:torrent.service.dm.PwTorrentDm');
Wind::import('EXT:torrent.service.dm.PwTorrentPeerDm');
Wind::import('EXT:torrent.service.dm.PwTorrentHistoryDm');
class IndexController extends PwBaseController
{
    private $user;
    public function beforeAction($handlerAdapter) {
        parent::beforeAction($handlerAdapter);
        $this->getUser();
    }
    public function run() {
        $this->setTemplate('');
        echo 'WindPT private BitTorrent tracker';
    }
    
    public function announceAction() {
        
        //变量获取
        $passKey = $this->getInput('passkey');
        $infoHash = $this->getInput('info_hash');
        $peerId = $this->getInput('peer_id');
        $event = $this->getInput('event');
        $port = $this->getInput('port');
        $downloaded = $this->getInput('downloaded');
        $uploaded = $this->getInput('uploaded');
        $left = $this->getInput('left');
        $compact = $this->getInput('compact');
        $noPeerId = $this->getInput('no_peer_id');
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $ip = Wind::getComponent('request')->getClientIp();
        
        //检测客户端是否允许下载
        if (!PwAnnounce::checkClient()) {
            PwAnnounce::showError('This a a bittorrent application and can\'t be loaded into a browser!');
        }
        
        //检测客户端角色
        $seeder = PwAnnounce::checkClientRole($left);
        
        //检测PassKey，验证用户权限
        $user = $this->_getTorrentUserDS()->getTorrentUserByPasskey($passKey);
        if (!$user) {
            PwAnnounce::showError('Invalid passkey! Re-download the torrent file!');
        }
        
        $userBan = Wekit::load('SRV:user.dao.PwUserBanDao')->getBanInfo($user['uid']);
        if ($userBan) {
            PwAnnounce::showError('User was banned!');
        }
        
        //获取种子
        $torrent = $this->_getTorrentDS()->getTorrentByInfoHash($infoHash);
        if (!$torrent) {
            PwAnnounce::showError('Torrent not registered with this tracker!');
        }
        unset($self);
        
        //获取Peers
        $peers = PwAnnounce::getPeersByTorrentId($torrent['id'], $peerId);
        //$self = PwAnnounce::getSelf($peers, $peerId);
        $self = $this->_getTorrentPeerDS()->getTorrentPeerByTorrentAndUid($torrent['id'], $user['uid']);
        //更新种子统计信息
        $torrent = PwAnnounce::updatePeerCount($torrent, $peers);
        
        //更新客户端提交数据
        if (isset($self)) {
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
                    $dm->setFinishedat(Pw::getTime())->setIp($ip)->setPort($port)->setUploaded($uploaded)->setDownloaded($downloaded)->setToGo($left)->setPrevAction(Pw::time2str(Pw::getTime(), 'Y-m-d H:i:s'))->setLastAction(Pw::time2str(Pw::getTime(), 'Y-m-d H:i:s'))->setSeeder($seeder)->setAgent($agent);
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
            $dm->setTorrent($torrent['id'])->setUserid($user['uid'])->setPeerId($peerId)->setIp($ip)->setPort($port)->setConnectable($connectable)->setUploaded($uploaded)->setDownloaded($downloaded)->setToGo($left)->setStarted(Pw::time2str(Pw::getTime(), 'Y-m-d H:i:s'))->setLastAction(Pw::time2str(Pw::getTime(), 'Y-m-d H:i:s'))->setSeeder($seeder)->setAgent($agent)->setPasskey($passKey);
            $this->_getTorrentPeerDS()->addTorrentPeer($dm);
            $self = $this->_getTorrentPeerDS()->getTorrentPeerByTorrentAndUid($torrent['id'], $user['uid']);
        }
        
        $historie = Wekit::load('EXT:torrent.service.dao.PwTorrentHistoryDao')->getTorrentHistoryByTorrentAndUid($torrent['id'], $user['uid']);
        if (!$historie) {
            $dm = new PwTorrentHistoryDm();
            $dm->setUid($user['uid'])->setTorrent($torrent['id'])->setUploaded($uploaded)->setDownloaded($downloaded);
            $this->_getTorrentHistoryDao()->addTorrentHistory($dm->getData());
        } else {
            $uploaded_add = max(0, $uploaded - $history['uploaded_last']);
            $downloaded_add = max(0, $downloaded - $history['downloaded_last']);
            
            $uploaded_total = $history['uploaded'] + $uploaded_add;
            $downloaded_total = $history['downloaded'] + $downloaded_add;
            
            if ($downloaded_total != 0) $rotio = round($uploaded_total / $downloaded_total, 2);
            else $rotio = 0;
            
            $dm = new PwTorrentHistoryDm($history['id']);
            $dm->setUid($user['uid'])->setTorrent($torrent['id'])->setUploaded($uploaded_total)->setUploadedLast($uploaded)->setDownloaded($downloaded_total)->setDownloadedLast($downloaded);
            if ($status != '') $dm->setStatus($status);
            $this->_getTorrentHistoryDao()->updateTorrentHistory($history['id'], $dm->getData());
            $uploaded = $uploaded_total;
            $downloaded = $downloaded_total;
            unset($uploaded_add);
            unset($downloaded_add);
            unset($uploaded_total);
            unset($downloaded_total);
        }
        
        if (Wekit::C('site', 'app.torrent.creditifopen') == 1) {
            $changed = 0;
            $WindApi = WindidApi::api('user');
            $pwUser = Wekit::load('user.PwUser');
            $crdtits = $WindApi->getUserCredit($user['uid']);
            $_credits = Wekit::C('site', 'app.torrent.credits');
            $user_torrents = count($this->_getTorrentDS()->fetchTorrentByUid($user['uid']));
            $histories = Wekit::load('EXT:torrent.service.dao.PwTorrentHistoryDao')->fetchTorrentHistoryByUid($user['uid']);
            foreach ($histories as $history) {
                $downloaded_total+= $history['downloaded'];
                $uploaded_total+= $history['uploaded'];
            }
            unset($histories);
            if ($downloaded_total != 0) $rotio_total = round($uploaded_total / $downloaded_total, 2);
            else $rotio_total = 0;
            $timeUsed = time() - strtotime($self['started']);
            $symbol = array('%downloaded%', '%downloaded_total%', '%uploaded%', '%uploaded_total%', '%rotio%', '%rotio_total%', '%time%', '%credit%', '%torrents%');
            $numbers = array($downloaded, $downloaded_total, $uploaded, $uploaded_total, $rotio, $rotio_total, $timeUsed, 0, $user_torrents);
            foreach ($_credits as $key => $value) {
                if (!$credit['enabled']) continue;
                $numbers[7] = $crdtits['credit' . $key];
                $exp = str_replace($symbol, $numbers, $credit['func']);
                $credit_c = PwAnnounce::bc($exp);
                $changes[$key] = $credit_c;
                $changed++;
            }
            if ($changed) {
                $creditBo = Wekit::load('SRV:credit.bo.PwCreditBo')->getInstance();
                $creditBo->sets($user['uid'], $changes);
                $creditBo->addLog('pt_tracker', $changes, new PwUserBo($user['uid']));
            }
        }
        
        foreach ($peers as $peer) {
            if ($peer['seeder'] == 'yes') {
                $seeder++;
            } else {
                $leecher++;
            }
        }
        $torrent['seeders'] = $seeder;
        $torrent['leechers'] = $leecher;
        
        //更新种子信息
        $dm = new PwTorrentDm($torrent['id']);
        $dm->setSeeders($torrent['seeders'])->setLeechers($torrent['leechers'])->setLastAction(Pw::time2str(Pw::getTime(), 'Y-m-d H:i:s'));
        $this->_getTorrentDS()->updateTorrent($dm);
        
        //返回Peers数据给客户端
        $peer_string = PwAnnounce::buildWaitTime($torrent);
        $peer_string = PwAnnounce::buildPeerList($peers, $compact, $no_peer_id, $peer_string);
        PwAnnounce::sendPeerList($peer_string);
    }
    public function downloadAction() {
        $id = $this->getInput('id');
        $result = $this->check();
        if ($result instanceof PwError) {
            $this->showError($result->getError());
        }
        $file = WEKIT_PATH . '../torrent/' . $id . '.torrent';
        if (!file_exists($file)) {
            $this->showError('种子文件不存在！');
        }
        header('Content-Description: File Transfer');
        $bencode = new PwBencode();
        $dictionary = $bencode->doDecodeFile($file);
        $dictionary['value']['announce'] = $bencode->doDecode($bencode->doEncodeString(WindUrlHelper::createUrl('app/index/announce?app=torrent&passkey=' . $this->user->passkey)));
        $torrent = $this->_getTorrentDS()->getTorrent($id);
        $torrentnameprefix = '[uupt][';
        $timestamp = Pw::getTime();
        
        header('Content-type: application/octet-streamn');
        header('Content-disposition: attachment; filename="' . $torrentnameprefix . rawurlencode($torrent['save_as']) . '].torrent"; charset=utf-8');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        echo $bencode->doEncode($dictionary);
        $this->setTemplate('');
    }
    public function check() {
        if (!$this->loginUser->uid) {
            return new PwError('必须登录才能下载种子！');
        }
        $userBan = Wekit::load('SRV:user.dao.PwUserBanDao')->getBanInfo($this->loginUser->uid);
        if ($userBan) {
            return new PwError('用户已被封禁！');
        }
        if (!$this->user->passkey) {
            Wind::import('EXT:torrent.service.dm.PwTorrentUserDm');
            $dm = new PwTorrentUserDm();
            $dm->setUid($this->loginUser->uid)->setPassKey($this->makePassKey());
            $this->_getTorrentUserDS()->addTorrentUser($dm);
            $this->getUser();
        }
        if (strlen($this->user->passkey) != 32) {
            $torrentUser = $this->_getTorrentUserDS()->getTorrentUserByUid($this->loginUser->uid);
            Wind::import('EXT:torrent.service.dm.PwTorrentUserDm');
            $dm = new PwTorrentUserDm($torrentUser['id']);
            $dm->setUid($this->loginUser->uid)->setPassKey($this->makePassKey());
            $this->_getTorrentUserDS()->updateTorrentUser($dm);
            $this->getUser();
        }
        return true;
    }
    public function getUser() {
        $user = new PwUserBo($this->loginUser->uid, true);
        $torrentUser = $this->_getTorrentUserDS()->getTorrentUserByUid($this->loginUser->uid);
        $user->passkey = $torrentUser['passkey'];
        $this->user = $user;
    }
    public function makePassKey() {
        return md5($this->loginUser->username . Pw::time2str(Pw::getTime(), 'Y-m-d H:i:s') . $this->loginUser->info['password']);
    }
    private function _getTorrentDS() {
        return Wekit::load('EXT:torrent.service.PwTorrent');
    }
    private function _getTorrentPeerDS() {
        return Wekit::load('EXT:torrent.service.PwTorrentPeer');
    }
    private function _getTorrentUserDS() {
        return Wekit::load('EXT:torrent.service.PwTorrentUser');
    }
    private function _getTorrentHistoryDao() {
        return Wekit::load('EXT:torrent.service.dao.PwTorrentHistoryDao');
    }
}