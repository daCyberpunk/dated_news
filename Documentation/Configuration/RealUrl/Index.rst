.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

.. _realurl:

RealUrl
=======================

This page shows an example realurl Configuration including the news related configuration.
With this configuration it is possible to have one page with news plugin where the list and detail view can be shown and one page where booking and confirmation takes place.

.. code-block:: php

    $TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT'] = array(
        //... your other realurl stuff
        'fixedPostVars' => array(
            'newsDetailConfiguration' => array(
                array(
                    'GETvar' => 'tx_news_pi1[action]',
                    'valueMap' => array(
                        'eventDetail' => '',
                    ),
                    'noMatch' => 'bypass'
                ),
                array(
                    'GETvar' => 'tx_news_pi1[controller]',
                    'valueMap' => array(
                        'News' => '',
                    ),
                    'noMatch' => 'bypass'
                ),
                array(
                    'GETvar' => 'tx_news_pi1[news]',
                    'lookUpTable' => array(
                        'table' => 'tx_news_domain_model_news',
                        'id_field' => 'uid',
                        'alias_field' => 'title',
                        'addWhereClause' => ' AND NOT deleted',
                        'useUniqueCache' => 1,
                        'useUniqueCache_conf' => array(
                            'strtolower' => 1,
                            'spaceCharacter' => '-'
                        ),
                        'languageGetVar' => 'L',
                        'languageExceptionUids' => '',
                        'languageField' => 'sys_language_uid',
                        'transOrigPointerField' => 'l10n_parent',
                        'autoUpdate' => 1,
                        'expireDays' => 180,
                    )
                )
            ),
            '10' => 'newsDetailConfiguration', // more detail pages can be added here
            'newsBookingConfiguration' => array(
                array(
                    'GETvar' => 'tx_news_pi1[action]',
                    'valueMap' => array(
                        'confirmed' => 'confirmApplication',
                    ),
                    'noMatch' => 'bypass'
                ),
                array(
                    'GETvar' => 'tx_news_pi1[controller]',
                    'valueMap' => array(
                        'News' => '',
                    ),
                    'noMatch' => 'bypass'
                ),
                array(
                    'GETvar' => 'tx_news_pi1[title]',
                    'noMatch' => 'bypass'
                ),
                array(
                    'GETvar' => 'tx_news_pi1[newApplication]',
                    'userFunc' => 'FalkRoeder\\DatedNews\\Hooks\\Realurl->decodeSpURL_getSequence',
                ),
                array(
                    'GETvar' => 'tx_news_pi1[news]',
                    'lookUpTable' => array(
                        'table' => 'tx_news_domain_model_news',
                        'id_field' => 'uid',
                        'alias_field' => 'title',
                        'addWhereClause' => ' AND NOT deleted',
                        'useUniqueCache' => 1,
                        'useUniqueCache_conf' => array(
                            'strtolower' => 1,
                            'spaceCharacter' => '-'
                        ),
                        'languageGetVar' => 'L',
                        'languageExceptionUids' => '',
                        'languageField' => 'sys_language_uid',
                        'transOrigPointerField' => 'l10n_parent',
                        'autoUpdate' => 1,
                        'expireDays' => 180,
                    )
                )
            ),
            '25' => 'newsBookingConfiguration', //more booking/confirmation pages can be added here
        ),
    );

.. _configuration-faq:

FAQ
---

Possible subsection: FAQ