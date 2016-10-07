SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `pw_app_torrents`
-- ----------------------------
DROP TABLE IF EXISTS `pw_app_torrents`;
CREATE TABLE `pw_app_torrents` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned NOT NULL,
  `info_hash` binary(20) NOT NULL,
  `filename` varchar(255) NOT NULL DEFAULT '',
  `save_as` varchar(255) NOT NULL DEFAULT '',
  `size` bigint(20) unsigned NOT NULL DEFAULT '0',
  `type` enum('single','multi') NOT NULL DEFAULT 'single',
  `leechers` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `seeders` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `owner` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `nfo` blob,
  `anonymous` tinyint(1) NOT NULL DEFAULT '0',
  `wikilink` varchar(256) DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `info_hash` (`info_hash`) USING BTREE,
  KEY `app_torrents_tid_foreign` (`tid`) USING BTREE,
  KEY `app_torrents_owner_foreign` (`owner`) USING BTREE
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `pw_app_torrent_agents`
-- ----------------------------
DROP TABLE IF EXISTS `pw_app_torrent_agents`;
CREATE TABLE `pw_app_torrent_agents` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `family` varchar(50) NOT NULL DEFAULT '',
  `peer_id_pattern` varchar(200) NOT NULL,
  `agent_pattern` varchar(200) NOT NULL,
  `https` tinyint(1) NOT NULL DEFAULT '0',
  `hits` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `pw_app_torrent_agents`
-- ----------------------------
BEGIN;
INSERT INTO `pw_app_torrent_agents` VALUES ('1', 'Azureus 2.5.0.4', '^-AZ2504-', '^Azureus 2.5.0.4', '1', '0'), ('2', 'Ktorrent 4.0.x', '^-KT4([0-9])([0-9])([0-9])-', '^KTorrent\\/4\\.([0-9])\\.([0-9])', '1', '0'), ('3', 'Bittorrent 6.x', '^M6-([0-9])-([0-9])--', '^BitTorrent\\/6([0-9])([0-9])([0-9])', '1', '0'), ('4', 'Deluge 0.x', '^-DE0([0-9])([0-9])([0-9])-', '^Deluge 0\\.([0-9])\\.([0-9])\\.([0-9])', '1', '0'), ('5', 'Transmission1.x', '^-TR1([0-9])([0-9])([0-9])-', '^Transmission\\/1\\.([0-9])([0-9])', '1', '0'), ('6', 'RTorrent 0.x(with libtorrent 0.x)', '^-lt([0-9A-E])([0-9A-E])([0-9A-E])([0-9A-E])-', '^rtorrent\\/0\\.([0-9])\\.([0-9])\\/0\\.([1-9][0-9]*)\\.(0|[1-9][0-9]*)', '0', '0'), ('7', 'Rufus 0.x', '', '^Rufus\\/0\\.([0-9])\\.([0-9])', '0', '0'), ('8', 'Azureus 3.x', '^-AZ3([0-9])([0-9])([0-9])-', '^Azureus 3\\.([0-9])\\.([0-9])\\.([0-9])', '1', '0'), ('9', 'uTorrent 1.7.x', '^-UT17([0-9])([0-9])-', '^uTorrent\\/17([0-9])([0-9])', '1', '0'), ('10', 'BitRocket 0.x', '^-BR0([0-9])([1-9][0-9]*)-', '^BitRocket\\/0\\.([0-9])\\.([0-9])\\(([1-9][0-9]*)\\) libtorrent\\/0\\.([1-9][0-9]*)\\.(0|[1-9][0-9]*)\\.(0|[1-9][0-9]*)', '1', '0'), ('11', 'MLDonkey 2.9.x', '^-ML2\\.9\\.([0-9])-', '^MLDonkey\\/2\\.9\\.([0-9])', '1', '0'), ('12', 'uTorrent 1.8.x', '^-UT18([0-9])([0-9])-', '^uTorrent\\/18([0-9])([0-9])', '1', '0'), ('13', 'Azureus 4.x', '^-AZ4([0-9])([0-9])([0-9])-', '^Azureus 4\\.([0-9])\\.([0-9])\\.([0-9])', '1', '0'), ('14', 'SymTorrent', '', '^SymTorrent', '0', '0'), ('15', 'Deluge 1.x', '^-DE1([0-9])([0-9A-Z])([0-9])-', '^Deluge 1\\.([0-9])\\.([0-9])', '1', '0'), ('16', 'uTorrent 1.8xB', '^-UT18([0-9])B-', '^uTorrent\\/18([0-9])B\\(([1-9][0-9]*)\\)', '1', '0'), ('17', 'uTorrent 2.x.x', '^-UT2([0-9])([0-9])([0-9])-', '^uTorrent\\/2([0-9])([0-9])([0-9])', '1', '0'), ('18', 'uTorrent 3.x.xB', '^-UT3([0-9])([0-9])B-', '^uTorrent\\/3([0-9])([0-9])B', '1', '0'), ('19', 'uTorrent 3.x', '^-UT3([0-9])([0-9])([0-9])-', '^uTorrent\\/3([0-9])([0-9])([0-9])', '1', '0'), ('20', 'uTorrentMac 1.x', '^-UM1([0-9])([0-9])([0-9])-', '^uTorrentMac\\/1([0-9])([0-9])([0-9])', '1', '0');
COMMIT;

