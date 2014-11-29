<?php
defined('WEKIT_VERSION') || exit('Forbidden');
class PwTorrentAgentAllowedFamily {
    const FETCH_MAIN = 1;
    public function getTorrentFile($id, $fetchmode = self::FETCH_MAIN) {
        if (empty($id)) return array();
        return $this->_getDao($fetchmode)->getTorrentFile($id);
    }
    public function fetchTorrentAgent($fetchmode = self::FETCH_MAIN) {
        return $this->_getDao($fetchmode)->fetchTorrentAgent();
    }
    protected function _getDaoMap() {
        return array(
            self::FETCH_MAIN        => 'EXT:torrent.service.dao.PwTorrentAgentAllowedFamilyDao',
        );
    }
    protected function _getDao($fetchmode = self::FETCH_MAIN) {
        return Wekit::loadDaoFromMap($fetchmode, $this->_getDaoMap(), 'PwTorrentAgentAllowedFamily');
    }
}