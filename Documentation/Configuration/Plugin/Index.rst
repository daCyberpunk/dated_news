.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

.. _plugin:

Plugin Settings
=======================

This page is divided into the following sections which are all configurable by using the Plugin:

.. only:: html

   .. contents::
        :local:
        :depth: 1

.. important:: It only describes settings which are essential for the use of this extension. For all others please see also the manual of tx_news. The settings of tx_news should all be fully usable. if not, please do not hesitate to contact me!


.. container:: calendar-view

.. _calendarView:

Calendar View
""""""""""""""""""""

============================================ ============ ========================= ======================================================
Property                                     Default      more info                 Description
============================================ ============ ========================= ======================================================
Switchable Views                                          switchableViews_          write comma-separated list of views, the FE User can switch. choose from: month, agendaWeek, basicWeek, agendaDay, basicDay, listYear, listMonth, listWeek, listDay
Default View                                 month        defaultView_              choose the view the Calendar starts with
Show All-Day-Slot                            0                                      Show the allday slot which is available in all agendaviews. If 0, the fulltime events will not be shown in calendar.
Show tags to filter                          0                                      Include all in rendered news used tags in a tag cloud for use as a calendar filter
Show categories to filter                    0                                      Include all in rendered news used categories in a tag cloud for use as a calendar filter
Time when Calendar view starts               00:00:00     minTime_                  First hour which is shown in agenda views.
Time when Calendar view ends                 24:00:00     maxTime_                  Last hour which is shown in agenda views.
aspectRatio                                  1.35         aspectRatio_              If set, default value will be overwritten. Determines the width-to-height aspect ratio of the calendar.
additional fullcalendar configuration                     additionalConfig_         Additional fullcalendaroptions written as JavaScript Object
============================================ ============ ========================= ======================================================
.. _switchableViews: https://fullcalendar.io/docs/views/Available_Views/
.. _defaultView: https://fullcalendar.io/docs/views/defaultView/
.. _minTime: https://fullcalendar.io/docs/agenda/minTime/
.. _maxTime: https://fullcalendar.io/docs/agenda/maxTime/
.. _aspectRatio: https://fullcalendar.io/docs/display/aspectRatio/

.. _additionalConfig:

Additional Config Field
^^^^^^^^^^^^^^^^^^^^^^^^

Fullcalendar is a huge JS Plugin with many options. Its not possible to bring all configarations into a TYPO3 Frontend Plugin. Thats why it is possible to add additional options in this field. To see all available customizations please visit https://fullcalendar.io/docs/ .

Example::
    .. literalinclude:: FullcalendarAdditional.js
        :linenos:
        :language: javascript
        :lines: 2-7



.. container:: detail-view

.. _eventdetail-view:

Detail View
""""""""""""""""""""

The detail Action of tx_news is replaced by the dated_news action EventDetail. So please be aware, that you after installing this extension, you have to reconfigure the "What to display" setting in all detail news plugins. That also means that the fluid templates like Detail.html are not used anymore. The right templates to use are the EventDetail.html now. Same with partials and layouts.

============================================ ===================================== ======================================================
Property                                     Default                               Description
============================================ ===================================== ======================================================
Page ID for Application / Booking Creation                                         Select the page where the booking creation is shown.
============================================ ===================================== ======================================================


.. _createapplication-view:

.. container:: createapplication-view

Created Application View
"""""""""""""""""""""""""

=============================================== ================================== ======================================================
Property                                        Default                            Description
=============================================== ================================== ======================================================
Page ID for Application / Booking confirmation                                     Select the page where the booking confirmation is shown when customer clicks on his confirmation link received by email.
Page ID for Detail                                                                 Select the page where Detail view is shown. Used for creating links in Mails and confirmations on page
Sender Mail                                                                        Mail which is shown and used as reply in the Email to customer on booking.
Sender Name                                                                        Name which is shown and used as reply in the Email to customer on booking.
Early Bird Time in Days before Event                                               Days before the event starts when the early bird price is available for customer. If event starts at the 4th of February and this is set to three, customer get early bird price when he is booking before 1st of Februray
Files to send with booking confirmation                                            Files to send with booking email. Usually used for payment terms or similar. Please note, that due to a bug it is not usuable when plugin is placed inside a gridelement. For that case you can use the TypoScript option :ref:`settings-filesForMailToApplyer`
=============================================== ================================== ======================================================

.. container:: confirmapplication-view

Confirm Application View
"""""""""""""""""""""""""

=============================================== ================================== ======================================================
Property                                        Default                            Description
=============================================== ================================== ======================================================
Page ID for Detail                                                                 Select the page where Detail view is shown. Used for creating links in Mails and confirmations on page
Sender Mail                                                                        Mail which is shown and used as reply in the Email to customer on booking.
Sender Name                                                                        Name which is shown and used as reply in the Email to customer on booking.
Admin Mails for notification                                                       Mail address(es) where information about the successfull booking should be send. Comma separated
Send Notification to Author                     0                                  If set, the author of an ecent (news) is also informed on successfull booking.
Send ICS Invitation to Customer                 0                                  If set, the customer receives an extra email with an calendar invitation for the event
Content to use for ICS Description              none                               Field of event (news) which is used for the description of the ICS invitation. Should not be contain html like the teaser could have if enabled in tx_news Extension or the bodytext definitly will have. If "locallang + url" is used, the description can written in locallang file and a url will be appended to the description.
Custom news Item Field                                                             Field name of event (news) which is used for the description of the ICS invitation. Same hint here like the "Content to use for ICS Description" field above concerning html content.
=============================================== ================================== ======================================================





