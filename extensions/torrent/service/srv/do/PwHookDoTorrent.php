<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('EXT:torrent.service.srv.helper.PwUtils');

class PwHookDoTorrent
{
    public function headerInfo1()
    {
        $showuserinfo = Wekit::C('site', 'app.torrent.showuserinfo');

        if (is_array($showuserinfo) && !in_array('headerinfo', $showuserinfo)) {
            return;
        }

        $user = Wekit::getLoginUser();

        if ($user) {
            $peers     = Wekit::load('EXT:torrent.service.PwTorrentPeer')->fetchTorrentPeerByUid($user->uid);
            $histories = Wekit::load('EXT:torrent.service.PwTorrentHistory')->fetchTorrentHistoryByUid($user->uid);

            $seeding = $leeching = 0;
            if (is_array($peers)) {
                foreach ($peers as $peer) {
                    if ($peer['seeder'] == 'yes') {
                        $seeding++;
                    } else {
                        $leeching++;
                    }
                }
            }

            if (is_array($histories)) {
                foreach ($histories as $history) {
                    $downloaded_total += $history['downloaded'];
                    $uploaded_total += $history['uploaded'];
                }
            }

            if ($downloaded_total != 0) {
                $rotio = round($uploaded_total / $downloaded_total, 2);
            } else {
                $rotio = 'Inf.';
            }

            echo '<a href="' . WindUrlHelper::createUrl('space/profile/run?uid=' . $user->uid) . '">下载：' . $leeching . ' 做种：' . $seeding . ' 分享率：' . $rotio . '</a>';
        }
    }

    public function pwCreditOperationConfig($config)
    {
        if (!is_array($config) || empty($config)) {
            return $config;
        }

        $config['app_torrent'] = array('PT', 'global', '种子 {$torrent}', false);

        return $config;
    }

    public function pwThreadsDaoBatchDelete($ids)
    {
        $torrentDs = Wekit::load('EXT:torrent.service.PwTorrent');
        $fileDs    = Wekit::load('EXT:torrent.service.PwTorrentFile');

        if (is_array($ids)) {
            foreach ($ids as $id) {
                $torrent = $torrentDs->getTorrentByTid($id);
                $files   = $fileDs->getTorrentFileByTorrent($torrent['id']);

                if (is_array($files)) {
                    foreach ($files as $file) {
                        $fileDs->deleteTorrentFile($file['id']);
                    }
                }

                $torrentDs->deleteTorrent($torrent['id']);

                @unlink(WEKIT_PATH . '../torrent/' . $torrent['id'] . '.torrent');
            }
        }
    }

    public function pwThreadsDaoDelete($id)
    {
        $torrentDs = Wekit::load('EXT:torrent.service.PwTorrent');
        $fileDs    = Wekit::load('EXT:torrent.service.PwTorrentFile');

        $torrent = $torrentDs->getTorrentByTid($id);
        $files   = $fileDs->getTorrentFileByTorrent($torrent['id']);

        if (is_array($files)) {
            foreach ($files as $file) {
                $fileDs->deleteTorrentFile($file['id']);
            }
        }

        $torrentDs->deleteTorrent($torrent['id']);

        @unlink(WEKIT_PATH . '../torrent/' . $torrent['id'] . '.torrent');
    }

    public function pwThreadType($tType)
    {
        $tType['torrent'] = array('种子贴', '发布种子资源', true);
        return $tType;
    }

    public function spaceNav($space, $src)
    {
        if ($space->visitUid == $space->spaceUid) {
            echo '<li class="' . Pw::isCurrent($src == 'my') . '"><a href="' . WindUrlHelper::createUrl('/app/torrent/index/my') . '">种子订阅</a></li>';
        }
    }

    public function spaceProfile($space)
    {
        $showuserinfo = Wekit::C('site', 'app.torrent.showuserinfo');

        if (is_array($showuserinfo) && !in_array('profile', $showuserinfo)) {
            return;
        }

        $user = Wekit::load('EXT:torrent.service.PwTorrentUser')->getTorrentUserByUid($space->spaceUid);

        $peers           = Wekit::load('EXT:torrent.service.PwTorrentPeer')->fetchTorrentPeerByUid($space->spaceUid);
        $this->histories = Wekit::load('EXT:torrent.service.PwTorrentHistory')->fetchTorrentHistoryByUid($space->spaceUid);
        $this->torrents  = $this->_getThreadService()->getThreadByUid($space->spaceUid);

        $this->passkey = $user['passkey'];

        $this->seeding = $this->leeching = 0;
        if (is_array($peers)) {
            foreach ($peers as $peer) {
                if ($peer['seeder'] == 'yes') {
                    $this->seeding++;
                } else {
                    $this->leeching++;
                }
            }
        }

        if (is_array($this->histories)) {
            $PwTorrent = Wekit::load('EXT:torrent.service.PwTorrent');
            foreach ($this->histories as $key => $history) {
                $downloaded_total += $history['downloaded'];
                $uploaded_total += $history['uploaded'];

                $torrent = $PwTorrent->getTorrent($history['torrent']);
                $thread  = $this->_getThreadService()->getThread($torrent['tid']);

                if ($thread) {
                    $this->histories[$key]['tid']     = $torrent['tid'];
                    $this->histories[$key]['subject'] = $thread['subject'];
                } else {
                    unset($this->histories[$key]);
                }
            }
        }

        if ($downloaded_total != 0) {
            $this->rotio = round($uploaded_total / $downloaded_total, 2);
        } else {
            $this->rotio = 'Inf.';
        }

        $this->downloaded_total = PwUtils::readableDataTransfer($downloaded_total);
        $this->uploaded_total   = PwUtils::readableDataTransfer($uploaded_total);

        PwHook::template('displayProfileTorrentHtml', 'EXT:torrent.template.profile_injector_after_content', true, $this);
    }

    private function _getThreadService()
    {
        return Wekit::load('forum.PwThread');
    }
}