-- ----------------------------
--  Table structure for `pw_app_torrent_users`
-- ----------------------------
DROP TABLE IF EXISTS `pw_app_torrent_users`;
CREATE TABLE `pw_app_torrent_users` (
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `passkey` varchar(40) NOT NULL DEFAULT '',
  `uploaded_mo` bigint(20) unsigned NOT NULL DEFAULT '0',
  `downloaded_mo` bigint(20) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `passkey` (`passkey`) USING BTREE,
  UNIQUE KEY `app_torrent_users_uid_foreign` (`uid`) USING BTREE,
  CONSTRAINT `app_torrent_users_uid_foreign` FOREIGN KEY (`uid`) REFERENCES `pw_user` (`uid`)
) DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `pw_app_torrent_files`
-- ----------------------------
DROP TABLE IF EXISTS `pw_app_torrent_files`;
CREATE TABLE `pw_app_torrent_files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `torrent_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `filename` varchar(255) NOT NULL DEFAULT '',
  `size` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `app_torrent_files_torrent_id_foreign` (`torrent_id`) USING BTREE,
  CONSTRAINT `app_torrent_files_torrent_id_foreign` FOREIGN KEY (`torrent_id`) REFERENCES `pw_app_torrents` (`id`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `pw_app_torrent_histories`
-- ----------------------------
DROP TABLE IF EXISTS `pw_app_torrent_histories`;
CREATE TABLE `pw_app_torrent_histories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `torrent_id` mediumint(8) unsigned NOT NULL,
  `uploaded` bigint(20) unsigned NOT NULL DEFAULT '0',
  `uploaded_last` bigint(20) NOT NULL DEFAULT '0',
  `downloaded` bigint(20) unsigned NOT NULL DEFAULT '0',
  `downloaded_last` bigint(20) NOT NULL DEFAULT '0',
  `left` bigint(20) unsigned NOT NULL DEFAULT '0',
  `state` enum('started','stopped') NOT NULL DEFAULT 'started',
  PRIMARY KEY (`id`),
  KEY `app_torrent_historys_uid_foreign` (`uid`) USING BTREE,
  KEY `app_torrent_historys_torrent_id_foreign` (`torrent_id`) USING BTREE,
  CONSTRAINT `app_torrent_historys_torrent_id_foreign` FOREIGN KEY (`torrent_id`) REFERENCES `pw_app_torrents` (`id`),
  CONSTRAINT `app_torrent_historys_uid_foreign` FOREIGN KEY (`uid`) REFERENCES `pw_user` (`uid`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `pw_app_torrent_peers`
-- ----------------------------
DROP TABLE IF EXISTS `pw_app_torrent_peers`;
CREATE TABLE `pw_app_torrent_peers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `torrent_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `ip` varbinary(64) NOT NULL DEFAULT '',
  `peer_id` binary(20) NOT NULL,
  `port` smallint(5) unsigned NOT NULL DEFAULT '0',
  `uploaded` bigint(20) unsigned NOT NULL DEFAULT '0',
  `downloaded` bigint(20) unsigned NOT NULL DEFAULT '0',
  `left` bigint(20) unsigned NOT NULL DEFAULT '0',
  `seeder` tinyint(1) NOT NULL DEFAULT '0',
  `connectable` tinyint(1) NOT NULL DEFAULT '1',
  `agent` varchar(60) NOT NULL DEFAULT '',
  `started_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `finished_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_action` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `peer_id` (`peer_id`) USING BTREE,
  KEY `app_torrent_peers_torrent_id_foreign` (`torrent_id`) USING BTREE,
  KEY `app_torrent_peers_uid_foreign` (`uid`) USING BTREE,
  CONSTRAINT `app_torrent_peers_torrent_id_foreign` FOREIGN KEY (`torrent_id`) REFERENCES `pw_app_torrents` (`id`),
  CONSTRAINT `app_torrent_peers_uid_foreign` FOREIGN KEY (`uid`) REFERENCES `pw_user` (`uid`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `pw_app_torrent_subscriptions`
-- ----------------------------
DROP TABLE IF EXISTS `pw_app_torrent_subscriptions`;
CREATE TABLE `pw_app_torrent_subscriptions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `torrent_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `app_torrent_subscriptions_uid_foreign` (`uid`) USING BTREE,
  KEY `app_torrent_subscriptions_torrent_id_foreign` (`torrent_id`) USING BTREE,
  CONSTRAINT `app_torrent_subscriptions_torrent_id_foreign` FOREIGN KEY (`torrent_id`) REFERENCES `pw_app_torrents` (`id`),
  CONSTRAINT `app_torrent_subscriptions_uid_foreign` FOREIGN KEY (`uid`) REFERENCES `pw_user` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

SET FOREIGN_KEY_CHECKS = 1;
