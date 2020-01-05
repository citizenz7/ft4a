-- Adminer 4.7.3 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `blog_cats`;
CREATE TABLE `blog_cats` (
  `catID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `catTitle` varchar(255) DEFAULT NULL,
  `catSlug` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`catID`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;

INSERT INTO `blog_cats` (`catID`, `catTitle`, `catSlug`) VALUES
(40,	'Gnu/Linux - PCLinuxOS',	'gnu-linux-pclinuxos'),
(39,	'Gnu/Linux - OpenSuse',	'gnu-linux-opensuse'),
(37,	'Gnu/Linux - Mageia',	'gnu-linux-mageia'),
(38,	'Gnu/Linux - Mint',	'gnu-linux-mint'),
(36,	'Gnu/Linux - Gentoo',	'gnu-linux-gentoo'),
(35,	'Gnu/Linux - Elementary',	'gnu-linux-elementary'),
(34,	'Gnu/Linux - CentOS',	'gnu-linux-centos'),
(33,	'Gnu/Linux - Autres Ubuntu',	'gnu-linux-autres-ubuntu'),
(32,	'Gnu/Linux - Ubuntu',	'gnu-linux-ubuntu'),
(31,	'Gnu/Linux - Autres Slackware',	'gnu-linux-autres-slackware'),
(30,	'Gnu/Linux - Slackware',	'gnu-linux-slackware'),
(29,	'Gnu/Linux - Autres Puppy',	'gnu-linux-autres-puppy'),
(27,	'xBSD - Autres OpenBSD',	'xbsd-autres-openbsd'),
(28,	'Gnu/Linux - Puppy',	'gnu-linux-puppy'),
(26,	'xBSD - OpenBSD',	'xbsd-openbsd'),
(24,	'xBSD - NetBSD',	'xbsd-netbsd'),
(25,	'xBSD - Autres NetBSD',	'xbsd-autres-netbsd'),
(23,	'xBSD - Autres FreeBSD',	'xbsd-autres-freebsd'),
(21,	'Gnu/Linux - Autres Fedora',	'gnu-linux-autres-fedora'),
(22,	'xBSD - FreeBSD',	'xbsd-freebsd'),
(19,	'Gnu/Linux - Autres Debian',	'gnu-linux-autres-debian'),
(20,	'Gnu/Linux - Fedora',	'gnu-linux-fedora'),
(18,	'Gnu/Linux - Debian',	'gnu-linux-debian'),
(17,	'Gnu/Linux - Autres Arch',	'gnu-linux-autres-arch'),
(16,	'Gnu/Linux - Arch',	'gnu-linux-arch'),
(14,	'Videos - Films',	'vidos-films'),
(15,	'Videos - Film animation',	'videos-film-animation'),
(13,	'Images & Photos',	'images-photos'),
(12,	'Documents / Ebooks',	'documents-ebooks'),
(11,	'Applications - xBSD',	'applications-xbsd'),
(10,	'Applications - Windows - Jeux',	'applications-windows-jeux'),
(9,	'Litterature',	'litterature'),
(7,	'Applications - Autres',	'applications-autres'),
(6,	'Applications - Mac',	'applications-mac'),
(5,	'Applications - Windows',	'applications-windows'),
(4,	'Applications - Gnu/Linux',	'applications-gnu-linux'),
(3,	'Presse',	'presse'),
(1,	'Audio / Sons',	'audio-sons'),
(2,	'Videos - Autres',	'videos-autres'),
(8,	'Applications - Gnu/Linux - Jeux',	'applications-gnu-linux-jeux'),
(41,	'Gnu/Linux - Autres',	'gnu-linux-autres'),
(42,	'Gnu/Linux - Autres CentOS',	'gnu-linux-autres-centos')
ON DUPLICATE KEY UPDATE `catID` = VALUES(`catID`), `catTitle` = VALUES(`catTitle`), `catSlug` = VALUES(`catSlug`);

DROP TABLE IF EXISTS `blog_licences`;
CREATE TABLE `blog_licences` (
  `licenceID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `licenceTitle` varchar(255) NOT NULL,
  `licenceSlug` varchar(255) NOT NULL,
  PRIMARY KEY (`licenceID`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

INSERT INTO `blog_licences` (`licenceID`, `licenceTitle`, `licenceSlug`) VALUES
(5,	'CeCILL',	'cecill'),
(19,	'C.C. Public Domain',	'c-c-public-domain'),
(15,	'Apache 2.0',	'apache-2-0'),
(18,	'C.C. 0',	'c-c-0'),
(17,	'FreeBSD',	'freebsd'),
(16,	'AGPL',	'agpl'),
(14,	'C.C. By-Nc-Nd',	'c-c-by-nc-nd'),
(13,	'C.C. By-Nc-Sa',	'c-c-by-nc-sa'),
(12,	'C.C. By-Nc',	'c-c-by-nc'),
(1,	'GPL V2',	'gpl-v2'),
(2,	'GPL V3',	'gpl-v3'),
(3,	'LGPL V2',	'lgpl-v2'),
(4,	'LGPL V3',	'lgpl-v3'),
(6,	'BSD',	'bsd'),
(7,	'MIT',	'mit'),
(9,	'C.C. By',	'c-c-by'),
(10,	'C.C. By-Nd',	'c-c-by-nd'),
(11,	'C.C. By-Sa',	'c-c-by-sa'),
(8,	'LAL',	'lal'),
(20,	'BDL SleepyCat',	'bdl-sleepycat')
ON DUPLICATE KEY UPDATE `licenceID` = VALUES(`licenceID`), `licenceTitle` = VALUES(`licenceTitle`), `licenceSlug` = VALUES(`licenceSlug`);

DROP TABLE IF EXISTS `blog_logs`;
CREATE TABLE `blog_logs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `remote_addr` varchar(255) CHARACTER SET latin1 NOT NULL,
  `request_uri` varchar(255) CHARACTER SET latin1 NOT NULL,
  `message` text CHARACTER SET latin1 NOT NULL,
  `log_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `blog_members`;
CREATE TABLE `blog_members` (
  `memberID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `pid` varchar(32) NOT NULL,
  `memberDate` datetime NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `active` varchar(255) NOT NULL,
  PRIMARY KEY (`memberID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `blog_messages`;
CREATE TABLE `blog_messages` (
  `messages_id` int(11) NOT NULL AUTO_INCREMENT,
  `messages_id_expediteur` int(11) NOT NULL DEFAULT '0',
  `messages_id_destinataire` int(11) NOT NULL DEFAULT '0',
  `messages_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `messages_titre` text NOT NULL,
  `messages_message` text NOT NULL,
  `messages_lu` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`messages_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `blog_posts_comments`;
CREATE TABLE `blog_posts_comments` (
  `cid` int(10) NOT NULL AUTO_INCREMENT,
  `cid_torrent` int(10) NOT NULL,
  `cid_parent` int(10) NOT NULL DEFAULT '0',
  `cadded` datetime NOT NULL,
  `ctext` text NOT NULL,
  `cuser` varchar(25) NOT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `blog_posts_seo`;
CREATE TABLE `blog_posts_seo` (
  `postID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `postHash` varchar(40) NOT NULL,
  `postTitle` varchar(255) DEFAULT NULL,
  `postAuthor` varchar(255) NOT NULL,
  `postSlug` varchar(255) DEFAULT NULL,
  `postLink` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `postDesc` text,
  `postCont` text,
  `postTaille` bigint(20) NOT NULL DEFAULT '0',
  `postDate` datetime DEFAULT NULL,
  `postTorrent` varchar(150) NOT NULL,
  `postImage` varchar(255) NOT NULL,
  `postViews` int(11) NOT NULL,
  PRIMARY KEY (`postID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `blog_post_cats`;
CREATE TABLE `blog_post_cats` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `postID` int(11) DEFAULT NULL,
  `catID` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `blog_post_licences`;
CREATE TABLE `blog_post_licences` (
  `id_BPL` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `postID_BPL` int(11) NOT NULL,
  `licenceID_BPL` int(11) NOT NULL,
  PRIMARY KEY (`id_BPL`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `compteur`;
CREATE TABLE `compteur` (
  `ip` varchar(15) NOT NULL,
  `timestamp` int(11) NOT NULL,
  KEY `ip` (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `connectes`;
CREATE TABLE `connectes` (
  `ip` varchar(45) NOT NULL,
  `pseudo` varchar(255) NOT NULL,
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `xbt_announce_log`;
CREATE TABLE `xbt_announce_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ipa` int(10) unsigned NOT NULL,
  `port` int(11) NOT NULL,
  `event` int(11) NOT NULL,
  `info_hash` binary(20) NOT NULL,
  `peer_id` binary(20) NOT NULL,
  `downloaded` bigint(20) unsigned NOT NULL,
  `left0` bigint(20) unsigned NOT NULL,
  `uploaded` bigint(20) unsigned NOT NULL,
  `uid` int(11) NOT NULL,
  `mtime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `xbt_config`;
CREATE TABLE `xbt_config` (
  `name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `value` varchar(255) CHARACTER SET latin1 NOT NULL,
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `xbt_config` (`name`, `value`) VALUES
('redirect_url',	'http://www.example.com'),
('query_log',	'1'),
('pid_file',	'/var/run/xbt_tracker_example.pid'),
('offline_message',	''),
('column_users_uid',	'uid'),
('column_files_seeders',	'seeders'),
('column_files_leechers',	'leechers'),
('column_files_fid',	'fid'),
('column_files_completed',	'completed'),
('write_db_interval',	'15'),
('scrape_interval',	'0'),
('read_db_interval',	'60'),
('read_config_interval',	'60'),
('clean_up_interval',	'60'),
('log_scrape',	'0'),
('log_announce',	'1'),
('log_access',	'0'),
('gzip_scrape',	'1'),
('full_scrape',	'1'),
('debug',	'1'),
('daemon',	'1'),
('anonymous_scrape',	'0'),
('announce_interval',	'200'),
('torrent_pass_private_key',	'MyPrivateKeyWithLettersAndNumbers'),
('table_announce_log',	'xbt_announce_log'),
('table_files',	'xbt_files'),
('table_files_users',	'xbt_files_users'),
('table_scrape_log',	'xbt_scrape_log'),
('table_users',	'xbt_users'),
('listen_ipa',	'*'),
('listen_port',	'xxxxx'),
('anonymous_announce',	'0'),
('auto_register',	'0')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `value` = VALUES(`value`);

DROP TABLE IF EXISTS `xbt_deny_from_hosts`;
CREATE TABLE `xbt_deny_from_hosts` (
  `begin` int(10) unsigned NOT NULL,
  `end` int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `xbt_files`;
CREATE TABLE `xbt_files` (
  `fid` int(11) NOT NULL AUTO_INCREMENT,
  `info_hash` binary(20) NOT NULL,
  `leechers` int(11) NOT NULL DEFAULT '0',
  `seeders` int(11) NOT NULL DEFAULT '0',
  `completed` int(11) NOT NULL DEFAULT '0',
  `flags` int(11) NOT NULL DEFAULT '0',
  `mtime` int(11) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`fid`),
  UNIQUE KEY `info_hash` (`info_hash`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `xbt_files_users`;
CREATE TABLE `xbt_files_users` (
  `fid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `active` tinyint(4) NOT NULL,
  `announced` int(11) NOT NULL,
  `completed` int(11) NOT NULL,
  `downloaded` bigint(20) unsigned NOT NULL,
  `left` bigint(20) unsigned NOT NULL,
  `uploaded` bigint(20) unsigned NOT NULL,
  `mtime` int(11) NOT NULL,
  `down_rate` int(10) unsigned NOT NULL,
  `up_rate` int(10) unsigned NOT NULL,
  UNIQUE KEY `fid` (`fid`,`uid`),
  KEY `uid` (`uid`),
  CONSTRAINT `xbt_files_users_ibfk_1` FOREIGN KEY (`fid`) REFERENCES `xbt_files` (`fid`) ON DELETE CASCADE,
  CONSTRAINT `xbt_files_users_ibfk_2` FOREIGN KEY (`fid`) REFERENCES `xbt_files` (`fid`) ON DELETE CASCADE,
  CONSTRAINT `xbt_files_users_ibfk_3` FOREIGN KEY (`fid`) REFERENCES `xbt_files` (`fid`) ON DELETE CASCADE,
  CONSTRAINT `xbt_files_users_ibfk_4` FOREIGN KEY (`uid`) REFERENCES `xbt_users` (`uid`) ON DELETE CASCADE,
  CONSTRAINT `xbt_files_users_ibfk_5` FOREIGN KEY (`uid`) REFERENCES `xbt_users` (`uid`) ON DELETE CASCADE,
  CONSTRAINT `xbt_files_users_ibfk_6` FOREIGN KEY (`uid`) REFERENCES `xbt_users` (`uid`) ON DELETE CASCADE,
  CONSTRAINT `xbt_files_users_ibfk_7` FOREIGN KEY (`uid`) REFERENCES `xbt_users` (`uid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `xbt_scrape_log`;
CREATE TABLE `xbt_scrape_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ipa` int(10) unsigned NOT NULL,
  `info_hash` binary(20) DEFAULT NULL,
  `uid` int(11) NOT NULL,
  `mtime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `xbt_users`;
CREATE TABLE `xbt_users` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `torrent_pass_version` int(11) NOT NULL DEFAULT '0',
  `downloaded` bigint(20) unsigned NOT NULL DEFAULT '0',
  `uploaded` bigint(20) unsigned NOT NULL DEFAULT '0',
  `torrent_pass` char(32) CHARACTER SET latin1 NOT NULL,
  `torrent_pass_secret` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


-- 2019-12-22 09:11:03
