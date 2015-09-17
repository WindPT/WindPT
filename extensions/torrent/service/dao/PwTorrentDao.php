<?php

defined('WEKIT_VERSION') || exit('Forbidden');

class PwTorrentDao extends PwBaseDao
{
    protected $_table = 'app_torrent';
    protected $_pk = 'id';
    protected $_dataStruct = array('id', 'tid', 'info_hash', 'filename', 'save_as', 'processing', 'size', 'added', 'type', 'numfiles', 'times_completed', 'leechers', 'seeders', 'last_action', 'visible', 'banned', 'owner', 'nfo', 'sp_state', 'promotion_time_type', 'promotion_until', 'anonymous', 'wikilink', 'pos_state', 'cache_stamp', 'picktype', 'picktime', 'last_reseed', 'endfree', 'endsticky');

    public function getTorrent($id)
    {
        return $this->_get($id);
    }

    public function getTorrentByTid($tid)
    {
        $sql = $this->_bindTable("SELECT * FROM %s WHERE tid = ?");
        $smt = $this->getConnection()->createStatement($sql);
        return $smt->getOne(array($tid));
    }

    public function fetchTorrentByUid($uid)
    {
        $sql = $this->_bindTable("SELECT * FROM %s WHERE owner = ?");
        $smt = $this->getConnection()->createStatement($sql);
        return $smt->queryAll(array($uid));
    }

    public function fetchTorrent()
    {
        $sql = $this->_bindTable("SELECT * FROM %s");
        $smt = $this->getConnection()->createStatement($sql);
        return $smt->queryAll();
    }

    public function getTorrentByInfoHash($info_hash)
    {
        $sql = $this->_bindTable("SELECT * FROM %s WHERE binary info_hash = ?");
        $smt = $this->getConnection()->createStatement($sql);
        return $smt->getOne(array($info_hash));
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
