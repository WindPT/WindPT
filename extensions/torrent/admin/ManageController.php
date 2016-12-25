<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('ADMIN:library.AdminBaseController');

class ManageController extends AdminBaseController
{
    private $config;

    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);

        $this->config = $this->_loadConfigService()->getValues('site');
    }

    public function run()
    {
        $cronList['ClearPeers']    = $this->_loadCronService()->getCronByFile('PwCronDoClearPeers');
        $cronList['ClearTorrents'] = $this->_loadCronService()->getCronByFile('PwCronDoClearTorrents');

        $this->setOutput($this->config, 'config');
        $this->setOutput($cronList, 'cronList');
    }

    public function agentAction()
    {
        $allowedClients = Wekit::load('EXT:torrent.service.PwTorrentAgent')->fetchTorrentAgent();
        $this->setOutput($this->config, 'config');
        $this->setOutput($allowedClients, 'allowedClients');
    }

    public function typeAction()
    {
        $forums = $this->_loadForumService()->getCommonForumList(PwForum::FETCH_MAIN | PwForum::FETCH_EXTRA);
        foreach ($forums as $key => $forum) {
            $forumset = unserialize($forum['settings_basic']);
            if ($forum['type'] == 'forum' && in_array('torrent', $forumset['allowtype'])) {
                $forums[$key]['topic_types'] = $this->_loadTopicTypeService()->getTypesByFid($forum['fid']);
            } else {
                unset($forums[$key]);
            }
        }
        reset($forums);
        $this->setOutput($this->config, 'config');
        $this->setOutput($forums, 'forums');
    }

    public function creditAction()
    {
        Wind::import('SRV:credit.bo.PwCreditBo');
        $creditType = PwCreditBo::getInstance()->cType;
        $this->setOutput($this->config, 'config');
        $this->setOutput($creditType, 'creditType');
    }

    public function dorunAction()
    {
        list($showuserinfo, $showpeers, $titlegenenabled, $titlegendouban, $titlegenomdb, $check, $deniedfts, $trackerserver, $torrentnameprefix, $peertimeout, $torrentimeout) = $this->getInput(array('showuserinfo', 'showpeers', 'titlegenenabled', 'titlegendouban', 'titlegenomdb', 'check', 'deniedfts', 'trackerserver', 'torrentnameprefix', 'peertimeout', 'torrentimeout'), 'post');

        if (is_array($deniedfts)) {
            foreach ($deniedfts as $key => $value) {
                if (empty($value)) {
                    continue;
                }

                $_deniedfts[$key] = $value;
            }
        }

        if (empty($torrentnameprefix)) {
            $torrentnameprefix = Wekit::C('site', 'info.name');
        }

        if (intval($peertimeout) < 15) {
            $peertimeout = 15;
        }

        $config = new PwConfigSet('site');
        $config->set('app.torrent.showuserinfo', $showuserinfo)->set('app.torrent.showpeers', $showpeers)->set('app.torrent.titlegen.enabled', $titlegenenabled)->set('app.torrent.titlegen.douban', $titlegendouban)->set('app.torrent.titlegen.omdb', $titlegenomdb)->set('app.torrent.check', $check)->set('app.torrent.trackerserver', $trackerserver)->set('app.torrent.torrentnameprefix', $torrentnameprefix)->set('app.torrent.cron.peertimeout', intval($peertimeout))->set('app.torrent.cron.torrentimeout', intval($torrentimeout));

        if (!empty($deniedfts)) {
            $config->set('app.torrent.deniedfts', $_deniedfts);
        }

        $config->flush();

        $this->showMessage('ADMIN:success');
    }

    public function doagentAction()
    {
        $PwTorrentAgentDs = Wekit::load('EXT:torrent.service.PwTorrentAgent');
        if ($this->getInput('act', 'post') == 'delete') {
            $PwTorrentAgentDs->deleteTorrentAgent($this->getInput('id', 'post'));
        } else {
            Wind::import('EXT:torrent.service.dm.PwTorrentAgentDm');

            list($allowedClients, $newAllowedClients) = $this->getInput(array('allowedClients', 'newAllowedClients'), 'post');

            if (is_array($allowedClients)) {
                foreach ($allowedClients as $key => $allowedClient) {
                    if (empty($allowedClient['family']) || empty($allowedClient['agent_pattern'])) {
                        continue;
                    }

                    $dm = new PwTorrentAgentDm($key);
                    $dm->setFamily($allowedClient['family'])->setPeeridPattern($allowedClient['peer_id_pattern'])->setAgentPattern($allowedClient['agent_pattern'])->setHttps($allowedClient['https']);
                    $PwTorrentAgentDs->updateTorrentAgent($dm);
                }
            }

            if (is_array($newAllowedClients)) {
                foreach ($newAllowedClients as $key => $allowedClient) {
                    if (empty($allowedClient['family']) || empty($allowedClient['agent_pattern'])) {
                        continue;
                    }

                    $dm = new PwTorrentAgentDm();
                    $dm->setFamily($allowedClient['family'])->setPeeridPattern($allowedClient['peer_id_pattern'])->setAgentPattern($allowedClient['agent_pattern'])->setHttps($allowedClient['https']);
                    $PwTorrentAgentDs->addTorrentAgent($dm);
                }
            }
        }

        $this->showMessage('ADMIN:success');
    }

    public function docreditAction()
    {
        $credits = $this->getInput('credits', 'post');

        if (is_array($credits)) {
            $credits = array_filter($credits, function ($var) {
                return is_array($var) && ($var['enabled'] == 1) && !empty($var['exp']);
            });

            $config = new PwConfigSet('site');
            $config->set('app.torrent.credits', $credits)->flush();
        }

        $this->showMessage('ADMIN:success');
    }

    public function dotypeAction()
    {
        $bind = $this->getInput('bind', 'post');

        $config = new PwConfigSet('site');
        $config->set('app.torrent.typebind', $bind)->flush();

        $this->showMessage('ADMIN:success');
    }

    private function _loadConfigService()
    {
        return Wekit::load('config.PwConfig');
    }

    private function _loadCronService()
    {
        return Wekit::load('cron.PwCron');
    }

    private function _loadForumService()
    {
        return Wekit::load('forum.PwForum');
    }

    private function _loadTopicTypeService()
    {
        return Wekit::load('forum.PwTopicType');
    }
}
