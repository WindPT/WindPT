<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:base.PwBaseDm');

class PwTorrentPeerDm extends PwBaseDm
{
    public $id;

    public function __construct($id = 0)
    {
        $this->id = $id;
    }

    public function setTorrent($torrent)
    {
        $this->_data['torrent'] = $torrent;
        return $this;
    }

    public function setPeerId($peer_id)
    {
        $this->_data['peer_id'] = $peer_id;
        return $this;
    }

    public function setUid($uid)
    {
        $this->_data['uid'] = $uid;
        return $this;
    }

    public function setIp($ip)
    {
        $this->_data['ip'] = $ip;
        return $this;
    }

    public function setPort($port)
    {
        $this->_data['port'] = $port;
        return $this;
    }

    public function setUploaded($uploaded)
    {
        $this->_data['uploaded'] = $uploaded;
        return $this;
    }

    public function setDownloaded($downloaded)
    {
        $this->_data['downloaded'] = $downloaded;
        return $this;
    }

    public function setLeft($left)
    {
        $this->_data['left'] = $left;
        return $this;
    }

    public function setSeeder($seeder)
    {
        $this->_data['seeder'] = $seeder;
        return $this;
    }

    public function setStartedAt($started_at)
    {
        $this->_data['started_at'] = $started_at;
        return $this;
    }

    public function setLastAction($last_action)
    {
        $this->_data['last_action'] = $last_action;
        return $this;
    }

    public function setConnectable($connectable)
    {
        $this->_data['connectable'] = $connectable;
        return $this;
    }

    public function setAgent($agent)
    {
        $this->_data['agent'] = $agent;
        return $this;
    }

    public function setFinishedAt($finishedat)
    {
        $this->_data['finished_at'] = $finishedat;
        return $this;
    }

    public function setPasskey($passkey)
    {
        $this->_data['passkey'] = $passkey;
        return $this;
    }

    protected function _beforeAdd()
    {
        return true;
    }

    protected function _beforeUpdate()
    {
        return true;
    }
}
