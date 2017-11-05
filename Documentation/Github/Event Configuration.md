# Event Configuration
Configure/add your event as any news item before. The item is extended with a lot of event options tried to explain here:

##Tab General
__1.Event Type (Structured Data -> Schema.org)__

if you choose "not an event" the news will be handled as normal news. 
The other options specifiy the event type as of schema .org. Inside the Fluid Template EventDetail you can see allready integrated the needed microdata markup. For more details about this see 
https://schema.org

##Tab Event Data
__1. Show in Calendar__
   * Flag if the event should be shown in calendarview or not
   
__2. User can book / Applicate__
   * Flag if User should be able to registrate for an event. In Fluid a condition is used to hide/show the application form.

__3. Text / Background Color (calendar view)__
   * Colors in which the event appears in calendar. This overwrites the color settings in categories.
   
__4. Event Start / End / Allday__
   * ofcourse, you know start and end.... Allday just means the event is the whole day. If in Calendar view the Allday slot is enabled, this event will be shown in agenda views on top of the agenda. 

__5. Recurrence__
   * a lot of possible event recurrence options. Userdefined opens a new tab where you can configure any imaginable recurrences.
   
__6. Target Group__
   * Just a text field which will be available in Fluid templates. You of course can use it for any additional inofrmation you want to provide to the user. 
   
__7. Slots available / Prices and Earlybirds__
   * Number of users whcih can registrate to an event. Prices and earlybird configuration you understand too. 
   
__8. Locations / Persons__
   * You can have records of locations and persons and add them here. 
   
__9. Application Bookings__
   * Applications / Registrations by users will be shown here if the event is an single Event (no recurrences)!
   
##Tab Recurrence Overwrites


__1. Behavior of building and overriding recurring events.__

__Thats an really important option! Handly it carefully!__ 
   * __do nothing__
   
     Just stores the data you entered before without touching existing recurrences or building new ones. 

   * __build/overwrite__
     
     If your event is configured as an event which will recurr, then with this option on save the recurring events will be generated. 
     All existing recurrences will be deleted and the existing applications to it are lost!
     
   * __rebuild all none modified__
   
     Creates all recurring events. Events where user already registered or you manually changed something (check flag modified!) will not be overwritten. If on rebuilding a new recurrence matches an existing modified version, it will not be rebuilded.
     
   * __overwrite all fields in all events__
     
     usefull if you changed some text and don't want wo do it for all your recurrences of the event. 
     
   * __overwrite all fields in all none modified events__
   
     The same but if an application exists or the modified flag is set in the recurrence of the event, that recurrence will not be changed. 
     
   * __overwrite only changed fields in all events__
   
     -- and so further......
     
 __2. News Recurrence__
 
 All builded recurrences will be shown here. You can change details in them but than you should set the flag modified so it might not be changed when you rebuild something. See the option above for more details on this flag.
 
 All registrations/Bookings to recurrences of an event, you can see here. 
 
 