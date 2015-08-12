<?php

defined('WEKIT_VERSION') || exit('Forbidden');

class PwTorrentTypeDo
{
    public function typeOfTorrent($tType)
    {
        $tType['torrent'] = array('种子贴', '发布种子资源', true);
        return $tType;
    }
}
