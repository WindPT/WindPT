<?php

defined('WEKIT_VERSION') || exit('Forbidden');

class PwTorrentDao extends PwBaseDao
{
    protected $_table = 'app_torrents';
    protected $_pk = 'id';
    protected $_dataStruct = array('id', 'tid', 'info_hash', 'filename', 'save_as', 'size', 'created_at', 'type', 'leechers', 'seeders', 'updated_at', 'owner', 'nfo', 'anonymous', 'wikilink');

    public function getTorrent($id)
    {
        return $this->_get($id);
    }

    public function getTorrentByTid($tid)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE tid = ?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne(array($tid));
    }

    public function getTorrentByInfoHash($info_hash)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE binary info_hash = ?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne(array($info_hash));
    }

    public function fetchTorrentByUid($uid)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE owner = ?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll(array($uid));
    }

    public function fetchTorrent()
    {
        $sql = $this->_bindTable('SELECT * FROM %s');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll();
    }

    public function addTorrent($fields)
    {
        return $this->_add($fields);
    }

    public function updateTorrent($id, $fields, $increaseFields = array())
    {
        return $this->_update($id, $fields);
    }

    public function deleteTorrent($id)
    {
        return $this->_delete($id);
    }
}
