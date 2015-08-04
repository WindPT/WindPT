<?php
defined('WEKIT_VERSION') || exit('Forbidden');
Wind::import('LIB:base.PwBaseDm');
class PwTorrentUserDm extends PwBaseDm {
    public $id;
    public function __construct($id = 0) {
        $this->id = $id;
    }
    public function setUid($uid) {
        $this->_data['uid'] = $uid;
        return $this;
    }
    public function setPassKey($passkey) {
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