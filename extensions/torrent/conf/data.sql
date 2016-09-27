SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `pw_app_torrent`
-- ----------------------------
DROP TABLE IF EXISTS `pw_app_torrent`;
CREATE TABLE `pw_app_torrent` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) NOT NULL,
  `info_hash` binary(20) NOT NULL,
  `filename` varchar(255) NOT NULL DEFAULT '',
  `save_as` varchar(255) NOT NULL DEFAULT '',
  `size` bigint(20) unsigned NOT NULL DEFAULT '0',
  `type` enum('single','multi') NOT NULL DEFAULT 'single',
  `leechers` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `seeders` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `owner` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `nfo` blob,
  `anonymous` enum('yes','no') NOT NULL DEFAULT 'no',
  `wikilink` varchar(256) DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `info_hash` (`info_hash`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `pw_app_torrent_agent_allowed_family`
-- ----------------------------
DROP TABLE IF EXISTS `pw_app_torrent_agent_allowed_family`;
CREATE TABLE `pw_app_torrent_agent_allowed_family` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `family` varchar(50) NOT NULL DEFAULT '',
  `peer_id_pattern` varchar(200) NOT NULL,
  `agent_pattern` varchar(200) NOT NULL,
  `https` enum('yes','no') NOT NULL DEFAULT 'no',
  `hits` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `pw_app_torrent_agent_allowed_family`
-- ----------------------------
BEGIN;
INSERT INTO `pw_app_torrent_agent_allowed_family` VALUES ('1', 'Azureus 2.5.0.4', '^-AZ2504-', '^Azureus 2.5.0.4', 'yes', '0'), ('2', 'Ktorrent 4.0.x', '^-KT4([0-9])([0-9])([0-9])-', '^KTorrent\\/4\\.([0-9])\\.([0-9])', 'yes', '0'), ('3', 'Bittorrent 6.x', '^M6-([0-9])-([0-9])--', '^BitTorrent\\/6([0-9])([0-9])([0-9])', 'yes', '0'), ('4', 'Deluge 0.x', '^-DE0([0-9])([0-9])([0-9])-', '^Deluge 0\\.([0-9])\\.([0-9])\\.([0-9])', 'yes', '0'), ('5', 'Transmission1.x', '^-TR1([0-9])([0-9])([0-9])-', '^Transmission\\/1\\.([0-9])([0-9])', 'yes', '0'), ('6', 'RTorrent 0.x(with libtorrent 0.x)', '^-lt([0-9A-E])([0-9A-E])([0-9A-E])([0-9A-E])-', '^rtorrent\\/0\\.([0-9])\\.([0-9])\\/0\\.([1-9][0-9]*)\\.(0|[1-9][0-9]*)', 'no', '0'), ('7', 'Rufus 0.x', '', '^Rufus\\/0\\.([0-9])\\.([0-9])', 'no', '0'), ('8', 'Azureus 3.x', '^-AZ3([0-9])([0-9])([0-9])-', '^Azureus 3\\.([0-9])\\.([0-9])\\.([0-9])', 'yes', '0'), ('9', 'uTorrent 1.7.x', '^-UT17([0-9])([0-9])-', '^uTorrent\\/17([0-9])([0-9])', 'yes', '0'), ('10', 'BitRocket 0.x', '^-BR0([0-9])([1-9][0-9]*)-', '^BitRocket\\/0\\.([0-9])\\.([0-9])\\(([1-9][0-9]*)\\) libtorrent\\/0\\.([1-9][0-9]*)\\.(0|[1-9][0-9]*)\\.(0|[1-9][0-9]*)', 'yes', '0'), ('11', 'MLDonkey 2.9.x', '^-ML2\\.9\\.([0-9])-', '^MLDonkey\\/2\\.9\\.([0-9])', 'yes', '0'), ('12', 'uTorrent 1.8.x', '^-UT18([0-9])([0-9])-', '^uTorrent\\/18([0-9])([0-9])', 'yes', '0'), ('13', 'Azureus 4.x', '^-AZ4([0-9])([0-9])([0-9])-', '^Azureus 4\\.([0-9])\\.([0-9])\\.([0-9])', 'yes', '0'), ('14', 'SymTorrent', '', '^SymTorrent', 'no', '0'), ('15', 'Deluge 1.x', '^-DE1([0-9])([0-9A-Z])([0-9])-', '^Deluge 1\\.([0-9])\\.([0-9])', 'yes', '0'), ('16', 'uTorrent 1.8xB', '^-UT18([0-9])B-', '^uTorrent\\/18([0-9])B\\(([1-9][0-9]*)\\)', 'yes', '0'), ('17', 'uTorrent 2.x.x', '^-UT2([0-9])([0-9])([0-9])-', '^uTorrent\\/2([0-9])([0-9])([0-9])', 'yes', '0'), ('19', 'uTorrent 3.x', '^-UT3([0-9])([0-9])([0-9])-', '^uTorrent\\/3([0-9])([0-9])([0-9])', 'yes', '0'), ('18', 'uTorrent 3.x.xB', '^-UT3([0-9])([0-9])B-', '^uTorrent\\/3([0-9])([0-9])B', 'yes', '0'), ('20', 'uTorrentMac 1.x', '^-UM1([0-9])([0-9])([0-9])-', '^uTorrentMac\\/1([0-9])([0-9])([0-9])', 'yes', '0');
COMMIT;

-- ----------------------------
--  Table structure for `pw_app_torrent_file`
-- ----------------------------
DROP TABLE IF EXISTS `pw_app_torrent_file`;
CREATE TABLE `pw_app_torrent_file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `torrent` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `filename` varchar(255) NOT NULL DEFAULT '',
  `size` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `pw_app_torrent_history`
-- ----------------------------
DROP TABLE IF EXISTS `pw_app_torrent_history`;
CREATE TABLE `pw_app_torrent_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `torrent` mediumint(8) NOT NULL,
  `uploaded` bigint(20) unsigned NOT NULL DEFAULT '0',
  `uploaded_last` bigint(20) NOT NULL DEFAULT '0',
  `downloaded` bigint(20) unsigned NOT NULL DEFAULT '0',
  `downloaded_last` bigint(20) NOT NULL DEFAULT '0',
  `status` enum('do','done','stop') NOT NULL DEFAULT 'do',
  PRIMARY KEY (`id`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `pw_app_torrent_peer`
-- ----------------------------
DROP TABLE IF EXISTS `pw_app_torrent_peer`;
CREATE TABLE `pw_app_torrent_peer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `torrent` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `peer_id` binary(20) NOT NULL,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `ip` varbinary(64) NOT NULL DEFAULT '',
  `port` smallint(5) unsigned NOT NULL DEFAULT '0',
  `uploaded` bigint(20) unsigned NOT NULL DEFAULT '0',
  `downloaded` bigint(20) unsigned NOT NULL DEFAULT '0',
  `left` bigint(20) unsigned NOT NULL DEFAULT '0',
  `seeder` enum('yes','no') NOT NULL DEFAULT 'no',
  `started_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_action` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `connectable` enum('yes','no') NOT NULL DEFAULT 'yes',
  `agent` varchar(60) NOT NULL DEFAULT '',
  `finished_at` int(10) unsigned NOT NULL DEFAULT '0',
  `passkey` varchar(40) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `pw_app_torrent_subscription`
-- ----------------------------
DROP TABLE IF EXISTS `pw_app_torrent_subscription`;
CREATE TABLE `pw_app_torrent_subscription` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `torrent` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `pw_app_torrent_user`
-- ----------------------------
DROP TABLE IF EXISTS `pw_app_torrent_user`;
CREATE TABLE `pw_app_torrent_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `passkey` varchar(40) NOT NULL DEFAULT '',
  `uploaded_mo` bigint(20) unsigned NOT NULL DEFAULT '0',
  `downloaded_mo` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;
