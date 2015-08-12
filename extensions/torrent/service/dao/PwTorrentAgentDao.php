<?php

defined('WEKIT_VERSION') || exit('Forbidden');

class PwTorrentAgentDao extends PwBaseDao
{
    protected $_table = 'app_torrent_agent_allowed_family';
    protected $_pk = 'id';
    protected $_dataStruct = array('id', 'family', 'peer_id_pattern', 'agent_pattern', 'allowhttps', 'hits');
    public function getTorrentAgent($id)
    {
        return $this->_get($id);
    }
    public function fetchTorrentAgent()
    {
        $sql = $this->_bindTable("SELECT * FROM %s");
        $smt = $this->getConnection()->createStatement($sql);
        return $smt->queryAll();
    }
    public function addTorrentAgent($fields)
    {
        return $this->_add($fields);
    }
    public function updateTorrentAgent($id, $fields, $increaseFields = array())
    {
        return $this->_update($id, $fields);
    }
    public function deleteTorrentAgent($id)
    {
        return $this->_delete($id);
    }
}
