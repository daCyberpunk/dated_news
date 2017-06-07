#
# Table structure for table 'tx_news_domain_model_news'
#
CREATE TABLE tx_news_domain_model_news (

	showincalendar tinyint(1) unsigned DEFAULT '0' NOT NULL,
	enable_application tinyint(1) unsigned DEFAULT '0' NOT NULL,
	fulltime tinyint(1) unsigned DEFAULT '0' NOT NULL,
	eventstart int(11) DEFAULT '0' NOT NULL,
	eventend int(11) DEFAULT '0' NOT NULL,
	eventtype varchar(255) DEFAULT '' NOT NULL,
	eventlocation varchar(255) DEFAULT '' NOT NULL,
	textcolor varchar(255) DEFAULT '' NOT NULL,
	backgroundcolor varchar(255) DEFAULT '' NOT NULL,
	application int(11) unsigned DEFAULT '0' NOT NULL,
	slots int(11) unsigned DEFAULT '0' NOT NULL,
	price varchar(255) DEFAULT '' NOT NULL,
	early_bird_price varchar(255) DEFAULT '' NOT NULL,
	early_bird_date int(11) DEFAULT '0' NOT NULL,
	targetgroup varchar(255) DEFAULT '' NOT NULL,
	locations int(11) unsigned DEFAULT '0' NOT NULL,
	persons int(11) unsigned DEFAULT '0' NOT NULL,

	newsrecurrence int(11) unsigned DEFAULT '0' NOT NULL,

	recurrence int(11) DEFAULT '0' NOT NULL,
	recurrence_type int(11) DEFAULT '0' NOT NULL,
	recurrence_until int(11) DEFAULT '0' NOT NULL,
	recurrence_count int(11) DEFAULT '1' NOT NULL,
	ud_type int(11) DEFAULT '0' NOT NULL,
	ud_daily_everycount int(11) DEFAULT '1' NOT NULL,
	ud_weekly_everycount int(11) DEFAULT '1' NOT NULL,
	ud_weekly_weekdays int(11) DEFAULT '0' NOT NULL,
	ud_monthly_base int(11) DEFAULT '0' NOT NULL,
	ud_monthly_perday int(11) DEFAULT '0' NOT NULL,
	ud_monthly_perday_weekdays int(11) DEFAULT '0' NOT NULL,
	ud_monthly_perdate_day int(11) DEFAULT '0' NOT NULL,
	ud_monthly_perdate_lastday int(11) DEFAULT '0' NOT NULL,
	ud_monthly_everycount int(11) DEFAULT '1' NOT NULL,
	ud_yearly_everycount int(11) DEFAULT '1' NOT NULL,
	ud_yearly_perday int(11) DEFAULT '0' NOT NULL,
	ud_yearly_perday_weekdays int(11) DEFAULT '0' NOT NULL,
	ud_yearly_perday_month int(11) DEFAULT '0' NOT NULL,

);

#
# Table structure for table 'tx_datednews_domain_model_newsrecurrence'
#
CREATE TABLE tx_datednews_domain_model_newsrecurrence (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	eventstart int(11) DEFAULT '0' NOT NULL,
	eventend int(11) DEFAULT '0' NOT NULL,
	eventlocation varchar(255) DEFAULT '' NOT NULL,
	bodytext text,
	teaser text,
	modified tinyint(1) unsigned DEFAULT '0' NOT NULL,
	parent_event int(11) unsigned DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted smallint(5) unsigned DEFAULT '0' NOT NULL,
	hidden smallint(5) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state smallint(6) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,
	l10n_state text,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
	KEY language (l10n_parent,sys_language_uid)

);

#
# Table structure for table 'tx_datednews_news_newsrecurrence_mm'
#
CREATE TABLE tx_datednews_news_newsrecurrence_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_datednews_domain_model_application'
#
CREATE TABLE tx_datednews_domain_model_application (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	application_title varchar(255) DEFAULT '' NOT NULL,
	title varchar(255) DEFAULT '' NOT NULL,
	name varchar(255) DEFAULT '' NOT NULL,
	surname varchar(255) DEFAULT '' NOT NULL,
	email varchar(255) DEFAULT '' NOT NULL,
	events int(11) unsigned DEFAULT '0' NOT NULL,
	reserved_slots int(11) unsigned DEFAULT '0' NOT NULL,
	form_timestamp int(11) unsigned DEFAULT '0' NOT NULL,
	company varchar(255) DEFAULT '' NOT NULL,
	address varchar(255) DEFAULT '' NOT NULL,
	address2 varchar(255) DEFAULT '' NOT NULL,
	zip varchar(255) DEFAULT '' NOT NULL,
	city varchar(255) DEFAULT '' NOT NULL,
	country varchar(255) DEFAULT '' NOT NULL,
	costs varchar(255) DEFAULT '' NOT NULL,
	phone varchar(255) DEFAULT '' NOT NULL,
	mobile varchar(255) DEFAULT '' NOT NULL,
	taxid varchar(255) DEFAULT '' NOT NULL,
	terms_accept tinyint(1) DEFAULT NULL,
	confirmed tinyint(1) DEFAULT NULL,
	message text NOT NULL,


	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
 KEY language (l10n_parent,sys_language_uid)

);

#
# Table structure for table 'tx_news_domain_model_news'
#
CREATE TABLE tx_news_domain_model_news (
	categories int(11) unsigned DEFAULT '0' NOT NULL,
);

#
# Table structure for table 'tx_news_domain_model_news'
#
CREATE TABLE tx_news_domain_model_news (

	application  int(11) unsigned DEFAULT '0' NOT NULL,

);
#
# Table structure for table 'tx_datednews_news_application_mm'
#
CREATE TABLE tx_datednews_news_application_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_datednews_domain_model_location'
#
CREATE TABLE tx_datednews_domain_model_location (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	name varchar(255) DEFAULT '' NOT NULL,
	address2 varchar(255) DEFAULT '' NOT NULL,
	zip varchar(255) DEFAULT '' NOT NULL,
	address varchar(255) DEFAULT '' NOT NULL,
	city varchar(255) DEFAULT '' NOT NULL,
	country varchar(255) DEFAULT '' NOT NULL,
	phone varchar(255) DEFAULT '' NOT NULL,
	email varchar(255) DEFAULT '' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
 KEY language (l10n_parent,sys_language_uid)

);

#
# Table structure for table 'tx_datednews_domain_model_person'
#
CREATE TABLE tx_datednews_domain_model_person (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	name varchar(255) DEFAULT '' NOT NULL,
	surname varchar(255) DEFAULT '' NOT NULL,
	title varchar(255) DEFAULT '' NOT NULL,
	email varchar(255) DEFAULT '' NOT NULL,
	images varchar(255) DEFAULT '' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
 KEY language (l10n_parent,sys_language_uid)

);

#
# Table structure for table 'tx_datednews_news_location_mm'
#
CREATE TABLE tx_datednews_news_location_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_datednews_news_person_mm'
#
CREATE TABLE tx_datednews_news_person_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_datednews_domain_model_person'
#
CREATE TABLE tx_datednews_domain_model_person (
	categories int(11) unsigned DEFAULT '0' NOT NULL,
);

#
# Extend table structure of table 'sys_category'
#
CREATE TABLE sys_category (
  textcolor varchar(255) DEFAULT '' NOT NULL,
	backgroundcolor varchar(255) DEFAULT '' NOT NULL,
);