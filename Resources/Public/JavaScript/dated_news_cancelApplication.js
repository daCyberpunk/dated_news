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
 *
 *
 *
 *
 *
 ***/

var ready = function ( fn ) {
    'use strict';
    if ( typeof fn !== 'function' ) return;
    if ( document.readyState === 'complete'  ) {
        return fn();
    }
    document.addEventListener( 'DOMContentLoaded', fn, false );
};
ready(function() {
    'use strict';
    let forms = document.querySelectorAll('.js-cancel-event');
    var ajaxPost = function (form, url, callback) {
        var url = form.action,
            xhr = new XMLHttpRequest();

        var data = new FormData(form);
        xhr.open("POST", url);
        // xhr.setRequestHeader("Content-type", "application/x-form-urlencoded");

        //.bind ensures that this inside of the function is the XHR object.
        xhr.onload = callback.bind(xhr);

        xhr.send(data);
    }
    var cancelEvent = function(e, form){
        e.preventDefault();
        var cb = function(data){
            console.log(data,':62')
        }
        // ajaxPost(this, '?type=6660668&tx_datednews_pi1[action]=cancel&tx_datednews_pi1[controller]=Application', cb)
        $.ajax({
            url: this.action,
            type: 'post',
            cache: false,
            data: $(this).serialize(),
            success: function(data) {
                console.log(data,':63')
            }
        });
    }
    forms.forEach(function (e) {
        let form = e;
        form.addEventListener("submit", cancelEvent.bind(form), false);
    });





});