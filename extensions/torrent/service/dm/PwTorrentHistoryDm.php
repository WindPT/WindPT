<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:base.PwBaseDm');

class PwTorrentHistoryDm extends PwBaseDm
{
    public $id;

    public function __construct($id = 0)
    {
        $this->id = $id;
    }

    public function setUid($uid)
    {
        $this->_data['uid'] = $uid;
        return $this;
    }

    public function setTorrentId($torrent_id)
    {
        $this->_data['torrent_id'] = $torrent_id;
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

    public function setLeeched($leeched)
    {
        $this->_data['leeched'] = $leeched;
        return $this;
    }

    public function setSeeded($seeded)
    {
        $this->_data['seeded'] = $seeded;
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
