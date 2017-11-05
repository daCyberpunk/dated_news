# Fluid Templates
The Extension ships with some preconfigured Templates. 

> Important Note: The Extension ships with an extended Version of news link viewhelper. When you link to Detail Pages of events, you will ned this. Because the original can not link to action eventDetail, and so the Uris always wrong. Also the extended VH can create absolute urls, whats needed in E-Mails. 
    

## Views:
__Calendar__
...the famous Calendar

__CreateApplication__
What you see when you send the booking

__ConfirmApplication__
What you see when you confirmed the booking

__EventDetail__
The Event Detailview. Like the News Detail, but with more Details. 

Some more extended FE Development is needed here. open an event in this view, shows allways all days and in the booking form there are "reserve seats" fields for each recurrence of an event. You can mainipulate this with a bit of Javascript.



__Qtip__ This you will find in Partials/Calendar. Its the little pop opens by hovering a event in calendar. You can put any information their you want from the event. 

## Email:
__MailApplicationApplyer__ Send to the registrating user 

__MailApplicationNotification__ send to admins and authors 

__MailConfirmationApplyer__ send to the user after confirmation

