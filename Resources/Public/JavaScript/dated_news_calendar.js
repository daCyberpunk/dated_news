"use strict";
// function addAllEvents(cal, event){
//     for (var key in eventscal[event]) {
//         if (eventscal[event].hasOwnProperty(key)) {
//             cal.fullCalendar( 'refetchEvents' );
//         }
//     }
// }
function removeAllEvents(cal, event){
    for (var key in eventscal[event]) {
        if (eventscal[event].hasOwnProperty(key)) {
            cal.fullCalendar( 'removeEvents', eventscal[event][key]['events'][0]['id'] )
        }
    }
}

$('.dated-news-filter').on('click', function(){
    var $this = $(this),
        cal = $('#calendar.calendar_' + $this.attr('data-dn-calendar')),
        event = "newsCalendarEvent_" + $this.attr('data-dn-calendar');

    $this.hasClass('dn-checked') ? $this.removeClass('dn-checked') : $this.addClass('dn-checked');
    var dnchecked = $('.dated-news-filter.dn-checked');
    if (!dnchecked.length) {
        // wenn nix gechecked dann alle adden
        for (var key in DatedNewsFilterAdded) {
            if (key == event) {
                DatedNewsFilterAdded = DatedNewsFilterAdded.splice(key, 1);
            }
        }
        cal.fullCalendar( 'refetchEvents' );
    } else {
        var filter = [];
        dnchecked.each(function(){
            filter.push($(this).data('dn-filter'));
        });

        filterCalendarEvents(filter, cal, event);
    }

});
var DatedNewsFilterAdded =[];

var filterCalendarEvents = function(filter, cal, event){
    if(filter.length === 0) {
        addAllEvents(cal, event);
        return;
    }
    removeAllEvents(cal, event);
    if (!DatedNewsFilterAdded.hasOwnProperty(event)){
        DatedNewsFilterAdded[event] = [];
    }
    //filter tags will be stored for time when calendarview changes filter should be applied again.

    var added = [];
    var tmpDatedNewsFilterAdded = [];
    tmpDatedNewsFilterAdded[event] = [];
    for (var i=0; i < filter.length; i++) {
        tmpDatedNewsFilterAdded[event].push(filter[i]);
        for (var key in newsCalendarTags[filter[i]]) {
            if (newsCalendarTags[filter[i]].hasOwnProperty(key)) {
                //make sure event wasn't added before
                if (!added['Event_'+newsCalendarTags[filter[i]][key]]) {
                    cal.fullCalendar( 'renderEvent', eventscal[event]['Event_'+newsCalendarTags[filter[i]][key]]['events'][0] );
                    added['Event_'+newsCalendarTags[filter[i]][key]] = 1;
                }
            }
        }
    }
    DatedNewsFilterAdded[event] = tmpDatedNewsFilterAdded[event];
};

function getViewport() {
    var e = window, a = 'inner';
    if (!('innerWidth' in window )) {
        a = 'client';
        e = document.documentElement || document.body;
    }
    return { width : e[ a+'Width' ] , height : e[ a+'Height' ] };
}

function callAfterResize(callback) {
    $(window).bind('resizeEnd', function () {
        callback();
    });

    $(window).resize(function () {
        if (this.resizeTO) clearTimeout(this.resizeTO);
        this.resizeTO = setTimeout(function () {
            $(this).trigger('resizeEnd');
        }, 500)
    });
};
function disableQtips(){
    //disable qtip on devices smaller then 768px width and follow direct the url to an event on click
    if(getViewport()['width'] > $('.fc-calendar-container').first().attr('data-qtipminwidth')){
        $('[data-hasqtip]').qtip('enable');
    } else {
        $('[data-hasqtip]').qtip('disable');
    }
};
$(document).on('click','a.fc-day-grid-event, .fc-content, a.fc-time-grid-event', function(e){
    if(getViewport()['width'] > $('.fc-calendar-container').first().attr('data-qtipminwidth')){
        if($(this).closest('.fc-calendar-container').hasClass('has-qtips')){
            e.preventDefault();
        }
    }
});
callAfterResize(function(){
    disableQtips();
});
$(window).on('load',disableQtips);