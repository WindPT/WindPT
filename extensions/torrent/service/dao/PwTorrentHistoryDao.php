<?php

defined('WEKIT_VERSION') || exit('Forbidden');

class PwTorrentHistoryDao extends PwBaseDao
{
    protected $_table      = 'app_torrent_histories';
    protected $_pk         = 'id';
    protected $_dataStruct = array('id', 'uid', 'torrent_id', 'uploaded', 'uploaded_last', 'downloaded', 'downloaded_last', 'left', 'state');

    public function getTorrentHistory($id)
    {
        return $this->_get($id);
    }

    public function getTorrentHistoryByTorrentIdAndUid($torrent, $uid)
    {
        $sql = $this->_bindTable("SELECT * FROM %s WHERE torrent_id = ? AND uid = ?");
        $smt = $this->getConnection()->createStatement($sql);
        return $smt->getOne(array($torrent, $uid));
    }

    public function fetchTorrentHistoryByTorrentId($torrent)
    {
        $sql = $this->_bindTable("SELECT * FROM %s WHERE torrent_id = ?");
        $smt = $this->getConnection()->createStatement($sql);
        return $smt->queryAll(array($torrent));
    }

    public function fetchTorrentHistoryByUid($uid)
    {
        $sql = $this->_bindTable("SELECT * FROM %s WHERE uid = ?");
        $smt = $this->getConnection()->createStatement($sql);
        return $smt->queryAll(array($uid));
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
