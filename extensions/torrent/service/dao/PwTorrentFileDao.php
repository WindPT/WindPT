<?php

defined('WEKIT_VERSION') || exit('Forbidden');

class PwTorrentFileDao extends PwBaseDao
{
    protected $_table = 'app_torrent_files';
    protected $_pk = 'id';
    protected $_dataStruct = array('id', 'torrent_id', 'filename', 'size');

    public function getTorrentFile($id)
    {
        return $this->_get($id);
    }

    public function getTorrentFileByTorrentId($id)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE torrent_id = ?', $this->getTable());
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll(array($id), 'id');
    }

    public function addTorrentFile($fields)
    {
        return $this->_add($fields);
    }

    public function updateTorrentFile($id, $fields, $increaseFields = array())
    {
        return $this->_update($id, $fields);
    }

    public function deleteTorrentFile($id)
    {
        return $this->_delete($id);
    }
}
