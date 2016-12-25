<?php

defined('WEKIT_VERSION') || exit('Forbidden');

class PwTorrentUser
{
    const FETCH_MAIN = 1;

    public function getTorrentUserByUid($uid, $fetchmode = self::FETCH_MAIN)
    {
        return $this->_getDao($fetchmode)->getTorrentUserByUid($uid);
    }

    public function getTorrentUserByPasskey($passkey, $fetchmode = self::FETCH_MAIN)
    {
        return $this->_getDao($fetchmode)->getTorrentUserByPasskey($passkey);
    }

    public function addTorrentUser(PwTorrentUserDm $dm)
    {
        if (($result = $dm->beforeAdd()) !== true) {
            return $result;
        }

        return $this->_getDao(self::FETCH_MAIN)->addTorrentUser($dm->getData());
    }

    public function updateTorrentUser(PwTorrentUserDm $dm, $fetchmode = self::FETCH_MAIN)
    {
        if (($result = $dm->beforeUpdate()) !== true) {
            return $result;
        }

        return $this->_getDao($fetchmode)->updateTorrentUser($dm->id, $dm->getData(), $dm->getIncreaseData());
    }

    public function deleteTorrentUser($id)
    {
        if (empty($id)) {
            return false;
        }

        return $this->_getDao(self::FETCH_MAIN)->deleteTorrentUser($id);
    }

    protected function _getDaoMap()
    {
        return array(
            self::FETCH_MAIN => 'EXT:torrent.service.dao.PwTorrentUserDao',
        );
    }

    protected function _getDao($fetchmode = self::FETCH_MAIN)
    {
        return Wekit::loadDaoFromMap($fetchmode, $this->_getDaoMap(), 'PwTorrentUser');
    }
}
