-- **********************************************************
-- *                                                        *
-- * IMPORTANT NOTE                                         *
-- *                                                        *
-- * Do not import this file manually but use the TYPOlight *
-- * install tool to create and maintain database tables!   *
-- *                                                        *
-- **********************************************************


-- --------------------------------------------------------

-- 
-- Table `tl_framework_routes`
-- 

CREATE TABLE `tl_framework_routes` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `route` varchar(255) NOT NULL default '',
  `addStatic` char(1) NOT NULL default '',
  `staticParams` blob NULL,
  `method` varchar(255) NOT NULL default 'GET/POST',
  `resolveTo` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

-- 
-- Table `tl_module`
-- 

CREATE TABLE `tl_module` (
  `routes` blob NULL,
  `defaultRoutedAction` varchar(255) NOT NULL default 'index',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
