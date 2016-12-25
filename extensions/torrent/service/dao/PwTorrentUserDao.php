<?php

defined('WEKIT_VERSION') || exit('Forbidden');

class PwTorrentUserDao extends PwBaseDao
{
    protected $_table = 'app_torrent_users';
    protected $_pk = 'uid';
    protected $_dataStruct = array('uid', 'passkey');

    public function getTorrentUserByUid($uid)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE uid=?', $this->getTable());
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne(array($uid), 'uid');
    }

    public function getTorrentUserByPasskey($passkey)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE passkey=?', $this->getTable());
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne(array($passkey), 'uid');
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
