/***
 *
 * This file is part of the "Dated News" Extension for TYPO3 CMS.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2017
 *
 * Author Falk Röder <mail@falk-roeder.de>
 *
 *
 *
 * Please do not change anything in here.
 *
 * ### JS AJAX Request reloadfields. ###
 * Usage: Add class "js-reloadfields" to html element with text to change in it. Also add
 * attributes data-dn-uid with the uid of the news record and data-dn-field with fieldname in newsrecord which has to be loaded.
 * Example: <div class="js-reloadfields" data-dn-field="slotsFree" data-dn-uid="{newsItem.uid}" >&nbsp;<!--set via ajax request--></div>
 * Example will check for freeSlots of every event in listview.
 * So listview pages still can be cached even an application has been done to an event. Usually the number of free slots is wring than as it is cached with the news object
 *
 *
 * ### Disabling tooltips if viewport width is smaller than TS Setting viewportMinWidthForTooltip ###
 *
 *
 ***/
var ready = function ( fn ) {
    // Sanity check
    if ( typeof fn !== 'function' ) return;
    // If document is already loaded, run method
    if ( document.readyState === 'complete'  ) {
        return fn();
    }
    // Otherwise, wait until document is loaded
    document.addEventListener( 'DOMContentLoaded', fn, false );
};

ready(function() {
    var elems = $('.js-reloadfields');
    var requestItems = {};
    //reloadfields
    elems.each(function(i, e){
        var item = $(e);
        if(!requestItems.hasOwnProperty(item.attr('data-dn-uid'))){
            requestItems[item.attr('data-dn-uid')] = [];
        }
        requestItems[item.attr('data-dn-uid')].push(item.attr('data-dn-field'));
    });
    if(elems.length) {
        $.ajax({
            url: "?type=6660666&tx_news_pi1[action]=reloadFields&tx_news_pi1[requestItems]=" + JSON.stringify(requestItems),
            contentType: "application/json",
            success: function(data, s){
                if(typeof data === 'string') {
                    data = JSON.parse(data);
                }
                elems.each(function(i, e){
                    var item = $(e);
                    if(data.hasOwnProperty(item.attr('data-dn-uid'))){
                        if(data[item.attr('data-dn-uid')].hasOwnProperty(item.attr('data-dn-field'))){
                            item.html(data[item.attr('data-dn-uid')][item.attr('data-dn-field')]);
                        }
                    }
                });
            }
        });
    }
    //timestamp in applicationform to prevent double sending applications
    var el = document.querySelectorAll('[name="tx_news_pi1[newApplication][formTimestamp]"]');
    if(el.length && el[0].value === '') {
        el[0].value = Math.round(new Date().getTime() / 1000);
    }
    //switch slotoptions
    //...
});
