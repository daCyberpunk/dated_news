<?php

/***************************************************************
 * Extension Manager/Repository config file for ext: "dated_news"
 *
 * Auto generated by Extension Builder 2015-07-31
 *
 * Manual updates:
 * Only the data in the array - anything else is removed by next write.
 * "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Dated News',
	'description' => 'Extends the TYPO3 versatile news system extension tx_news with a calendar view using fullcalendar.js',
	'category' => 'fe',
	'author' => 'Falk Röder',
	'author_email' => 'mail@falk-roeder.de',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => '0',
	'createDirs' => '',
	'clearCacheOnLoad' => 0,
	'version' => '3.4.0',
	'constraints' => array(
		'depends' => array(
			'typo3' => '7.6.0-8.3.99',
			'news' => '4.0.0-5.2.99',
			'vhs' => '3.0.0-3.0.99'
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
);