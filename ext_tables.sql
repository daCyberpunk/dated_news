#
# Table structure for table 'tx_news_domain_model_news'
#
CREATE TABLE tx_news_domain_model_news (

	showincalendar tinyint(1) unsigned DEFAULT '0' NOT NULL,
	fulltime tinyint(1) unsigned DEFAULT '0' NOT NULL,
	eventstart datetime DEFAULT '0000-00-00 00:00:00',
	eventend varchar(255) DEFAULT '' NOT NULL,
	eventlocation varchar(255) DEFAULT '' NOT NULL,
	textcolor varchar(255) DEFAULT '' NOT NULL,
	backgroundcolor varchar(255) DEFAULT '' NOT NULL,

);

#
# Table structure for table 'tx_news_domain_model_news'
#
CREATE TABLE tx_news_domain_model_news (
	categories int(11) unsigned DEFAULT '0' NOT NULL,
);
