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
        
        //获取Peers getTorrentPeerByTorrentAndUid
        $peers = PwAnnounce::getPeersByTorrentId($torrent['id'], $peerId);
        $self = PwAnnounce::getSelf($peers, $peerId);
        
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
            
            $this->_getTorrentPeerDS()->deleteTorrentPeerByTorrentAndUid($torrent['id'], $user['uid']);

            $dm = new PwTorrentPeerDm();
            $dm->setTorrent($torrent['id'])->setUserid($user['uid'])->setPeerId($peerId)->setIp($ip)->setPort($port)->setConnectable($connectable)->setUploaded($uploaded)->setDownloaded($downloaded)->setToGo($left)->setStarted(Pw::time2str(Pw::getTime(), 'Y-m-d H:i:s'))->setLastAction(Pw::time2str(Pw::getTime(), 'Y-m-d H:i:s'))->setSeeder($seeder)->setAgent($agent)->setPasskey($passKey);
            $this->_getTorrentPeerDS()->addTorrentPeer($dm);
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
            
            $user_torrents = $this->_getTorrentDS()->fetchTorrentByUid($user['uid']);
            
            if (count($user_torrents) < 1) {
                $credit_total = 0;
            } elseif ($downloaded_total / 1073741824 < 10) {
                $credit_total = 1;
            } elseif ($downloaded_total / 1073741824 < 30) {
                if ($rotio > 1.05) $credit_total = 2;
                elseif ($rotio < 0.95) $credit_total = 1;
                else $credit_total = 2;
            } elseif ($downloaded_total / 1073741824 < 60) {
                if ($rotio > 1.55) $credit_total = 3;
                elseif ($rotio < 1.45) $credit_total = 2;
                else $credit_total = 3;
            } elseif ($downloaded_total / 1073741824 < 100) {
                if ($rotio > 2.05) $credit_total = 4;
                elseif ($rotio < 1.95) $credit_total = 3;
                else $credit_total = 4;
            } elseif ($downloaded_total / 1073741824 < 400) {
                if ($rotio > 2.55) $credit_total = 5;
                elseif ($rotio < 2.45) $credit_total = 4;
                else $credit_total = 5;
            } elseif ($downloaded_total / 1073741824 < 1024) {
                if ($rotio > 3.55) $credit_total = 6;
                elseif ($rotio < 3.45) $credit_total = 5;
                else $credit_total = 6;
            } elseif ($downloaded_total / 1073741824 < 3075) {
                if ($rotio > 4.05) $credit_total = 7;
                elseif ($rotio < 3.95) $credit_total = 6;
                else $credit_total = 7;
            } elseif ($downloaded_total / 1073741824 < 5120) {
                if ($rotio > 4.55) $credit_total = 8;
                elseif ($rotio < 4.45) $credit_total = 7;
                else $credit_total = 8;
            } elseif ($downloaded_total / 1073741824 < 9216) {
                if ($rotio > 5.05) $credit_total = 9;
                elseif ($rotio < 4.95) $credit_total = 8;
                else $credit_total = 9;
            } elseif ($downloaded_total / 1073741824 < 11264) {
                if ($rotio > 5.55) $credit_total = 10;
                elseif ($rotio < 5.45) $credit_total = 9;
                else $credit_total = 10;
            } elseif ($downloaded_total / 1073741824 < 13312) {
                if ($rotio > 6.05) $credit_total = 11;
                elseif ($rotio < 5.95) $credit_total = 10;
                else $credit_total = 11;
            } elseif ($downloaded_total / 1073741824 < 14336) {
                if ($rotio > 6.55) $credit_total = 12;
                elseif ($rotio < 6.45) $credit_total = 11;
                else $credit_total = 12;
            } elseif ($downloaded_total / 1073741824 < 17408) {
                if ($rotio > 7.05) $credit_total = 13;
                elseif ($rotio < 6.95) $credit_total = 12;
                else $credit_total = 13;
            } elseif ($downloaded_total / 1073741824 < 20480) {
                if ($rotio > 7.55) $credit_total = 14;
                elseif ($rotio < 7.45) $credit_total = 13;
                else $credit_total = 14;
            }
            
            $WindApi = WindidApi::api('user');
            $crdtits = $WindApi->getUserCredit($user['uid']);
            $credit_add = $credit_total - $crdtits['credit3'];
            
            if ($credit_add != 0) {
                $pwUser = Wekit::load('user.PwUser');
                Wind::import('SRV:credit.bo.PwCreditBo');
                $creditBo = PwCreditBo::getInstance();
                $changes = array('3' => $credit_add);
                $creditBo->addLog('PT Tracker', $changes, new PwUserBo($user['uid']));
                $credits_to = array('3' => $credit_total);
                $creditBo->execute(array($user['uid'] => $credits_to), false);
            }
            
            $dm = new PwTorrentHistoryDm($history['id']);
            $dm->setUid($user['uid'])->setTorrent($torrent['id'])->setUploaded($uploaded)->setUploadedLast($uploaded_last)->setDownloaded($downloaded_total)->setDownloadedLast($downloaded);
            if ($status != '') $dm->setStatus($status);
            $this->_getTorrentHistoryDao()->updateTorrentHistory($history['id'], $dm->getData());
            
            //$sql = 'UPDATE pw_app_torrent_user SET uploaded_mo = :uploaded, downloaded_mo = :downloaded WHERE uid = :uid';
            //$this->dexec($dbHandle, $sql, array(':uid' => $user['uid'], ':uploaded' => $user['uploaded_mo'] + $uploaded_add, ':downloaded' => $user['downloaded_mo'] + $downloaded_add));
            
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
