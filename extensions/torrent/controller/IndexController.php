<?php
defined('WEKIT_VERSION') or exit(403);
Wind::import('SRV:user.bo.PwUserBo');
Wind::import('EXT:torrent.service.srv.helper.PwBencode');
Wind::import('EXT:torrent.service.srv.helper.PwAnnounce');
Wind::import('EXT:torrent.service.dm.PwTorrentDm');
Wind::import('EXT:torrent.service.dm.PwTorrentPeerDm');

class IndexController extends PwBaseController
{
    private $user;
    public function beforeAction($handlerAdapter) {
        parent::beforeAction($handlerAdapter);
        $this->getUser();
    }
    public function run() {
        $peers = $this->_getTorrentPeerDS()->getTorrentPeerByTorrent(8);
    }
    
    public function ptProfileAction() {
        $this->setTemplate('');
    }
    
    private function dquery($dbHandle, $sql, $bind) {
        $sth = @$dbHandle->prepare($sql) or PwAnnounce::showError("database: Cannot prepare statement for execution!");
        @$sth->execute($bind) or PwAnnounce::showError("database: Cannot fetch result");
        if ($sth->errorCode == 0) {
            $result = @$sth->fetchAll(PDO::FETCH_ASSOC);
            if (empty($result) || empty($result[0])) return NULL;
            else return $result;
        } else {
            PwAnnounce::showError("database: Database returned an error : " . $sth->errorCode);
        }
    }
    
    private function dexec($dbHandle, $sql, $bind) {
        $sth = @$dbHandle->prepare($sql) or PwAnnounce::showError("database: Cannot prepare statement for execution!");
        @$sth->execute($bind) or PwAnnounce::showError("database: Cannot fetch result");
        if ($sth->errorCode == 0) return $sth->rowCount();
        else PwAnnounce::showError("database: Database returned an error : " . $sth->errorCode);
    }
    
