.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _admin-manual:

Installation Manual
====================

.. _admin-requirements:

Requirements
------------

1. TYPO3 CMS 7.6 - 8.7
2. tx_news 5.3.0 - 6.1.99

.. _admin-installation:

Installation
------------

1. Switch to the module “Extension Manager”.
2. Get the extension from the Extension Manager: Press the “Retrieve/Update” button and search for the extension key dated_news and import the extension from the repository.

.. _admin-include-typoscript:

Include static Typoscript
-------------------------

The extension ships some TypoScript code which needs to be included.

1. Switch to the root page of your site.
2. Switch to the Template module and select *Info/Modify*.
3. Press the link *Edit the whole template record* and switch to the tab Includes.
4. Select *Dated News (dated_news)* at the field Include static (from extensions):




