<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:base.PwBaseDm');

class PwTorrentUserDm extends PwBaseDm
{
    public function __construct($uid = 0)
    {
        $this->_data['uid'] = $uid;
    }

    public function setPassKey($passkey)
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
