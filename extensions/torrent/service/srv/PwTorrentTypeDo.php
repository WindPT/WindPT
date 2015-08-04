<?php
defined('WEKIT_VERSION') or exit(403);
class PwTorrentTypeDo {
    public function typeOfTorrent($tType) {
        $tType['torrent'] = array('种子贴', '发布种子资源', true);
        return $tType;
    }
}
?>