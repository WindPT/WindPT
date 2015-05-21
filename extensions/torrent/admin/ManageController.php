<?php
defined('WEKIT_VERSION') or exit(403);
Wind::import('ADMIN:library.AdminBaseController');
class ManageController extends AdminBaseController
{

    public function beforeAction($handlerAdapter) {
        parent::beforeAction($handlerAdapter);
    }

    public function run() {
        $service = $this->_loadConfigService();
        $config = $service->getValues('site');
        $cronDs = Wekit::load('SRV:cron.PwCron');
        $cronList['ClearPeers'] = $cronDs->getCronByFile('PwCronDoClearPeers');
        $cronList['ClearTorrents'] = $cronDs->getCronByFile('PwCronDoClearTorrents');
        $config['app.torrent.pt_threads'] = implode(',', $config['app.torrent.pt_threads']);
        $this->setOutput($config, 'config');
        $this->setOutput($cronList, 'cronList');
    }

    public function creditAction() {
        $service = $this->_loadConfigService();
        $config = $service->getValues('site');
        Wind::import('SRV:credit.bo.PwCreditBo');
        $creditType = PwCreditBo::getInstance()->cType;
        $this->setOutput($config, 'config');
        $this->setOutput($creditType, 'creditType');
    }

    public function agentAction() {
        $service = $this->_loadConfigService();
        $config = $service->getValues('site');
        $allowedClients = Wekit::load('EXT:torrent.service.PwTorrentAgent')->fetchTorrentAgent();
        $this->setOutput($config, 'config');
        $this->setOutput($allowedClients, 'allowedClients');
    }

    public function themeAction() {
        $service = $this->_loadConfigService();
        $config = $service->getValues('site');
        if ($config['theme.site.default'] == 'pt') {
            $this->setOutput($config, 'config');
        } else {
            $this->setTemplate('');
            echo '必须使用 PT 专用主题才能进行设置。';
        }
    }

    public function dorunAction() {
        list($pt_threads, $showuserinfo, $titlegenifopen, $titlegendouban, $check, $deniedfts, $torrentnameprefix, $peertimeout, $torrentimeout) = $this->getInput(array('pt_threads', 'showuserinfo', 'titlegenifopen', 'titlegendouban', 'check', 'deniedfts', 'torrentnameprefix', 'peertimeout', 'torrentimeout'), 'post');
        $pt_threads = explode(',', $pt_threads);
        foreach ($pt_threads as $key => $value) {
            $pt_threads[$key] = intval($value);
        }
        foreach ($deniedfts as $key => $value) {
            if (empty($value)) continue;
            $_deniedfts[$key] = $value;
        }
        if (empty($torrentnameprefix)) $torrentnameprefix = Wekit::C('site', 'info.name');
        if (intval($peertimeout) < 15) $peertimeout = 15;
        $config = new PwConfigSet('site');
        $config->set('app.torrent.pt_threads', $pt_threads)->set('app.torrent.showuserinfo', $showuserinfo)->set('app.torrent.titlegen.ifopen', $titlegenifopen)->set('app.torrent.titlegen.douban', $titlegendouban)->set('app.torrent.check', $check)->set('app.torrent.torrentnameprefix', $torrentnameprefix)->set('app.torrent.cron.peertimeout', intval($peertimeout))->set('app.torrent.cron.torrentimeout', intval($torrentimeout));
        if (!empty($deniedfts)) $config->set('app.torrent.deniedfts', $_deniedfts);
        $config->flush();
        $this->showMessage('ADMIN:success');
    }

    public function docreditAction() {
        list($creditifopen, $credits, $calfunc, $calcmd) = $this->getInput(array('creditifopen', 'credits', 'calfunc', 'calcmd'), 'post');
        $_calcmd = array('expr', 'bc', 'dc');
        $_credits = array();
        !$credits && $credits = array();
        foreach ($credits as $key => $credit) {
            if (!$credit['enabled'] || empty($credit['func'])) continue;
            $_credits[$key] = $credit;
        }
        if ($calfunc == 'exec' && !in_array($calcmd, $_calcmd)) $calcmd = 'bc';
        if ($calfunc == 'curl' && in_array($calcmd, $_calcmd)) $calcmd = '';
        $config = new PwConfigSet('site');
        $config->set('app.torrent.creditifopen', intval($creditifopen))->set('app.torrent.credits', $_credits)->set('app.torrent.calfunc', $calfunc)->set('app.torrent.calcmd', $calcmd)->flush();
        $this->showMessage('ADMIN:success');
    }

    public function doagentAction() {
        $PwTorrentAgentDs = Wekit::load('EXT:torrent.service.PwTorrentAgent');
        if ($this->getInput('act', 'post') == 'delete') {
            $PwTorrentAgentDs->deleteTorrentAgent($this->getInput('id', 'post'));
        } else {
            Wind::import('EXT:torrent.service.dm.PwTorrentAgentDm');
            list($allowedClients, $newAllowedClients) = $this->getInput(array('allowedClients', 'newAllowedClients'), 'post');
            foreach ($allowedClients as $key => $allowedClient) {
                if (empty($allowedClient['family']) || empty($allowedClient['agent_pattern'])) continue;
                $dm = new PwTorrentAgentDm($key);
                $dm->setFamily($allowedClient['family'])->setPeeridPattern($allowedClient['peer_id_pattern'])->setAgentPattern($allowedClient['agent_pattern'])->setAllowHttps($allowedClient['allowhttps']);
                $PwTorrentAgentDs->updateTorrentAgent($dm);
            }
            foreach ($newAllowedClients as $key => $allowedClient) {
                if (empty($allowedClient['family']) || empty($allowedClient['agent_pattern'])) continue;
                $dm = new PwTorrentAgentDm();
                $dm->setFamily($allowedClient['family'])->setPeeridPattern($allowedClient['peer_id_pattern'])->setAgentPattern($allowedClient['agent_pattern'])->setAllowHttps($allowedClient['allowhttps']);
                $PwTorrentAgentDs->addTorrentAgent($dm);
            }
        }
        $this->showMessage('ADMIN:success');
    }

    public function dothemeAction() {
        list($qmenuifopen, $showpeers) = $this->getInput(array('qmenuifopen', 'showpeers'), 'post');
        $config = new PwConfigSet('site');
        $config->set('app.torrent.theme.qmenuifopen', intval($qmenuifopen))->set('app.torrent.theme.showpeers', $showpeers);
        $config->flush();
        $this->showMessage('ADMIN:success');
    }

    private function _loadConfigService() {
        return Wekit::load('config.PwConfig');
    }
}
?>
