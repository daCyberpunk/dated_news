.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

.. _news:

News
====

This page is divided into the following sections which are all configurable by using the Plugin:

:ref:`generalTab`

:ref:`eventTab`

:ref:`optionTab`


.. important:: It only describes new fields which are essential or self-explanatory for the use of news records as a calendar item. For all others please see also the manual of tx_news.


.. container:: general-tab

.. _generalTab:

General
""""""""""""""""""""

============================================ =============== ======================================================
Field                                        Default         Description
============================================ =============== ======================================================
Event Type                                   is not an event Default value displays news at is delivered by the tx_news extension. Also no Event tab is shown. Other options providing Event Types described by http://schema.org . The Fluid Template delivered by this extension includes already all for the event needed meta data. If an event type is set, the :ref:`eventTab` Tab is shown.
============================================ =============== ======================================================



.. container:: event-tab

.. _eventTab:

""""""""""""""
Event Data
""""""""""""""

============================================ =============== ======================================================
Field                                        Default         Description
============================================ =============== ======================================================
Text Color                                                   Defines the text color an event get in Calendar view. Overwrites the color setting in Category which might be set.
Background Color                                             Defines the background an event get in Calendar view. Overwrites the color setting in Category which might be set.
============================================ =============== ======================================================


.. container:: option-tab

.. _optionTab:

""""""""""""""
Option
""""""""""""""

============================================ =============== ======================================================
Field                                        Default         Description
============================================ =============== ======================================================
Category                                                     As usual it defines the category of a news record. These categories can be used for filtering events. See :ref:`calendarView`.
Tags                                                         As usual you can add tags to a news record. These tags can be used for filtering events. See :ref:`calendarView`.
============================================ =============== ======================================================

