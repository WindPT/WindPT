<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:base.PwBaseDm');

class PwTorrentHistoryDm extends PwBaseDm
{
    public $id;

    public function __construct($id = 0)
    {
        $this->id = $id;
    }

    public function setUid($uid)
    {
        $this->_data['uid'] = $uid;
        return $this;
    }

    public function setTorrentId($torrent_id)
    {
        $this->_data['torrent_id'] = $torrent_id;
        return $this;
    }

    public function setUploaded($uploaded)
    {
        $this->_data['uploaded'] = $uploaded;
        return $this;
    }

    public function setUploadedLast($uploaded_last)
    {
        $this->_data['uploaded_last'] = $uploaded_last;
        return $this;
    }

    public function setDownloaded($downloaded)
    {
        $this->_data['downloaded'] = $downloaded;
        return $this;
    }

    public function setDownloadedLast($downloaded_last)
    {
        $this->_data['downloaded_last'] = $downloaded_last;
        return $this;
    }

    public function setStatus($status)
    {
        $this->_data['status'] = $status;
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
