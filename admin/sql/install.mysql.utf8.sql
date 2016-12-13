CREATE TABLE IF NOT EXISTS `#__jlfacebooktabsJ3_fbtabs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ordering` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `theme` varchar(255) NOT NULL,
  `layout` varchar(255) NOT NULL,
  `featured_only` tinyint(3) NOT NULL,
  `show_tags` tinyint(3) NOT NULL,
  `show_image` tinyint(3) NOT NULL,
  `show_author` tinyint(3) NOT NULL,
  `show_creation_date` tinyint(3) NOT NULL,
  `show_rating` tinyint(3) NOT NULL,
  `show_hits` tinyint(3) NOT NULL,
  `show_readmore` tinyint(3) NOT NULL,
  `show_desc_icon` tinyint(3) NOT NULL,
  `show_info_icon` tinyint(3) NOT NULL,
  `intro_text` text NOT NULL,
  `footer_text` text NOT NULL,
  `show_short_desc` tinyint(3) NOT NULL,
  `short_desc_max` int(11) NOT NULL,
  `allow_desc_html` tinyint(3) NOT NULL,
  `lightbox_active` tinyint(3) NOT NULL,
  `show_category` tinyint(3) NOT NULL,
  `fbpost` text NOT NULL,
  `state` tinyint(1) NOT NULL,
  `language` varchar(255) NOT NULL,
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL,
  `cache_enabled` tinyint(3) NOT NULL,
  `items_limit` tinyint(3) NOT NULL,
  `items_sort` tinyint(3) NOT NULL,
  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__jlfacebooktabsJ3_tab_cat` (
  `tabid` int(11) NOT NULL,
  `catid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `#__jlfacebooktabsJ3_tab_fbpage` (
  `tabid` int(11) NOT NULL,
  `pageid` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
