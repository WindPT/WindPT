<?php
defined('WEKIT_VERSION') or exit(403);
Wind::import('ADMIN:library.AdminBaseController');
Wind::import('SRV:credit.bo.PwCreditBo');
class ManageController extends AdminBaseController
{
    
    public function beforeAction($handlerAdapter) {
        parent::beforeAction($handlerAdapter);
    }
    
    public function run() {
        $service = $this->_loadConfigService();
        $config = $service->getValues('site');
        $this->setOutput($config, 'config');
    }
    
    public function creditAction() {
        $service = $this->_loadConfigService();
        $config = $service->getValues('site');
        $creditType = PwCreditBo::getInstance()->cType;
        $this->setOutput($config, 'config');
        $this->setOutput($creditType, 'creditType');
    }
    
    public function dorunAction() {
        list($deniedfts, $showuserinfo) = $this->getInput(array('deniedfts', 'showuserinfo'), 'post');
        $config = new PwConfigSet('site');
        $config->set('app.torrent.deniedfts', $deniedfts)->set('app.torrent.showuserinfo', $showuserinfo)->flush();
        $this->showMessage('ADMIN:success');
    }
    
    public function docreditAction() {
        list($creditifopen, $credits) = $this->getInput(array('creditifopen', 'credits'), 'post');
        $_credits = array();
        !$credits && $credits = array();
        foreach ($credits as $key => $credit) {
            if (!$credit['enabled'] || empty($credit['func'])) continue;
            $_credits[$key] = $credit;
        }
        $config = new PwConfigSet('site');
        $config->set('app.torrent.creditifopen', intval($creditifopen))->set('app.torrent.credits', $_credits)->flush();
        $this->showMessage('ADMIN:success');
    }
    
    private function _loadConfigService() {
        return Wekit::load('config.PwConfig');
    }
}
?>