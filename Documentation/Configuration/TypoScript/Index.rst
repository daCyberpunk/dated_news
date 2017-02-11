.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

.. _ts:

TypoScript
=======================

This page is divided into the following sections which are all configurable by using TypoScript:


.. container:: ts-properties


	=========================== ===================================== ======================================================
	Property                    Data type                             Default
	=========================== ===================================== ======================================================
	twentyfourhour_             :ref:`t3tsref:data-type-boolean`      1
	tooltipPreStyle_            :ref:`t3tsref:data-type-string`
   	uiThemeCustom_              :ref:`t3tsref:data-type-string`       empty
	uiTheme_                    :ref:`t3tsref:data-type-string`       lightness
	cssFile_                    :ref:`t3tsref:data-type-path`         EXT:dated_news/Resources/Public/CSS/dated_news.css
	titlePosition_              :ref:`t3tsref:data-type-string`       header_left
	switchableViewsPosition_    :ref:`t3tsref:data-type-string`       header_center
	prevPosition_               :ref:`t3tsref:data-type-string`       header_right
	nextPosition_               :ref:`t3tsref:data-type-string`       header_right
	todayPosition_              :ref:`t3tsref:data-type-string`       header_right
	validDaysConfirmationLink_  :ref:`t3tsref:data-type-int`          3
	filesForMailToApplyer_      :ref:`t3tsref:data-type-string`
	=========================== ===================================== ======================================================


.. _settings-twentyfourhour:

twentyfourhour
""""""""""""""""""""
.. container:: table-row

   Property
         twentyfourhour
   Data type
         boolean
   Description
         if checked (default) the time in calendar will be shown in 24 hours format (e.g. 23:00); If not the 23:00 is changing to 11:00pm.



.. _settings-tooltipPreStyle:

tooltipPreStyle
""""""""""""""""""""
.. container:: table-row

   Property
         tooltipPreStyle
   Data type
         string
   Description
         here you can choose some preconfigured styles for the tooltip which is shwon if you hover an event in calendar.




.. _settings-uiTheme:

uiTheme
"""""""""""""""
.. container:: table-row

   Property
         uiTheme
   Data type
         string
   Description
        here you can choose some preconfigured styles for the calendar (not the tooltips).
        These styles are jQuery Themes. For the option *custom* you need to set uiThemeCustom_ as well.


.. _settings-uiThemeCustom:

uiThemeCustom
"""""""""""""""
.. container:: table-row

   Property
         uiThemeCustom
   Data type
         string
   Description
         here you can write down the name of your own calendar theme, maybe an other jQuery theme. The extension looks for an folder with this name in *typo3conf/ext/dated_news/Resources/Public/CSS/jqueryThemes/* and there for the files *jquery-ui.css* and *jquery-ui.theme.css* to load.



.. _settings-cssFile:

cssFile
"""""""""""""""
.. container:: table-row

   Property
         cssFile
   Data type
         path
   Description
         Path to Dated News CSS



.. _settings-titlePosition:

titlePosition
"""""""""""""""
.. container:: table-row

   Property
         titlePosition
   Data type
         string
   Description
         Determines where the Calendar title is positioned.

.. _settings-switchableViewsPosition:

switchableViewsPosition
""""""""""""""""""""""""
.. container:: table-row

   Property
         switchableViewsPosition
   Data type
         string
   Description
         Determines where the view buttons are positioned.

.. _settings-prevPosition:

prevPosition
"""""""""""""""
.. container:: table-row

   Property
         prevPosition
   Data type
         string
   Description
         Determines where the prev button title is positioned.

.. _settings-nextPosition:

nextPosition
"""""""""""""""
.. container:: table-row

   Property
         nextPosition
   Data type
         string
   Description
         Determines where the next button title is positioned.

.. _settings-todayPosition:

todayPosition
"""""""""""""""
.. container:: table-row

   Property
         todayPosition
   Data type
         string
   Description
         Determines where the today button title is positioned.

.. _settings-validDaysConfirmationLink:

validDaysConfirmationLink
""""""""""""""""""""""""""
.. container:: table-row

   Property
         validDaysConfirmationLink
   Data type
         integer
   Description
         Determines how many days the confirmationlink will be usable, when a customer is applicate / booking for an event.

.. _settings-filesForMailToApplyer:

filesForMailToApplyer
""""""""""""""""""""""""""
.. container:: table-row

   Property
         filesForMailToApplyer
   Data type
         string
   Description
         :typoscript:`plugin.tx_news.settings.dated_news.filesForMailToApplyer = /uploads/tx_datednews/aaaaaaaaaaaaa.pdf, /uploads/tx_datednews/bbbbbbbbbbbbb.pdf`
         Files to send with booking email. Usually used for payment terms or similar.



Tag based Filtering
^^^^^^^^^^^^^^^^^^^^

The Extension adds the possibility to filter events based on their tags you can add to the news items.
You just have to build a list of tag-items where every item has the class *dated-news-filter* and a data attribute *data-dn-filter* with the tag title in it. Luckily the news extension comes allready with a tag-template. The code you add there could look like the following:
Q

::

	<f:if condition="{tags}">

       <div class="button-group filters-button-group">

            <f:for each="{tags}" as="tag">

               <button class="dated-news-filter button" data-dn-filter="{tag.title}">{tag.title}</button>

            </f:for>

        </div>

    </f:if>





.. _configuration-faq:

FAQ
---

Possible subsection: FAQ