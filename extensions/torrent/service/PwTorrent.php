<?php
defined('WEKIT_VERSION') || exit('Forbidden');
class PwTorrent {
    const FETCH_MAIN = 1;
    public function getTorrent($id, $fetchmode = self::FETCH_MAIN) {
        if (empty($id)) return array();
        return $this->_getDao($fetchmode)->getTorrent($id);
    }
    public function getTorrentByTid($tid, $fetchmod = self::FETCH_MAIN) {
        if (empty($tid)) return array();
        return $this->_getDao($fetchmod)->getTorrentByTid($tid);
    }
    public function getTorrentByInfoHash($info_hash, $fetchmod = self::FETCH_MAIN) {
        if (empty($info_hash)) return array();
        return $this->_getDao($fetchmod)->getTorrentByInfoHash($info_hash);
    }
    public function addTorrent(PwTorrentDm $dm) {
        if (($result = $dm->beforeAdd()) !== true) {
            return $result;
        }
        return $this->_getDao(self::FETCH_MAIN)->addTorrent($dm->getData());
    }
    public function checkTorrentExist($hash) {
        return $this->_getDao(self::FETCH_MAIN)->checkTorrentExist($hash);
    }
    public function updateTorrent(PwTorrentDm $dm, $fetchmode = self::FETCH_MAIN) {
        if (($result = $dm->beforeUpdate()) !== true) {
            return $result;
        }
        return $this->_getDao($fetchmode)->updateTorrent($dm->id, $dm->getData(), $dm->getIncreaseData());
    }
    public function deleteTorrent($id) {
        if (empty($id)) return false;
        return $this->_getDao(self::FETCH_MAIN)->deleteTorrent($aid);
    }
    protected function _getDaoMap() {
        return array(
            self::FETCH_MAIN        => 'EXT:torrent.service.dao.PwTorrentDao',
        );
    }
    protected function _getDao($fetchmode = self::FETCH_MAIN) {
        return Wekit::loadDaoFromMap($fetchmode, $this->_getDaoMap(), 'PwTorrent');
    }
}