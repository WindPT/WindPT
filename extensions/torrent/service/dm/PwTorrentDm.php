<?php
defined('WEKIT_VERSION') || exit('Forbidden');
Wind::import('LIB:base.PwBaseDm');
class PwTorrentDm extends PwBaseDm {
    public $id;
    public function __construct($id = 0) {
        $this->id = $id;
    }
    public function setTid($tid) {
        $this->_data['tid'] = $tid;
        return $this;
    }
    public function setInfoHash($hash) {
        $this->_data['info_hash'] = $hash;
        return $this;
    }
    public function setFileName($filename) {
        $this->_data['filename'] = $filename;
        return $this;
    }
    public function setSaveAs($save_as) {
        $this->_data['save_as'] = $save_as;
        return $this;
    }
    public function setProcessing($processing) {
        $this->_data['processing'] = $processing;
        return $this;
    }
    public function setSize($size) {
        $this->_data['size'] = $size;
        return $this;
    }
    public function setAdded($added) {
        $this->_data['added'] = $added;
        return $this;
    }
    public function setType($type) {
        $this->_data['type'] = $type;
        return $this;
    }
    public function setNumfiles($numfiles) {
        $this->_data['numfiles'] = $numfiles;
        return $this;
    }
    public function setTimesCompleted($times_completed) {
        $this->_data['times_completed'] = $times_completed;
        return $this;
    }
    public function setLeechers($leechers) {
        $this->_data['leechers'] = $leechers;
        return $this;
    }
    public function setSeeders($seeders) {
        $this->_data['seeders'] = $seeders;
        return $this;
    }
    public function setLastAction($last_action) {
        $this->_data['last_action'] = $last_action;
        return $this;
    }
    public function setVisible($visible) {
        $this->_data['visible'] = $visible;
        return $this;
    }
    public function setBanned($banned) {
        $this->_data['banned'] = $banned;
        return $this;
    }
    public function setOwner($owner) {
        $this->_data['owner'] = $owner;
        return $this;
    }
    public function setNfo($nfo) {
        $this->_data['nfo'] = $nfo;
        return $this;
    }
    public function setSpState($sp_state) {
        $this->_data['sp_state'] = $sp_state;
        return $this;
    }
    public function setPromotionTimeType($promotion_time_type) {
        $this->_data['promotion_time_type'] = $promotion_time_type;
        return $this;
    }
    public function setPromotionUntil($promotion_until) {
        $this->_data['promotion_until'] = $promotion_until;
        return $this;
    }
    public function setAnonymous($anonymous) {
        $this->_data['anonymous'] = $anonymous;
        return $this;
    }
    public function setWikilink($wikilink) {
        $this->_data['wikilink'] = $wikilink;
        return $this;
    }
    public function setPosState($pos_state) {
        $this->_data['pos_state'] = $pos_state;
        return $this;
    }
    public function setCacheStamp($cache_stamp) {
        $this->_data['cache_stamp'] = $cache_stamp;
        return $this;
    }
    public function setPickType($picktype) {
        $this->_data['picktype'] = $picktype;
        return $this;
    }
    public function setPickTime($picktime) {
        $this->_data['picktime'] = $picktime;
        return $this;
    }
    public function setLastReseed($last_reseed) {
        $this->_data['last_reseed'] = $last_reseed;
        return $this;
    }
    public function setEndFree($endfree) {
        $this->_data['endfree'] = $endfree;
        return $this;
    }
    public function setEndSticky($endsticky) {
        $this->_data['endsticky'] = $endsticky;
        return $this;
    }
    protected function _beforeAdd() {
        return true;
    }
    protected function _beforeUpdate() {
        return true;
    }
}