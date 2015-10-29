<?php

defined('WEKIT_VERSION') || exit('Forbidden');

class PwSpaceNavDoTorrent
{
    public function appDo($space, $src)
    {
        echo '<li><a href="' . WindUrlHelper::createUrl('/app/torrent/index/my') . '">种子订阅</a></li>';
    }
}