    public function announceAction() {
        
        //变量获取
        $passKey = $this->getInput("passkey");
        $infoHash = $this->getInput("info_hash");
        $peerId = $this->getInput("peer_id");
        $event = $this->getInput("event");
        $port = $this->getInput("port");
        $downloaded = $this->getInput("downloaded");
        $uploaded = $this->getInput("uploaded");
        $left = $this->getInput("left");
        $compact = $this->getInput("compact");
        $noPeerId = $this->getInput("no_peer_id");
        $agent = $_SERVER["HTTP_USER_AGENT"];
        $ip = Wind::getComponent('request')->getClientIp();
        
        //检测客户端是否允许下载
        if (!PwAnnounce::checkClient()) {
            PwAnnounce::showError("This a a bittorrent application and can't be loaded into a browser!");
        }
        
        //检测客户端角色
        $seeder = PwAnnounce::checkClientRole($left);
        
        //检测PassKey，验证用户权限
        $user = $this->_getTorrentUserDS()->getTorrentUserByPasskey($passKey);
        if (!$user) {
            PwAnnounce::showError("Invalid passkey! Re-download the torrent file!");
        }
        
        $config = require (realpath(dirname(__FILE__)) . '/../../../../conf/database.php');
        try {
            $dbHandle = new PDO($config['dsn'], $config['user'], $config['pwd']);
            $dbHandle->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        }
        catch(PDOException $e) {
            PwAnnounce::showError("database: Cannot connect to database!");
        }
        
        $sql = 'SELECT * from pw_user_ban WHERE uid = :uid';
        $ban_info = $this->dquery($dbHandle, $sql, array(':uid' => $user['uid']));
        if ($ban_info[0]) {
            PwAnnounce::showError("User was banned!");
        }
        
        //获取种子
        $torrent = $this->_getTorrentDS()->getTorrentByInfoHash($infoHash);
        if (!$torrent) {
            PwAnnounce::showError("Torrent not registered with this tracker!");
        }
        unset($self);
        
        //获取Peers
        $peers = PwAnnounce::getPeersByTorrentId($torrent['id'], $peerId);
        $self = PwAnnounce::getSelf($peers, $peerId);
        
        //更新种子统计信息
        $torrent = PwAnnounce::updatePeerCount($torrent, $peers);
        
        //更新客户端提交数据
        if (isset($self)) {
            $dm = new PwTorrentPeerDm($self['id']);
            switch ($event) {
                case "":
                case "started":
                    $dm->setIp($ip)->setPort($port)->setUploaded($uploaded)->setDownloaded($downloaded)->setToGo($left)->setPrevAction(Pw::time2str(Pw::getTime(), "Y-m-d H:i:s"))->setLastAction(Pw::time2str(Pw::getTime(), "Y-m-d H:i:s"))->setSeeder($seeder)->setAgent($agent);
                    $this->_getTorrentPeerDS()->updateTorrentPeer($dm);
                    break;

                case "stopped":
                    $this->_getTorrentPeerDS()->deleteTorrentPeer($self['id']);
                    $status = 'stop';
                    break;

                case "completed":
                    $dm->setFinishedat(Pw::getTime());
                    $dm->setIp($ip)->setPort($port)->setUploaded($uploaded)->setDownloaded($downloaded)->setToGo($left)->setPrevAction(Pw::time2str(Pw::getTime(), "Y-m-d H:i:s"))->setLastAction(Pw::time2str(Pw::getTime(), "Y-m-d H:i:s"))->setSeeder($seeder)->setAgent($agent);
                    $this->_getTorrentPeerDS()->updateTorrentPeer($dm);
                    $status = 'done';
                    break;

                default:
                    PwAnnounce::showError("Invalid event from client!");
            }
        } else {
            $sockres = @pfsockopen($ip, $port, $errno, $errstr, 5);
            if ($errno == "111") {
                $connectable = "no";
            } else {
                $connectable = "yes";
            }
            @fclose($sockres);
            
            $sql = 'DELETE from pw_app_torrent_peer WHERE userid = :uid AND torrent = :torrent';
            $this->dexec($dbHandle, $sql, array(':uid' => $user['uid'], ':torrent' => $torrent['id']));
            
            $dm = new PwTorrentPeerDm();
            $dm->setTottent($torrent['id'])->setUserid($user['uid'])->setPeerId($peerId)->setIp($ip)->setPort($port)->setConnectable($connectable)->setUploaded($uploaded)->setDownloaded($downloaded)->setToGo($left)->setStarted(Pw::time2str(Pw::getTime(), "Y-m-d H:i:s"))->setLastAction(Pw::time2str(Pw::getTime(), "Y-m-d H:i:s"))->setSeeder($seeder)->setAgent($agent)->setPasskey($passKey);
            $this->_getTorrentPeerDS()->addTorrentPeer($dm);
        }
        
        $sql = 'SELECT * from pw_app_torrent_history WHERE uid = :uid AND torrent = :torrent';
        $dresult = $this->dquery($dbHandle, $sql, array(':uid' => $user['uid'], ':torrent' => $torrent['id']));
        if (empty($dresult)) {
            $sql = 'INSERT INTO pw_app_torrent_history(uid, torrent, uploaded, downloaded) values(:uid, :torrent, :uploaded, :downloaded)';
            $this->dexec($dbHandle, $sql, array(':uid' => $user['uid'], ':torrent' => $torrent['id'], ':uploaded' => $uploaded, ':downloaded' => $downloaded));
        } else {
            $uploaded_add = max(0, $uploaded - $dresult[0]['uploaded_last']);
            $downloaded_add = max(0, $downloaded - $dresult[0]['downloaded_last']);
            
            $uploaded_total = $dresult[0]['uploaded'] + $uploaded_add;
            $downloaded_total = $dresult[0]['downloaded'] + $downloaded_add;
            
            if ($downloaded_total != 0) $rotio = round($uploaded_total / $downloaded_total, 2);
            else $rotio = 0;
            
            $sql = 'SELECT count(*) as total from pw_bbs_threads WHERE disabled = 0 AND ischeck = 1 AND special = "torrent" AND created_userid = :uid';
            $tresult = $this->dquery($dbHandle, $sql, array(':uid' => $user['uid']));
            
            if ($tresult[0]['total'] < 1) {
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
            
            if ($status != '') {
                $sql = 'UPDATE pw_app_torrent_history SET uploaded = :uploaded, uploaded_last = :uploaded_last, downloaded = :downloaded, downloaded_last = :downloaded_last, status = :status WHERE uid = :uid AND torrent = :torrent';
                $this->dexec($dbHandle, $sql, array(':uid' => $user['uid'], ':torrent' => $torrent['id'], ':uploaded' => $uploaded_total, ':uploaded_last' => $uploaded, ':downloaded' => $downloaded_total, ':downloaded_last' => $downloaded, ':status' => $status));
            } else {
                $sql = 'UPDATE pw_app_torrent_history SET uploaded = :uploaded, uploaded_last = :uploaded_last, downloaded = :downloaded, downloaded_last = :downloaded_last WHERE uid = :uid AND torrent = :torrent';
                $this->dexec($dbHandle, $sql, array(':uid' => $user['uid'], ':torrent' => $torrent['id'], ':uploaded' => $uploaded_total, ':uploaded_last' => $uploaded, ':downloaded' => $downloaded_total, ':downloaded_last' => $downloaded));
            }
            
            $sql = 'UPDATE pw_app_torrent_user SET uploaded_mo = :uploaded, downloaded_mo = :downloaded WHERE uid = :uid';
            $this->dexec($dbHandle, $sql, array(':uid' => $user['uid'], ':uploaded' => $user['uploaded_mo'] + $uploaded_add, ':downloaded' => $user['downloaded_mo'] + $downloaded_add));
        }
        
        $sql = 'SELECT COUNT(*) AS count FROM pw_app_torrent_peer WHERE torrent = :torrent AND seeder = :seeder';
        $dresult = $this->dquery($dbHandle, $sql, array(':torrent' => $torrent['id'], ':seeder' => 'yes'));
        $torrent['seeders'] = $dresult[0]['count'];
        
        $sql = 'SELECT COUNT(*) AS count FROM pw_app_torrent_peer WHERE torrent = :torrent AND seeder = :seeder';
        $dresult = $this->dquery($dbHandle, $sql, array(':torrent' => $torrent['id'], ':seeder' => 'no'));
        $torrent['leechers'] = $dresult[0]['count'];
        
        //更新种子信息
        $dm = new PwTorrentDm($torrent['id']);
        $dm->setSeeders($torrent['seeders']);
        $dm->setLeechers($torrent['leechers']);
        $dm->setLastAction(Pw::time2str(Pw::getTime(), "Y-m-d H:i:s"));
        $this->_getTorrentDS()->updateTorrent($dm);
        
        //返回Peers数据给客户端
        $peer_string = PwAnnounce::buildWaitTime($torrent);
        $peer_string = PwAnnounce::buildPeerList($peers, $compact, $no_peer_id, $peer_string);
        PwAnnounce::sendPeerList($peer_string);
    }
    public function downloadAction() {
        $id = $this->getInput("id");
        $result = $this->check();
        if ($result instanceof PwError) {
            $this->showError($result->getError());
        }
        $file = "./torrent/$id.torrent";
        if (!file_exists($file)) {
            $this->showError("种子文件不存在！");
        }
        header('Content-Description: File Transfer');
        $bencode = new PwBencode();
        $dictionary = $bencode->doDecodeFile($file);
        $dictionary['value']['announce'] = $bencode->doDecode($bencode->doEncodeString(WindUrlHelper::createUrl("app/index/announce?app=torrent&passkey=" . $this->user->passkey)));
        $torrent = $this->_getTorrentDS()->getTorrent($id);
        $torrentnameprefix = "[uupt][";
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
            return new PwError("必须登录才能下载种子！");
        }
        if (!$this->user->passkey) {
            Wind::import('EXT:torrent.service.dm.PwTorrentUserDm');
            $dm = new PwTorrentUserDm();
            $dm->setUid($this->loginUser->uid);
            $dm->setPassKey($this->makePassKey());
            $this->_getTorrentUserDS()->addTorrentUser($dm);
            $this->getUser();
        }
        if (strlen($this->user->passkey) != 32) {
            $torrentUser = $this->_getTorrentUserDS()->getTorrentUserByUid($this->loginUser->uid);
            Wind::import('EXT:torrent.service.dm.PwTorrentUserDm');
            $dm = new PwTorrentUserDm($torrentUser['id']);
            $dm->setUid($this->loginUser->uid);
            $dm->setPassKey($this->makePassKey());
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
        return md5($this->loginUser->username . Pw::time2str(Pw::getTime(), "Y-m-d H:i:s") . $this->loginUser->info['password']);
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
}
