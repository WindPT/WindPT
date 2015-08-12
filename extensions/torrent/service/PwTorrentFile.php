<?php

defined('WEKIT_VERSION') || exit('Forbidden');

class PwTorrentFile
{
    const FETCH_MAIN = 1;
    public function getTorrentFile($id, $fetchmode = self::FETCH_MAIN)
    {
        if (empty($id)) {
            return array();
        }

        return $this->_getDao($fetchmode)->getTorrentFile($id);
    }
    public function addTorrentFile(PwTorrentFileDm $dm)
    {
        if (($result = $dm->beforeAdd()) !== true) {
            return $result;
        }
        return $this->_getDao(self::FETCH_MAIN)->addTorrentFile($dm->getData());
    }
    public function getTorrentFileByTorrent($id, $fetchmode = self::FETCH_MAIN)
    {
        return $this->_getDao($fetchmode)->getTorrentFileByTorrent($id);
    }
    public function updateTorrentFile(PwTorrentFileDm $dm, $fetchmode = self::FETCH_MAIN)
    {
        if (($result = $dm->beforeUpdate()) !== true) {
            return $result;
        }
        return $this->_getDao($fetchmode)->updateTorrentFile($dm->id, $dm->getData(), $dm->getIncreaseData());
    }
    public function deleteTorrentFile($id)
    {
        if (empty($id)) {
            return false;
        }

        return $this->_getDao(self::FETCH_MAIN)->deleteTorrentFile($id);
    }
    protected function _getDaoMap()
    {
        return array(
            self::FETCH_MAIN => 'EXT:torrent.service.dao.PwTorrentFileDao',
        );
    }
    protected function _getDao($fetchmode = self::FETCH_MAIN)
    {
        return Wekit::loadDaoFromMap($fetchmode, $this->_getDaoMap(), 'PwTorrentFile');
    }
}
