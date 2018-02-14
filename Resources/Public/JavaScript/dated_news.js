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
    let switchSlotOptions;
    let elRecurrence;
    let el;
    let elems = document.querySelectorAll('.js-reloadfields');
    let requestItems = {};
    //reloadfields
    elems.forEach(function (e) {
        let item = e;
        if (!requestItems.hasOwnProperty(item.getAttribute('data-dn-uid'))) {
            requestItems[item.getAttribute('data-dn-uid')] = [];
        }
        requestItems[item.getAttribute('data-dn-uid')].push(item.getAttribute('data-dn-field'));
    });
    if(elems.length) {
        let httpRequest = new XMLHttpRequest();

        if (!httpRequest) {
            console.error('Giving up :( Cannot create an XMLHTTP instance');
            return false;
        }
        httpRequest.onreadystatechange = function(data, s){
            if (httpRequest.readyState === XMLHttpRequest.DONE) {
                if (httpRequest.status === 200) {
                    let data = httpRequest.responseText;
                if(typeof data === 'string') {
                    data = JSON.parse(data);
                }
                    elems.forEach(function (e) {
                    var item = $(e);
                    if(data.hasOwnProperty(item.attr('data-dn-uid'))){
                        if(data[item.attr('data-dn-uid')].hasOwnProperty(item.attr('data-dn-field'))){
                            item.html(data[item.attr('data-dn-uid')][item.attr('data-dn-field')]);
                        }
                    }
                });
                } else {
                    console.error('There was a problem with the request.');
            }
    }
        };
        httpRequest.open('GET', '?type=6660666&tx_news_pi1[action]=reloadFields&tx_news_pi1[requestItems]=' + JSON.stringify(requestItems));
        httpRequest.send();
    }
    //switch slotoptions
    elRecurrence = document.querySelectorAll('[name="tx_news_pi1[reservedRecurrence]"]')[0];
    if(typeof elRecurrence !== 'undefined') {
        switchSlotOptions = function () {
            let recurrenceDates;
            let selected;
            if (elRecurrence) {
                selected = elRecurrence.options[elRecurrence.selectedIndex].value;
                recurrenceDates = document.querySelectorAll('[name^="tx_news_pi1[reservedSlots-"]');
                recurrenceDates.forEach(function (el) {
                    if (el.getAttribute('name') !== 'tx_news_pi1[reservedSlots-' + selected + ']') {
                        el.style.display = 'none';
                    } else {
                        el.style.display = 'block';

                    }
});
            }

        };
        elRecurrence.addEventListener('change', switchSlotOptions, true);
        switchSlotOptions();
    }
});