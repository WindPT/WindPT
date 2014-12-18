<?php
defined('WEKIT_VERSION') or exit(403);
/**
 * 空间资料页面
 *
 * @author 7IN0SAN9 <me@7in0.me>
 * @copyright http://7in0.me
 * @license http://7in0.me
 */
class App_Torrent_Space_ProfileDo {
	
	/**
	 * @param array $space
	 */
	public function app_TorrentDo($space) {
		$config = require(realpath(dirname(__FILE__)).'/../../../../../conf/database.php');
    $dbHandle = new PDO ( $config['dsn'], $config['user'], $config['pwd'] );
    $dbHandle->setAttribute ( PDO::ATTR_EMULATE_PREPARES, false );
    
    $sql = 'SELECT SUM(downloaded) AS downloaded_total, SUM(uploaded) AS uploaded_total FROM pw_app_torrent_history WHERE uid = :id';
    $sth = @$dbHandle->prepare ( $sql );
    @$sth->execute ( array(':id'=>$space->{'spaceUid'}) );
    $result = @$sth->fetch ( PDO::FETCH_ASSOC );
    if (is_array ( $result )) {
      $downloaded_total = floor($result ['downloaded_total']/1048567);
      $uploaded_total = floor($result ['uploaded_total']/1048567);

      if ($downloaded_total != 0)
        $rotio = round($result ['uploaded_total']/$result ['downloaded_total'], 2);
      else
        $rotio = 'Inf.';
    }
    $sql = 'SELECT passkey from pw_app_torrent_user WHERE uid = :id';
    $sth = @$dbHandle->prepare ( $sql );
    @$sth->execute ( array(':id'=>$space->{'spaceUid'}) );
    $result = @$sth->fetch ( PDO::FETCH_ASSOC );
    $passkey = $result['passkey'];
		echo '<div class="space_profile"><h3><strong>PT个人信息</strong></h3>';
    if ($space->{'visitUid'} == $space->{'spaceUid'})
      echo '<dl class="cc"><dt>Passkey：</dt><dd><span id="passkey" style="background-color:rgb(51,51,51); color:rgb(51,51,51);">'.$passkey.'</span><button id="btnToggle" onclick="if ($(\'#btnToggle\').text() == \'显示\') {$(\'#passkey\').css(\'background\', \'white\'); $(\'#btnToggle\').text(\'隐藏\');} else {$(\'#passkey\').css(\'background\', \'rgb(51,51,51)\');$(\'#btnToggle\').text(\'显示\');}">显示</button><button>重设</button></dd></dl>';
    echo '<dl class="cc"><dt>下载：</dt><dd>'.$downloaded_total.' M</dd></dl>';
    echo '<dl class="cc"><dt>上传：</dt><dd>'.$uploaded_total.' M</dd></dl>';
    echo '<dl class="cc"><dt>分享率：</dt><dd>'.$rotio.'</dd></dl>';
    echo '</div>';
	}
}

?>
