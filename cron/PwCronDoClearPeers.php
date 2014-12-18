<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwCronDoClearOnline.php 18771 2012-09-27 07:47:26Z gao.wanggao $ 
 * @package 
 */
Wind::import('SRV:cron.srv.base.AbstractCronBase');

class PwCronDoClearPeers extends AbstractCronBase{
	
	public function run($cronId) {
  	$config = require(realpath(dirname(__FILE__)).'/../../../../../conf/database.php');
    try {
        $dbHandle = new PDO ( $config['dsn'], $config['user'], $config['pwd'] );
        $dbHandle->setAttribute ( PDO::ATTR_EMULATE_PREPARES, false );

        $sql = 'DELETE from pw_app_torrent_peer WHERE NOW() - last_action > 350';
        $sth = @$dbHandle->prepare ( $sql );
        @$sth->execute ();
    } catch ( PDOException $e ) {
      return NULL;
    }
	}
}
?>