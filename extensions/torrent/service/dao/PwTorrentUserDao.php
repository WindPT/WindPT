<?php

defined('WEKIT_VERSION') || exit('Forbidden');

class PwTorrentUserDao extends PwBaseDao
{
    protected $_table = 'app_torrent_user';
    protected $_pk = 'id';
    protected $_dataStruct = array('id', 'uid', 'passkey');

    public function getTorrentUser($id)
    {
        return $this->_get($id);
    }

    public function getTorrentUserByUid($uid)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE uid=?', $this->getTable());
        $smt = $this->getConnection()->createStatement($sql);
        return $smt->getOne(array($uid), 'id');
    }

    public function getTorrentUserByPasskey($passkey)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE passkey=?', $this->getTable());
        $smt = $this->getConnection()->createStatement($sql);
        return $smt->getOne(array($passkey), 'id');
    }

    public function addTorrentUser($fields)
    {
        return $this->_add($fields);
    }

    public function updateTorrentUser($id, $fields, $increaseFields = array())
    {
        return $this->_update($id, $fields);
    }

    public function deleteTorrentUser($id)
    {
        return $this->_delete($id);
    }
}
