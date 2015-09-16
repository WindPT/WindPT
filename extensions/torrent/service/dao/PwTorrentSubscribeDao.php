<?php

defined('WEKIT_VERSION') || exit('Forbidden');

class PwTorrentSubscribeDao extends PwBaseDao
{
    protected $_table = 'app_torrent_subscription';
    protected $_pk = 'id';
    protected $_dataStruct = array('id', 'uid', 'torrent');

    public function getTorrentSubscribe($id)
    {
        return $this->_get($id);
    }

    public function getTorrentSubscribeByUid($uid)
    {
        $sql = $this->_bindSql('SELECT %1$s.id, %1$s.torrent, %2$s.filename, %2$s.size, %3$s.tid, %3$s.fid, %3$s.`subject`, %3$s.created_time, %3$s.created_username, %4$s.`name` FROM %1$s INNER JOIN %2$s ON %2$s.id = %1$s.torrent INNER JOIN %3$s ON %2$s.tid = %3$s.tid INNER JOIN %4$s ON %3$s.fid = %4$s.fid WHERE uid = %5$d', $this->getTable(), $this->getTable('app_torrent'), $this->getTable('bbs_threads'), $this->getTable('bbs_forum'), $uid);
        $rst = $this->getConnection()->query($sql);
        return $rst->fetchAll();
    }

    public function getTorrentSubscribeByUidAndTorrent($uid, $torrent)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE uid = ? AND torrent = ?', $this->getTable());
        $smt = $this->getConnection()->createStatement($sql);
        return $smt->getOne(array($uid, $torrent));
    }

    public function fetchTorrentSubscribe()
    {
        $sql = $this->_bindTable('SELECT * FROM %s');
        $smt = $this->getConnection()->createStatement($sql);
        return $smt->queryAll();
    }

    public function addTorrentSubscribe($fields)
    {
        return $this->_add($fields);
    }

    public function deleteTorrentSubscribe($id)
    {
        return $this->_delete($id);
    }
}
