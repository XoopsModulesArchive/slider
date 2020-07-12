#
# Table structure for table `slider`
#
#Create Table

CREATE TABLE slider (
  `id` int(8) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `description` longtext,
  `mydate` int(10) NOT NULL default '0',
  `active` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `title` (`title`(40))
) TYPE=MyISAM;

CREATE TABLE slider_content (
  `id` int(8) NOT NULL auto_increment,
  `sid` int(8) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `description` longtext,
  `content` longtext,
  PRIMARY KEY  (`id`),
  KEY `title` (`title`(40))
) TYPE=MyISAM;