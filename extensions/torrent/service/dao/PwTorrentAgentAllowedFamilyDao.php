<?php
defined('WEKIT_VERSION') || exit('Forbidden');
class PwTorrentFileDao extends PwBaseDao {
    protected $_table = 'app_torrent_agent_allowed_family';
    protected $_pk = 'id';
    protected $_dataStruct = array('id', 'family', 'start_name', 'peer_id_pattern', 'peer_id_match_num', 'peer_id_matchtype', 'peer_id_start', 'agent_pattern', 'agent_match_num', 'agent_matchtype', 'agent_start', 'exception', 'allowhttps', 'comment', 'hits');
    public function getTorrentAgent($id) {
        return $this->_get($id);
    }
    public function fetchTorrentAgent() {
        $sql = $this->_bindSql('SELECT * FROM %s', $this->getTable());
        $rst = $this->getConnection()->query($sql);
        return $rst->fetchAll("id");
    }
    public function addTorrentAgent($fields) {
        return $this->_add($fields);
    }
    public function updateTorrentAgent($id, $fields, $increaseFields = array()) {
        return $this->_update($id, $fields);
    }
    public function deleteTorrentAgent($id) {
        return $this->_delete($id);
    }
}