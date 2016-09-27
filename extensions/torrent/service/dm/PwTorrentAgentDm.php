<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:base.PwBaseDm');

class PwTorrentAgentDm extends PwBaseDm
{
    public $id;

    public function __construct($id = 0)
    {
        $this->id = $id;
    }

    public function setFamily($family)
    {
        $this->_data['family'] = $family;
        return $this;
    }

    public function setPeeridPattern($peer_id_pattern)
    {
        $this->_data['peer_id_pattern'] = $peer_id_pattern;
        return $this;
    }

    public function setAgentPattern($agent_pattern)
    {
        $this->_data['agent_pattern'] = $agent_pattern;
        return $this;
    }

    public function setHttps($https)
    {
        $this->_data['https'] = $https;
        return $this;
    }

    public function setHits($hits)
    {
        $this->_data['hits'] = $hits;
        return $this;
    }

    protected function _beforeAdd()
    {
        return true;
    }

    protected function _beforeUpdate()
    {
        return true;
    }
}
