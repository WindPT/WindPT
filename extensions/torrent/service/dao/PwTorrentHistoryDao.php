<?php

defined('WEKIT_VERSION') || exit('Forbidden');

class PwTorrentHistoryDao extends PwBaseDao
{
    protected $_table = 'app_torrent_history';
    protected $_pk = 'id';
    protected $_dataStruct = array('id', 'uid', 'torrent', 'uploaded', 'uploaded_last', 'downloaded', 'downloaded_last', 'status');

    public function getTorrentHistory($id)
    {
        return $this->_get($id);
    }

    public function fetchTorrentHistoryByTorrent($torrent)
    {
        $sql = $this->_bindTable("SELECT * FROM %s WHERE torrent = ?");
        $smt = $this->getConnection()->createStatement($sql);
        return $smt->queryAll(array($torrent));
    }

    public function fetchTorrentHistoryByUid($uid)
    {
        $sql = $this->_bindTable("SELECT * FROM %s WHERE uid = ?");
        $smt = $this->getConnection()->createStatement($sql);
        return $smt->queryAll(array($uid));
    }

    public function getTorrentHistoryByTorrentAndUid($torrent, $uid)
    {
        $sql = $this->_bindTable("SELECT * FROM %s WHERE torrent = ? AND uid = ?");
        $smt = $this->getConnection()->createStatement($sql);
        return $smt->getOne(array($torrent, $uid));
    }

    public function addTorrentHistory($fields)
    {
        return $this->_add($fields);
    }

    public function updateTorrentHistory($id, $fields, $increaseFields = array())
    {
        return $this->_update($id, $fields);
    }

    public function deleteTorrentHistory($id)
    {
        return $this->_delete($id);
    }
}
