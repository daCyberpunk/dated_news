"use strict";
/*
* Please do not change anything in here.
*
* Adding Filterability to fullcalender Events by tags and/or categories.
* Disabling tooltips if viewport width is smaller than TS Setting viewportMinWidthForTooltip
*
* Author Falk Röder <mail@falk-roeder.de>
* package TYPO3
* subpackage dated_news
*
* */
var DatedNews = {
    filterAdded: [],
    eventscal: [],
    init: function(){
        $('.dated-news-filter').on('click', function(){
            var $this = $(this),
                cal = $('#calendar.calendar_' + $this.attr('data-dn-calendar')),
                event = "newsCalendarEvent_" + $this.attr('data-dn-calendar');

            $this.hasClass('dn-checked') ? $this.removeClass('dn-checked') : $this.addClass('dn-checked');
            var dnchecked = $('.dated-news-filter.dn-checked');
            if (!dnchecked.length) {
                // wenn nix gechecked dann alle adden
                for (var key in DatedNews.filterAdded) {
                    if (key == event) {
                        DatedNews.filterAdded = DatedNews.filterAdded.splice(key, 1);
                    }
                }
                cal.fullCalendar( 'refetchEvents' );
            } else {
                var filter = [];
                dnchecked.each(function(){
                    filter.push($(this).data('dn-filter'));
                });

                DatedNews.filterCalendarEvents(filter, cal, event);
            }

        });
        $(document).on('click','a.fc-day-grid-event, .fc-content, a.fc-time-grid-event', function(e){
            if(DatedNews.getViewport()['width'] > $('.fc-calendar-container').first().attr('data-qtipminwidth')){
                if($(this).closest('.fc-calendar-container').hasClass('has-qtips')){
                    e.preventDefault();
                }
            }
        });
        DatedNews.callAfterResize(function(){
            DatedNews.disableQtips();
        });
        $(window).on('load',DatedNews.disableQtips);
    },
    removeAllEvents: function(cal, event){
        for (var key in DatedNews.eventscal[event]) {
            if (DatedNews.eventscal[event].hasOwnProperty(key)) {
                cal.fullCalendar( 'removeEvents', DatedNews.eventscal[event][key]['events'][0]['id'] )
            }
        }
    },
    filterCalendarEvents: function(filter, cal, event){
        if(filter.length === 0) {
            // DatedNews.addAllEvents(cal, event);
            return;
        }
        DatedNews.removeAllEvents(cal, event);
        if (!DatedNews.filterAdded.hasOwnProperty(event)){
            DatedNews.filterAdded[event] = [];
        }
        //filter tags will be stored for time when calendarview changes filter should be applied again.

        var added = [];
        var tmpDatedNewsFilterAdded = [];
        tmpDatedNewsFilterAdded[event] = [];
        for (var i=0; i < filter.length; i++) {
            tmpDatedNewsFilterAdded[event].push(filter[i]);
            for (var key in DatedNews.newsCalendarTags[filter[i]]) {
                if (DatedNews.newsCalendarTags[filter[i]].hasOwnProperty(key)) {
                    //make sure event wasn't added before
                    if (!added['Event_'+DatedNews.newsCalendarTags[filter[i]][key]]) {
                        cal.fullCalendar( 'renderEvent', DatedNews.eventscal[event]['Event_'+DatedNews.newsCalendarTags[filter[i]][key]]['events'][0] );
                        added['Event_'+DatedNews.newsCalendarTags[filter[i]][key]] = 1;
                    }
                }
            }
        }
        DatedNews.filterAdded[event] = tmpDatedNewsFilterAdded[event];
    },
    getViewport: function(){
        var e = window, a = 'inner';
        if (!('innerWidth' in window )) {
            a = 'client';
            e = document.documentElement || document.body;
        }
        return { width : e[ a+'Width' ] , height : e[ a+'Height' ] };
    },
    callAfterResize: function(callback){
        $(window).bind('resizeEnd', function () {
            callback();
        });

        $(window).resize(function () {
            if (this.resizeTO) clearTimeout(this.resizeTO);
            this.resizeTO = setTimeout(function () {
                $(this).trigger('resizeEnd');
            }, 500)
        });
    },
    disableQtips: function(){
        //disable qtip on devices smaller then 768px width and follow direct the url to an event on click
        if(DatedNews.getViewport()['width'] > $('.fc-calendar-container').first().attr('data-qtipminwidth')){
            $('[data-hasqtip]').qtip('enable');
        } else {
            $('[data-hasqtip]').qtip('disable');
        }
    }
};
DatedNews.init();
