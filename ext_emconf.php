<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "dated_news".
 *
 * Auto generated 20-02-2018 09:31
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
  'title' => 'Dated News',
  'description' => 'Extends the TYPO3 versatile news system extension tx_news with a calendar view using fullcalendar.js/qtip.js and allows to book for the now pricable events',
  'category' => 'fe',
  'author' => 'Falk Röder',
  'author_email' => 'mail@falk-roeder.de',
  'state' => 'stable',
  'uploadfolder' => false,
  'createDirs' => '',
  'clearCacheOnLoad' => 1,
  'version' => '6.0.0',
  'constraints' => 
  array (
    'depends' => 
    array (
      'typo3' => '8.0.0-9.5.99',
      'news' => '5.3.0-7.99.99',
      'recurr' => '1.0.0',
    ),
    'conflicts' => 
    array (
    ),
    'suggests' => 
    array (
    ),
  ),
  'clearcacheonload' => true,
  'author_company' => NULL,
);

