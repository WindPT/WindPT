/*
 Navicat Premium Data Transfer

 Source Server         : 192.168.26.190
 Source Server Type    : MySQL
 Source Server Version : 50528
 Source Host           : localhost
 Source Database       : xuulm_newpt

 Target Server Type    : MySQL
 Target Server Version : 50528
 File Encoding         : utf-8

 Date: 10/12/2014 12:51:32 PM
*/

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
  `processing` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `size` bigint(20) unsigned NOT NULL DEFAULT '0',
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `type` enum('single','multi') NOT NULL DEFAULT 'single',
  `numfiles` smallint(5) unsigned NOT NULL DEFAULT '0',
  `times_completed` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `leechers` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `seeders` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `last_action` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `visible` enum('yes','no') NOT NULL DEFAULT 'yes',
  `banned` enum('yes','no') NOT NULL DEFAULT 'no',
  `owner` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `nfo` blob,
  `sp_state` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `promotion_time_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `promotion_until` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `anonymous` enum('yes','no') NOT NULL DEFAULT 'no',
  `wikilink` varchar(256) DEFAULT '',
  `pos_state` enum('normal','sticky') NOT NULL DEFAULT 'normal',
  `cache_stamp` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `picktype` enum('hot','classic','recommended','normal') NOT NULL DEFAULT 'normal',
  `picktime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_reseed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `endfree` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `endsticky` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `info_hash` (`info_hash`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `pw_app_torrent_agent_allowed_family`
-- ----------------------------
DROP TABLE IF EXISTS `pw_app_torrent_agent_allowed_family`;
CREATE TABLE `pw_app_torrent_agent_allowed_family` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `family` varchar(50) NOT NULL DEFAULT '',
  `start_name` varchar(100) NOT NULL DEFAULT '',
  `peer_id_pattern` varchar(200) NOT NULL,
  `peer_id_match_num` tinyint(3) unsigned NOT NULL,
  `peer_id_matchtype` enum('dec','hex') NOT NULL DEFAULT 'dec',
  `peer_id_start` varchar(20) NOT NULL,
  `agent_pattern` varchar(200) NOT NULL,
  `agent_match_num` tinyint(3) unsigned NOT NULL,
  `agent_matchtype` enum('dec','hex') NOT NULL DEFAULT 'dec',
  `agent_start` varchar(100) NOT NULL,
  `exception` enum('yes','no') NOT NULL DEFAULT 'no',
  `allowhttps` enum('yes','no') NOT NULL DEFAULT 'no',
  `comment` varchar(200) NOT NULL DEFAULT '',
  `hits` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `pw_app_torrent_agent_allowed_family`
-- ----------------------------
BEGIN;
INSERT INTO `pw_app_torrent_agent_allowed_family` VALUES ('1', 'Azureus 2.5.0.4', 'Azureus 2.5.0.4', '/^-AZ2504-/', '0', 'dec', '-AZ2504-', '/^Azureus 2.5.0.4/', '0', 'dec', 'Azureus 2.5.0.4', 'no', 'yes', '', '0'), ('2', 'Ktorrent 4.0.x', 'Ktorrent 4.0.x', '/^-KT4([0-9])([0-9])([0-9])-/', '0', 'dec', '-KT4000-', '/^KTorrent\\/4\\.([0-9])\\.([0-9])/', '0', 'dec', 'KTorrent/4.0.0', 'no', 'yes', '', '0'), ('3', 'Bittorrent 6.x', 'Bittorrent 6.0.1', '/^M6-([0-9])-([0-9])--/', '2', 'dec', 'M6-0-1--', '/^BitTorrent\\/6([0-9])([0-9])([0-9])/', '3', 'dec', 'BitTorrent/6010', 'no', 'yes', '', '0'), ('4', 'Deluge 0.x', 'Deluge 0.5.8.9', '/^-DE0([0-9])([0-9])([0-9])-/', '3', 'dec', '-DE0589-', '/^Deluge 0\\.([0-9])\\.([0-9])\\.([0-9])/', '3', 'dec', 'Deluge 0.5.8.9', 'no', 'yes', '', '0'), ('5', 'Transmission1.x', 'Transmission 1.06 (build 5136)', '/^-TR1([0-9])([0-9])([0-9])-/', '3', 'dec', '-TR1060-', '/^Transmission\\/1\\.([0-9])([0-9])/', '3', 'dec', 'Transmission/1.06', 'no', 'yes', '', '0'), ('6', 'RTorrent 0.x(with libtorrent 0.x)', 'rTorrent 0.8.0 (with libtorrent 0.12.0)', '/^-lt([0-9A-E])([0-9A-E])([0-9A-E])([0-9A-E])-/', '4', 'hex', '-lt0C00-', '/^rtorrent\\/0\\.([0-9])\\.([0-9])\\/0\\.([1-9][0-9]*)\\.(0|[1-9][0-9]*)/', '4', 'dec', 'rtorrent/0.8.0/0.12.0', 'no', 'no', '', '0'), ('7', 'Rufus 0.x', 'Rufus 0.6.9', '', '0', 'dec', '', '/^Rufus\\/0\\.([0-9])\\.([0-9])/', '2', 'dec', 'Rufus/0.6.9', 'no', 'no', '', '0'), ('8', 'Azureus 3.x', 'Azureus 3.0.5.0', '/^-AZ3([0-9])([0-9])([0-9])-/', '3', 'dec', '-AZ3050-', '/^Azureus 3\\.([0-9])\\.([0-9])\\.([0-9])/', '3', 'dec', 'Azureus 3.0.5.0', 'no', 'yes', '', '0'), ('9', 'uTorrent 1.7.x', 'uTorrent 1.7.5', '/^-UT17([0-9])([0-9])-/', '2', 'dec', '-UT1750-', '/^uTorrent\\/17([0-9])([0-9])/', '2', 'dec', 'uTorrent/1750', 'no', 'yes', '', '0'), ('10', 'BitRocket 0.x', 'BitRocket 0.3.3(32)', '/^-BR0([0-9])([1-9][0-9]*)-/', '2', 'dec', '-BR0332-', '/^BitRocket\\/0\\.([0-9])\\.([0-9])\\(([1-9][0-9]*)\\) libtorrent\\/0\\.([1-9][0-9]*)\\.(0|[1-9][0-9]*)\\.(0|[1-9][0-9]*)/', '6', 'dec', 'BitRocket/0.3.3(32) libtorrent/0.13.0.0', 'no', 'yes', '', '0'), ('11', 'MLDonkey 2.9.x', 'MLDonkey 2.9.2', '/^-ML2\\.9\\.([0-9])-/', '1', 'dec', '-ML2.9.2-', '/^MLDonkey\\/2\\.9\\.([0-9])/', '1', 'dec', 'MLDonkey/2.9.2', 'no', 'yes', '', '0'), ('12', 'uTorrent 1.8.x', 'uTorrent 1.8.0', '/^-UT18([0-9])([0-9])-/', '2', 'dec', '-UT1800-', '/^uTorrent\\/18([0-9])([0-9])/', '2', 'dec', 'uTorrent/1800', 'no', 'yes', '', '0'), ('13', 'Azureus 4.x', 'Vuze 4.0.0.2', '/^-AZ4([0-9])([0-9])([0-9])-/', '3', 'dec', '-AZ4002-', '/^Azureus 4\\.([0-9])\\.([0-9])\\.([0-9])/', '3', 'dec', 'Azureus 4.0.0.2', 'no', 'yes', '', '0'), ('14', 'SymTorrent', '', '', '0', 'dec', '', '/^SymTorrent/', '0', 'dec', 'SymTorrent', 'no', 'no', '', '0'), ('15', 'Deluge 1.x', 'Deluge 1.1.6', '/^-DE1([0-9])([0-9])([0-9])-/', '3', 'dec', '-DE1160-', '/^Deluge 1\\.([0-9])\\.([0-9])/', '2', 'dec', 'Deluge 1.1.6', 'no', 'yes', '', '0'), ('16', 'uTorrent 1.8xB', 'uTorrent 1.80 Beta (build 9137)', '/^-UT18([0-9])B-/', '1', 'dec', '-UT180B-', '/^uTorrent\\/18([0-9])B\\(([1-9][0-9]*)\\)/', '2', 'dec', 'uTorrent/180B(9137)', 'no', 'yes', '', '0'), ('17', 'uTorrent 2.x.x', 'uTorrent 2.0(build 17624)', '/^-UT2([0-9])([0-9])([0-9])-/', '3', 'dec', '-UT2000-', '/^uTorrent\\/2([0-9])([0-9])([0-9])/', '3', 'dec', 'uTorrent/2000', 'no', 'yes', '', '0'), ('19', 'uTorrent 3.x', 'uTorrent 3.0', '/^-UT3([0-9])([0-9])([0-9])-/', '3', 'dec', '-UT3000-', '/^uTorrent\\/3([0-9])([0-9])([0-9])/', '3', 'dec', 'uTorrent/3000', 'no', 'yes', '', '0'), ('18', 'uTorrent 3.x.xB', 'uTorrent 3.0 Beta', '/^-UT3([0-9])([0-9])B-/', '2', 'dec', '-UT300B-', '/^uTorrent\\/3([0-9])([0-9])B/', '2', 'dec', 'uTorrent/300B', 'no', 'yes', '', '0');
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
) ENGINE=MyISAM AUTO_INCREMENT=98 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `pw_app_torrent_history`
-- ----------------------------
DROP TABLE IF EXISTS `pw_app_torrent_history`;
CREATE TABLE `pw_app_torrent_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `torrent` mediumint(8) NOT NULL,
  `uploaded` bigint(20) unsigned NOT NULL DEFAULT '0',
  `uploaded_last` bigint(20) DEFAULT '0',
  `downloaded` bigint(20) unsigned NOT NULL DEFAULT '0',
  `downloaded_last` bigint(20) DEFAULT '0',
  `status` enum('do','done','stop') NOT NULL DEFAULT 'do',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `pw_app_torrent_peer`
-- ----------------------------
DROP TABLE IF EXISTS `pw_app_torrent_peer`;
CREATE TABLE `pw_app_torrent_peer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `torrent` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `peer_id` binary(20) NOT NULL,
  `ip` varbinary(64) NOT NULL DEFAULT '',
  `port` smallint(5) unsigned NOT NULL DEFAULT '0',
  `uploaded` bigint(20) unsigned NOT NULL DEFAULT '0',
  `downloaded` bigint(20) unsigned NOT NULL DEFAULT '0',
  `to_go` bigint(20) unsigned NOT NULL DEFAULT '0',
  `seeder` enum('yes','no') NOT NULL DEFAULT 'no',
  `started` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_action` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `prev_action` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `connectable` enum('yes','no') NOT NULL DEFAULT 'yes',
  `userid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `agent` varchar(60) NOT NULL DEFAULT '',
  `finishedat` int(10) unsigned NOT NULL DEFAULT '0',
  `passkey` char(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `pw_app_torrent_user`
-- ----------------------------
DROP TABLE IF EXISTS `pw_app_torrent_user`;
CREATE TABLE `pw_app_torrent_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `passkey` varchar(32) NOT NULL DEFAULT '',
  `uploaded_mo` bigint(20) unsigned NOT NULL DEFAULT '0',
  `downloaded_mo` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;
