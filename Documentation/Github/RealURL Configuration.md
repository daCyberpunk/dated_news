# Example realURL Configuration
```php
$TYPO3_CONF_VARS['FE']['addRootLineFields'].= ',tx_realurl_pathsegment';
$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT'] = array(
    /*... other stuff*/
    'fixedPostVars' => array(
        'newsDetailConfiguration' => array(
            array(
                'GETvar' => 'tx_news_pi1[action]',
                'valueMap' => array(
                    'detail' => '',
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
        'eventDetailConfiguration' => array(
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
        'newsCategoryConfiguration' => array(
            array(
                'GETvar' => 'tx_news_pi1[overwriteDemand][categories]',
                'lookUpTable' => array(
                    'table' => 'tx_news_domain_model_category',
                    'id_field' => 'uid',
                    'alias_field' => 'title',
                    'addWhereClause' => ' AND NOT deleted',
                    'useUniqueCache' => 1,
                    'useUniqueCache_conf' => array(
                        'strtolower' => 1,
                        'spaceCharacter' => '-'
                    )
                )
            )
        ),
        'newsTagConfiguration' => array(
            array(
                'GETvar' => 'tx_news_pi1[overwriteDemand][tags]',
                'lookUpTable' => array(
                    'table' => 'tx_news_domain_model_tag',
                    'id_field' => 'uid',
                    'alias_field' => 'title',
                    'addWhereClause' => ' AND NOT deleted',
                    'useUniqueCache' => 1,
                    'useUniqueCache_conf' => array(
                        'strtolower' => 1,
                        'spaceCharacter' => '-'
                    )
                )
            )
        ),
        '24' => 'newsDetailConfiguration',
        '26' => 'eventDetailConfiguration',
        '25' => 'newsBookingConfiguration',
        '7' => 'newsTagConfiguration',
    ),
    'postVarSets' => array(
        '_DEFAULT' => array(
            'controller' => array(
                array(
                    'GETvar' => 'tx_news_pi1[action]',
                    'noMatch' => 'bypass'
                ),
                array(
                    'GETvar' => 'tx_news_pi1[controller]',
                    'noMatch' => 'bypass'
                ),
            ),

            'dateFilter' => array(
                array(
                    'GETvar' => 'tx_news_pi1[overwriteDemand][year]',
                ),
                array(
                    'GETvar' => 'tx_news_pi1[overwriteDemand][month]',
                ),
            ),
            'page' => array(
                array(
                    'GETvar' => 'tx_news_pi1[@widget_0][currentPage]',
                ),
            ),
        ),
    ),
    /*...other stuff*/

);
```