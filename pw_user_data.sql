SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `pw_user_data`
-- ----------------------------
DROP TABLE IF EXISTS `pw_user_data`;
CREATE TABLE `pw_user_data` (
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `lastvisit` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后访问时间',
  `lastloginip` varchar(20) NOT NULL DEFAULT '' COMMENT '最后登录IP',
  `lastpost` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后发帖时间',
  `lastactivetime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后活动时间',
  `onlinetime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '在线时长',
  `trypwd` varchar(16) NOT NULL DEFAULT '' COMMENT '尝试的登录错误信息，trydate|trynum',
  `postcheck` varchar(16) NOT NULL DEFAULT '' COMMENT '发帖检查',
  `postnum` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '发帖数',
  `digest` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '精华数',
  `todaypost` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '今天发帖数',
  `todayupload` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '今日上传个数',
  `findpwd` varchar(26) NOT NULL DEFAULT '' COMMENT '找回密码尝试错误次数,trydate|trynum',
  `follows` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关注数',
  `fans` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '粉丝数',
  `message_tone` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否有新消息',
  `messages` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '私信数',
  `notices` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '消息数',
  `likes` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '喜欢次数',
  `punch` varchar(200) NOT NULL DEFAULT '' COMMENT '打卡相关',
  `credit1` double NOT NULL DEFAULT '0' COMMENT '积分字段1',
  `credit2` double NOT NULL DEFAULT '0' COMMENT '积分字段2',
  `credit3` double NOT NULL DEFAULT '0' COMMENT '积分字段3',
  `credit4` double NOT NULL DEFAULT '0' COMMENT '积分字段4',
  `credit5` double NOT NULL DEFAULT '0' COMMENT '积分字段5',
  `credit6` double NOT NULL DEFAULT '0' COMMENT '积分字段6',
  `credit7` double NOT NULL DEFAULT '0' COMMENT '积分字段7',
  `credit8` double NOT NULL DEFAULT '0' COMMENT '积分字段8',
  `join_forum` varchar(255) NOT NULL DEFAULT '' COMMENT '加入的版块',
  `recommend_friend` varchar(255) NOT NULL DEFAULT '' COMMENT '推荐朋友',
  `last_credit_affect_log` varchar(255) NOT NULL DEFAULT '' COMMENT '最后积分变动内容',
  `medal_ids` varchar(255) NOT NULL DEFAULT '',
  `last_search_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) DEFAULT CHARSET=utf8 COMMENT='用户扩展数据表';

-- ----------------------------
--  Table structure for `pw_windid_user_data`
-- ----------------------------
DROP TABLE IF EXISTS `pw_windid_user_data`;
CREATE TABLE `pw_windid_user_data` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `messages` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '用户消息数',
  `credit1` double NOT NULL DEFAULT '0' COMMENT '积分1',
  `credit2` double NOT NULL DEFAULT '0' COMMENT '积分2',
  `credit3` double NOT NULL DEFAULT '0' COMMENT '积分3',
  `credit4` double NOT NULL DEFAULT '0' COMMENT '积分4',
  `credit5` double NOT NULL DEFAULT '0' COMMENT '积分5',
  `credit6` double NOT NULL DEFAULT '0' COMMENT '积分6',
  `credit7` double NOT NULL DEFAULT '0' COMMENT '积分7',
  `credit8` double NOT NULL DEFAULT '0' COMMENT '积分8',
  PRIMARY KEY (`uid`)
) DEFAULT CHARSET=utf8 COMMENT='windid用户数据';

SET FOREIGN_KEY_CHECKS = 1;
