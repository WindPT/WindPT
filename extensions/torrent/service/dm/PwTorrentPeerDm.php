<?php
defined('WEKIT_VERSION') || exit('Forbidden');
Wind::import('LIB:base.PwBaseDm');
class PwTorrentPeerDm extends PwBaseDm {
    public $id;
    public function __construct($id = 0) {
        $this->id = $id;
    }
    public function setTorrent($torrent) {
        $this->_data['torrent'] = $torrent;
        return $this;
    }
    public function setPeerId($peer_id) {
        $this->_data['peer_id'] = $peer_id;
        return $this;
    }
    public function setIp($ip) {
        $this->_data['ip'] = $ip;
        return $this;
    }
    public function setPort($port) {
        $this->_data['port'] = $port;
        return $this;
    }
    public function setUploaded($uploaded) {
        $this->_data['uploaded'] = $uploaded;
        return $this;
    }
    public function setDownloaded($downloaded) {
        $this->_data['downloaded'] = $downloaded;
        return $this;
    }
    public function setToGo($to_go) {
        $this->_data['to_go'] = $to_go;
        return $this;
    }
    public function setSeeder($seeder) {
        $this->_data['seeder'] = $seeder;
        return $this;
    }
    public function setStarted($started) {
        $this->_data['started'] = $started;
        return $this;
    }
    public function setLastAction($last_action) {
        $this->_data['last_action'] = $last_action;
        return $this;
    }
    public function setPrevAction($prev_action) {
        $this->_data['prev_action'] = $prev_action;
        return $this;
    }
    public function setConnectable($connectable) {
        $this->_data['connectable'] = $connectable;
        return $this;
    }
    public function setUserid($userid) {
        $this->_data['userid'] = $userid;
        return $this;
    }
    public function setAgent($agent) {
        $this->_data['agent'] = $agent;
        return $this;
    }
    public function setFinishedat($finishedat) {
        $this->_data['finishedat'] = $finishedat;
        return $this;
    }
    public function setPasskey($passkey) {
        $this->_data['passkey'] = $passkey;
        return $this;
    }
    protected function _beforeAdd() {
        return true;
    }
    protected function _beforeUpdate() {
        return true;
    }
}