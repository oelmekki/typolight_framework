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
-- Table `tl_testing_build`
-- 

CREATE TABLE `tl_testing_build` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tstamp` int(10) unsigned NOT NULL default '0',
  `created_at` int(10) unsigned NOT NULL default '0',
  `revision` varchar(255) NOT NULL default '',
  `author` varchar(255) NOT NULL default '',
  `message` varchar(255) NOT NULL default '',
  `scm` varchar(255) NOT NULL default '',
  `test_results` text NULL,
  PRIMARY KEY  (`id`),
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

-- 
-- Table `tl_example_emodel_1`
-- 

CREATE TABLE `tl_example_emodel_1` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tstamp` int(10) unsigned NOT NULL default '0',
  `created_at` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

-- 
-- Table `tl_example_emodel_validations`
-- 

CREATE TABLE `tl_example_emodel_validations` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tstamp` int(10) unsigned NOT NULL default '0',
  `created_at` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `phone` varchar(255) NOT NULL default '',
  `exampleemodel1_id` int(10) unsigned NOT NULL default '0', 
  PRIMARY KEY  (`id`),
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

-- 
-- Table `tl_example_emodel_assoc1`
-- 

CREATE TABLE `tl_example_emodel_assoc1` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `created_at` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `exampleemodel5_id` int(10) unsigned NOT NULL default '0', 
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`),
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

-- 
-- Table `tl_example_emodel_assoc2`
-- 

CREATE TABLE `tl_example_emodel_assoc2` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `created_at` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`),
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

-- 
-- Table `tl_example_emodel_assoc3`
-- 

CREATE TABLE `tl_example_emodel_assoc3` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `exampleemodelassoc1_id` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `created_at` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

-- 
-- Table `tl_example_emodel_assoc4`
-- 

CREATE TABLE `tl_example_emodel_assoc4` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `exampleemodelassoc2_id` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `created_at` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

-- 
-- Table `tl_example_emodel_assoc5`
-- 

CREATE TABLE `tl_example_emodel_assoc5` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `exampleemodelassoc1_id` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `created_at` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

-- 
-- Table `tl_assoc1_assoc5`
-- 

CREATE TABLE `tl_assoc1_assoc5` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `exampleemodelassoc1_id` int(10) unsigned NOT NULL default '0',
  `exampleemodelassoc5_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
