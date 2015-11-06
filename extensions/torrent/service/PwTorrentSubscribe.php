<?php

defined('WEKIT_VERSION') || exit('Forbidden');

class PwTorrentSubscribe
{
    const FETCH_MAIN = 1;

    public function getTorrentSubscribe($id, $fetchmode = self::FETCH_MAIN)
    {
        if (empty($id)) {
            return array();
        }

        return $this->_getDao($fetchmode)->getTorrentSubscribe($id);
    }

    public function getTorrentSubscribeByUid($uid, $fetchmode = self::FETCH_MAIN)
    {
        return $this->_getDao($fetchmode)->getTorrentSubscribeByUid($uid);
    }

    public function getTorrentSubscribeByUidAndTorrent($uid, $torrent, $fetchmode = self::FETCH_MAIN)
    {
        return $this->_getDao($fetchmode)->getTorrentSubscribeByUidAndTorrent($uid, $torrent);
    }

    public function fetchTorrentSubscribe($fetchmode = self::FETCH_MAIN)
    {
        return $this->_getDao($fetchmode)->fetchTorrentSubscribe();
    }

    public function addTorrentSubscribe(PwTorrentSubscribeDm $dm)
    {
        if (($result = $dm->beforeAdd()) !== true) {
            return $result;
        }
        return $this->_getDao(self::FETCH_MAIN)->addTorrentSubscribe($dm->getData());
    }

    public function deleteTorrentSubscribe($id)
    {
        if (empty($id)) {
            return false;
        }

        return $this->_getDao(self::FETCH_MAIN)->deleteTorrentSubscribe($id);
    }

    protected function _getDaoMap()
    {
        return array(self::FETCH_MAIN => 'EXT:torrent.service.dao.PwTorrentSubscribeDao');
    }

    protected function _getDao($fetchmode = self::FETCH_MAIN)
    {
        return Wekit::loadDaoFromMap($fetchmode, $this->_getDaoMap(), 'PwTorrentSubscribeDao');
    }
}
