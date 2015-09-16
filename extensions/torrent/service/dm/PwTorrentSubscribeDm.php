<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:base.PwBaseDm');

class PwTorrentSubscribeDm extends PwBaseDm
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

    public function setTorrent($torrent)
    {
        $this->_data['torrent'] = $torrent;
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
