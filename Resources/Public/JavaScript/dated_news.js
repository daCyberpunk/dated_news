/*
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
 * Author Falk Röder <mail@falk-roeder.de>
 * package TYPO3
 * subpackage dated_news
 *
 * */
!function(){function g(){if(!e&&(e=!0,f)){for(var a=0;a<f.length;a++)f[a].call(window,[]);f=[]}}function h(a){var b=window.onload;"function"!=typeof window.onload?window.onload=a:window.onload=function(){b&&b(),a()}}function i(){if(!d){if(d=!0,document.addEventListener&&!c.opera&&document.addEventListener("DOMContentLoaded",g,!1),c.msie&&window==top&&function(){if(!e){try{document.documentElement.doScroll("left")}catch(a){return void setTimeout(arguments.callee,0)}g()}}(),c.opera&&document.addEventListener("DOMContentLoaded",function(){if(!e){for(var a=0;a<document.styleSheets.length;a++)if(document.styleSheets[a].disabled)return void setTimeout(arguments.callee,0);g()}},!1),c.safari){var a;!function(){if(!e){if("loaded"!=document.readyState&&"complete"!=document.readyState)return void setTimeout(arguments.callee,0);if(void 0===a){for(var b=document.getElementsByTagName("link"),c=0;c<b.length;c++)"stylesheet"==b[c].getAttribute("rel")&&a++;var d=document.getElementsByTagName("style");a+=d.length}if(document.styleSheets.length!=a)return void setTimeout(arguments.callee,0);g()}}()}h(g)}}var a=window.DomReady={},b=navigator.userAgent.toLowerCase(),c={version:(b.match(/.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/)||[])[1],safari:/webkit/.test(b),opera:/opera/.test(b),msie:/msie/.test(b)&&!/opera/.test(b),mozilla:/mozilla/.test(b)&&!/(compatible|webkit)/.test(b)},d=!1,e=!1,f=[];a.ready=function(a,b){i(),e?a.call(window,[]):f.push(function(){return a.call(window,[])})},i()}();
DomReady.ready(function() {
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