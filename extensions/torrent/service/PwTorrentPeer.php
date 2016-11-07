<?php

defined('WEKIT_VERSION') || exit('Forbidden');

class PwTorrentPeer
{
    const FETCH_MAIN = 1;
    public function getTorrentPeer($id, $fetchmode = self::FETCH_MAIN)
    {
        if (empty($id)) {
            return array();
        }

        return $this->_getDao($fetchmode)->getTorrentPeer($id);
    }

    public function getTorrentPeerByPeerID($peer_id, $fetchmode = self::FETCH_MAIN)
    {
        return $this->_getDao($fetchmode)->getTorrentPeerByPeerID($peer_id);
    }

    public function getTorrentPeerByPeerIDAndTorrentID($peer_id, $torrent_id, $fetchmode = self::FETCH_MAIN)
    {
        return $this->_getDao($fetchmode)->getTorrentPeerByPeerIDAndTorrentID($peer_id, $torrent_id);
    }

    public function fetchTorrentPeerByTorrentId($tid, $fetchmode = self::FETCH_MAIN)
    {
        return $this->_getDao($fetchmode)->fetchTorrentPeerByTorrentId($tid);
    }

    public function getTorrentPeerByTorrentIdAndUid($tid, $uid, $fetchmode = self::FETCH_MAIN)
    {
        if (empty($tid) || empty($uid)) {
            return false;
        }

        return $this->_getDao($fetchmode)->getTorrentPeerByTorrentIdAndUid($tid, $uid);
    }

    public function fetchTorrentPeerByUid($uid, $fetchmode = self::FETCH_MAIN)
    {
        return $this->_getDao($fetchmode)->fetchTorrentPeerByUid($uid);
    }

    public function addTorrentPeer(PwTorrentPeerDm $dm)
    {
        if (($result = $dm->beforeAdd()) !== true) {
            return $result;
        }
        return $this->_getDao(self::FETCH_MAIN)->addTorrentPeer($dm->getData());
    }

    public function updateTorrentPeer(PwTorrentPeerDm $dm, $fetchmode = self::FETCH_MAIN)
    {
        if (($result = $dm->beforeUpdate()) !== true) {
            return $result;
        }
        return $this->_getDao($fetchmode)->updateTorrentPeer($dm->id, $dm->getData(), $dm->getIncreaseData());
    }

    public function deleteTorrentPeer($id)
    {
        if (empty($id)) {
            return false;
        }

        return $this->_getDao(self::FETCH_MAIN)->deleteTorrentPeer($id);
    }

    protected function _getDaoMap()
    {
        return array(
            self::FETCH_MAIN => 'EXT:torrent.service.dao.PwTorrentPeerDao',
        );
    }

    protected function _getDao($fetchmode = self::FETCH_MAIN)
    {
        return Wekit::loadDaoFromMap($fetchmode, $this->_getDaoMap(), 'PwTorrentPeer');
    }
}
