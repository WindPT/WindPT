<?php
defined('WEKIT_VERSION') or exit(403);
Wind::import('SRV:forum.srv.threadDisplay.do.PwThreadDisplayDoBase');
/**
 * 帖子内容展示
 *
 * @author 7IN0SAN9 <me@7in0.me>
 * @copyright http://7in0.me
 * @license http://7in0.me
 */
class App_TorrentPwThreadDisplayDo extends PwThreadDisplayDoBase {
	public function __construct($srv) {

	}

	public function createHtmlAfterUserInfo($user, $read) {
    $config = require(realpath(dirname(__FILE__)).'/../../../../../conf/database.php');
    $dbHandle = new PDO ( $config['dsn'], $config['user'], $config['pwd'] );
    $dbHandle->setAttribute ( PDO::ATTR_EMULATE_PREPARES, false );

    $sql = 'SELECT SUM(downloaded) AS downloaded_total, SUM(uploaded) AS uploaded_total FROM pw_app_torrent_history WHERE uid = :id';
    $sth = @$dbHandle->prepare ( $sql );
    @$sth->execute ( array(':id'=>$user['uid']) );
    $result = @$sth->fetch ( PDO::FETCH_ASSOC );
    if (is_array ( $result )) {
      $downloaded_total = floor($result ['downloaded_total']/1048567);
      $uploaded_total = floor($result ['uploaded_total']/1048567);
      
      if ($downloaded_total != 0)
        $rotio = round($result ['uploaded_total']/$result ['downloaded_total'], 2);
      else
        $rotio = 'Inf.';
    }
    echo '<div id="PTInfo">下载： ' . $downloaded_total . ' M<br>上传： ' . $uploaded_total . ' M<br>分享率： ' . $rotio . '</div>';
	}

	public function runJs() {
    
	}
}
?>