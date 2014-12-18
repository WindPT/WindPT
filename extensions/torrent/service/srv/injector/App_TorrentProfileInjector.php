<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRC:library.engine.hook.PwBaseHookInjector');
Wind::import('EXT:torrent.service.srv.do.App_TorrentProfileDo');

class App_TorrentProfileInjector extends PwBaseHookInjector{
    
	public function createHtml(){
		$user = Wekit::getLoginUser();
		$bp = new PwUserProfileExtends($user);
		return new App_TorrentProfileDo($bp);
	}
	
}