
"use strict";
function addAllEvents(cal, event){
    for (var key in eventscal[event]) {
        if (eventscal[event].hasOwnProperty(key)) {
            cal.fullCalendar( 'addEventSource', eventscal[event][key] );
        }
    }
}
function removeAllEvents(cal, event){
    for (var key in eventscal[event]) {
        if (eventscal[event].hasOwnProperty(key)) {
            cal.fullCalendar( 'removeEventSource', eventscal[event][key] )
        }
    }
}

$('.dated-news-filter').on('click', function(){
    var $this = $(this),
        cal = $('#calendar.calendar_' + $this.attr('data-dn-calendar')),
        event = "newsCalendarEvent_" + $this.attr('data-dn-calendar');
    removeAllEvents(cal, event);
    $this.hasClass('dn-checked') ? $this.removeClass('dn-checked') : $this.addClass('dn-checked');
    var dnchecked = $('.dated-news-filter.dn-checked');
    // wenn nix gechecked dann alle adden
    if (!dnchecked.length) {
        addAllEvents(cal, event);
    } else {
        var added =[];
        dnchecked.each(function(){
            var filter = $(this).data('dn-filter');
            for (var key in newsCalendarTags[filter]) {
                if (newsCalendarTags[filter].hasOwnProperty(key)) {
                    //make sure event wasn't added before
                    if (!added['Event_'+newsCalendarTags[filter][key]]) {
                        cal.fullCalendar( 'addEventSource', eventscal[event]['Event_'+newsCalendarTags[filter][key]] )
                        added['Event_'+newsCalendarTags[filter][key]] = 1;
                    }
                }
            }
        });
    }
});
//disable qtip on devices smaller then 768px width and follow direct the url to an event on click
$(document).on('click','a.fc-day-grid-event, .fc-content, a.fc-time-grid-event', function(e){
    if(getViewport()['width'] > 767){
        e.preventDefault();
    } else {
        $('[data-hasqtip]').qtip('hide').qtip('disable');
    }
});

function getViewport() {
    var e = window, a = 'inner';
    if (!('innerWidth' in window )) {
        a = 'client';
        e = document.documentElement || document.body;
    }
    return { width : e[ a+'Width' ] , height : e[ a+'Height' ] };
}