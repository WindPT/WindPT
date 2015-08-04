<?php
defined('WEKIT_VERSION') || exit('Forbidden');
class PwTorrentAgent
{
    const FETCH_MAIN = 1;
    
    public function getTorrentAgent($id, $fetchmode = self::FETCH_MAIN) {
        if (empty($id)) return array();
        return $this->_getDao($fetchmode)->getTorrentAgent($id);
    }
    public function addTorrentAgent(PwTorrentAgentDm $dm) {
        if (($result = $dm->beforeAdd()) !== true) {
            return $result;
        }
        return $this->_getDao(self::FETCH_MAIN)->addTorrentAgent($dm->getData());
    }
    public function fetchTorrentAgent($fetchmode = self::FETCH_MAIN) {
        return $this->_getDao($fetchmode)->fetchTorrentAgent();
    }
    public function updateTorrentAgent(PwTorrentAgentDm $dm, $fetchmode = self::FETCH_MAIN) {
        if (($result = $dm->beforeUpdate()) !== true) {
            return $result;
        }
        return $this->_getDao($fetchmode)->updateTorrentAgent($dm->id, $dm->getData(), $dm->getIncreaseData());
    }
    public function deleteTorrentAgent($id) {
        if (empty($id)) return false;
        return $this->_getDao(self::FETCH_MAIN)->deleteTorrentAgent($id);
    }
    protected function _getDaoMap() {
        return array(self::FETCH_MAIN => 'EXT:torrent.service.dao.PwTorrentAgentDao',);
    }
    protected function _getDao($fetchmode = self::FETCH_MAIN) {
        return Wekit::loadDaoFromMap($fetchmode, $this->_getDaoMap(), 'PwTorrentAgentDao');
    }
}