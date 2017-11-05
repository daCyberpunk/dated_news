# Plugin Configuration
## Calendar View
Most settings are the same like in [tx_news](https://docs.typo3.org/typo3cms/extensions/news/).
### Tab Calendar View
1. Switchable views
   * comma-separated list of views, the FE User can switch. choose from: month, agendaWeek, basicWeek, agendaDay, basicDay, listYear, listMonth, listWeek, listDay

2. Default View
   * choose the view the Calendar starts with
   
3. Show Allday-Slot
   * Show the allday slot which is available in all agendaviews. If 0, the fulltime events will not be shown in calendar.
   
4. Show Qtip-Popups
   * Enable qtips when hovering an events

5. Show tags/Categories to filter
   * Include all in rendered events used tags / categories in a tag cloud for use as a calendar filter
   
6. Sorting Filterlist
   * Sort the tag List
   
7. Time when Calendar view starts
    * First hour which is shown in agenda views.
    
8. Time when Calendar view ends
    * Last hour which is shown in agenda views.
    
9. Aspect Ratio
    * If set, default value will be overwritten. Determines the width-to-height aspect ratio of the calendar.
    https://fullcalendar.io/docs/display/aspectRatio/
    
10. Additional fullcalendar configuration
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


## List/Detail
## Detail
## Created Application
## Confirm Application