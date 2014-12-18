<?php
defined('WEKIT_VERSION') || exit('Forbidden');
Wind::import('LIB:base.PwBaseDm');
class PwTorrentFileDm extends PwBaseDm {
    public $id;
    public function __construct($id = 0) {
        $this->id = $id;
    }
    public function setTottent($torrent) {
        $this->_data['torrent'] = $torrent;
        return $this;
    }
    public function setFileName($filename) {
        $this->_data['filename'] = $filename;
        return $this;
    }
    public function setSize($size) {
        $this->_data['size'] = $size;
        return $this;
    }
    protected function _beforeAdd() {
        return true;
    }
    protected function _beforeUpdate() {
        return true;
    }
}