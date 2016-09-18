<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:base.PwBaseDm');

class PwTorrentDm extends PwBaseDm
{
    public $id;

    public function __construct($id = 0)
    {
        $this->id = $id;
    }

    public function setTid($tid)
    {
        $this->_data['tid'] = $tid;
        return $this;
    }

    public function setInfoHash($hash)
    {
        $this->_data['info_hash'] = $hash;
        return $this;
    }

    public function setFileName($filename)
    {
        $this->_data['filename'] = $filename;
        return $this;
    }

    public function setSaveAs($save_as)
    {
        $this->_data['save_as'] = $save_as;
        return $this;
    }

    public function setSize($size)
    {
        $this->_data['size'] = $size;
        return $this;
    }

    public function setAdded($added)
    {
        $this->_data['added'] = $added;
        return $this;
    }

    public function setType($type)
    {
        $this->_data['type'] = $type;
        return $this;
    }

    public function setNumfiles($numfiles)
    {
        $this->_data['numfiles'] = $numfiles;
        return $this;
    }

    public function setTimesCompleted($times_completed)
    {
        $this->_data['times_completed'] = $times_completed;
        return $this;
    }

    public function setLeechers($leechers)
    {
        $this->_data['leechers'] = $leechers;
        return $this;
    }

    public function setSeeders($seeders)
    {
        $this->_data['seeders'] = $seeders;
        return $this;
    }

    public function setLastAction($last_action)
    {
        $this->_data['last_action'] = $last_action;
        return $this;
    }

    public function setOwner($owner)
    {
        $this->_data['owner'] = $owner;
        return $this;
    }

    public function setNfo($nfo)
    {
        $this->_data['nfo'] = $nfo;
        return $this;
    }

    public function setAnonymous($anonymous)
    {
        $this->_data['anonymous'] = $anonymous;
        return $this;
    }

    public function setWikilink($wikilink)
    {
        $this->_data['wikilink'] = $wikilink;
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
