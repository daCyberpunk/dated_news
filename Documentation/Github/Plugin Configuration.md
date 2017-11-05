# Plugin Configuration
Most settings are the same like in [tx_news](https://docs.typo3.org/typo3cms/extensions/news/).
## Calendar View

### Tab Calendar View
__1. Switchable views__
   * comma-separated list of views, the FE User can switch. choose from: month, agendaWeek, basicWeek, agendaDay, basicDay, listYear, listMonth, listWeek, listDay

__2. Default View__
   * choose the view the Calendar starts with
   
__3. Show Allday-Slot__
   * Show the allday slot which is available in all agendaviews. If 0, the fulltime events will not be shown in calendar.
   
__4. Show Qtip-Popups__
   * Enable qtips when hovering an events

__5. Show tags/Categories to filter__
   * Include all in rendered events used tags / categories in a tag cloud for use as a calendar filter
   
__6. Sorting Filterlist__
   * Sort the tag List
   
__7. Time when Calendar view starts__
    * First hour which is shown in agenda views.
    
__8. Time when Calendar view ends__
    * Last hour which is shown in agenda views.
    
__9. Aspect Ratio__
    * If set, default value will be overwritten. Determines the width-to-height aspect ratio of the calendar.
    https://fullcalendar.io/docs/display/aspectRatio/
    
__10. Additional fullcalendar configuration__
    * Additional fullcalendaroptions written as JavaScript Object
    * all settings can be taken from 
    https://fullcalendar.io/docs
    * Example:

        ```javascript
        eventLimit: 3,
        views: {
            agenda: {
               eventLimit: 2
            }
        }
        ```


## List/Detail and Detail
### Tab Additional
__1. Page ID for Application / Booking Creation__

   * Chose the page where you install the Plugin with the view Created Application. It's like a confirmation page that a mail was send out but there are happening much more things. 

__2. Page ID for Application / Booking confirmation__

   * not needed here

### Tab Email
__1. Sender__
   * Name and Email of the sender for the mail to the user who wants to registrate and for the mail to the authors and admins.

__2. Admin Mails__
   * Commaseparated list of mailadresses which needs to be informed on a registration

__3. Send Notification to Author__
   * The author of a news/event is informed on registration too. 



## Created Application
Thats the page where a user will be redirected when he sent the registration form. 

__1. Page ID for Application / Booking confirmation__

   * choose the page where the confirmation link will be point to. You need to install a plugin there with the Confirmation View.
 
## Confirm Application
__1. Files to send with booking confirmation__
   * Does not work inside gridelements! If your using gridelements you have to choose the option in typoscript for sending files. 

__2. Field to use for ICS Description__
   * Fields means a field of the news. If you choose teaser, the teaser will be in the ICS Invitation a registrating user gets. 
If you choose custom field, the field has to exist and the given name needs to be the database name of the field. If you dont know that, contact your developer. But in most cases the other options should be enough. 
