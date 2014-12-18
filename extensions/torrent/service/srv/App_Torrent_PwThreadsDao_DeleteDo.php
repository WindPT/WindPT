<?php
defined('WEKIT_VERSION') or exit(403);

/**
 * 删除一个帖子时，调用
 *
 * @author 7IN0SAN9 <me@7in0.me>
 * @copyright http://7in0.me
 * @license http://7in0.me
 */
class App_Torrent_PwThreadsDao_DeleteDo {
	/**
	 * @param int $id 帖子tid
	 * @return void
	 */
	public function app_TorrentDo($id) {
  	$config = require(realpath(dirname(__FILE__)).'/../../../../../conf/database.php');
    try {
        $dbHandle = new PDO ( $config['dsn'], $config['user'], $config['pwd'] );
        $dbHandle->setAttribute ( PDO::ATTR_EMULATE_PREPARES, false );

        $sql = 'SELECT id from pw_app_torrent WHERE tid = :tid';
        $sth = @$dbHandle->prepare ( $sql );
        @$sth->execute ( array(':tid'=>$id) );
        $torrent = @$sth->fetch(PDO::FETCH_ASSOC);

        $sql = 'DELETE from pw_app_torrent, pw_app_torrent_file USING pw_app_torrent INNER JOIN pw_app_torrent_file ON pw_app_torrent.id = pw_app_torrent_file.torrent WHERE pw_app_torrent.tid = :tid';
        $sth = @$dbHandle->prepare ( $sql );
        @$sth->execute ( array(':tid'=>$id) );
        @unlink('../../../../../torrent/' . $torrent['id'] . '.torrent');

    } catch ( PDOException $e ) {}
	}
}

?>