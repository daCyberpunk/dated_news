
window.onload = function(){


    var d = document,
        findAncestor = function(el, cls) {
            while ((el = el.parentElement) && !el.classList.contains(cls));
            return el;
        },
        fEventType = d.querySelectorAll('[name$="[eventtype]"]'),
        fRecurrence = d.querySelectorAll('[name$="[recurrence]"]'),
        fRecurrenceGroup = d.querySelectorAll('[name$="[recurrence_type]"],[data-formengine-input-name$="[recurrence_until]"],[data-formengine-input-name$="[recurrence_count]"],[name$="[ud_type]"]'),
        fRecurrenceType = d.querySelectorAll('[name$="[recurrence_type]"]'),
        fRecurrenceUntil = d.querySelectorAll('[name$="[recurrence_until]"]'),
        fRecurrenceCount = d.querySelectorAll('[data-formengine-input-name$="[recurrence_count]"]'),
        fRecurrenceUpdateBehavior = d.querySelectorAll('[name$="[recurrence_updated_behavior]"]'),


        fUdType = d.querySelectorAll('[name$="[ud_type]"]'),
        fUdMonthlyBase = d.querySelectorAll('[name$="[ud_monthly_base]"]'),
        fUdMonthlyPerdayWeekdays = d.querySelectorAll('[name$="[ud_monthly_perday_weekdays]"]'),
        fUdMonthlyPerday = d.querySelectorAll('[data-formengine-input-name$="[ud_monthly_perday]"]'),
        fUdMonthlyPerdateDay = d.querySelectorAll('[data-formengine-input-name$="[ud_monthly_perdate_day]"]'),
        fUdMonthlyPerdateLastday = d.querySelectorAll('[data-formengine-input-name$="[ud_monthly_perdate_lastday]"]'),
        fUdYearlyPerdayCheck = d.querySelectorAll('[data-formengine-input-name$="[ud_yearly_perday]"]'),
        fUdYearlyPerdayWeekdays = d.querySelectorAll('[name$="[ud_yearly_perday_weekdays]"]'),

        paletteDaily = findAncestor(d.querySelectorAll('[data-formengine-input-name$="[ud_daily_everycount]"]')[0],'form-section'),
        paletteWeekly = findAncestor(d.querySelectorAll('[data-formengine-input-name$="[ud_weekly_everycount]"]')[0],'form-section'),
        paletteMonthly = findAncestor(d.querySelectorAll('[data-formengine-input-name$="[ud_monthly_everycount]"]')[0],'form-section'),
        paletteYearly = findAncestor(d.querySelectorAll('[data-formengine-input-name$="[ud_yearly_everycount]"]')[0],'form-section')
        ;




    

    var fRecurrenceChange,
        fRecurrenceTypeChange,
        fUdTypeChange,
        fUdMonthlyBaseChange,
        fUdYearlyPerdayCheckClick,
        fRecurrenceUpdateBehaviorChange,
        addEvent,
        showFields = function(fieldArray,changeColor){
            for(var i = 0; i < fieldArray.length; i++){
                var el = findAncestor(fieldArray[i], 't3js-formengine-palette-field');
                if(el.style.display=== 'none') {
                    el.style.display = 'block';
                    if(changeColor && changeColor !== false) {
                        fadeColor(el,'#FF0000','#fafafa','1000');
                    }
                }
            }
        },
        hideFields = function(fieldArray){
            for(var i = 0; i < fieldArray.length; i++){
                var el = findAncestor(fieldArray[i], 't3js-formengine-palette-field');
                el.style.display = 'none';
            }
        },
        hideTab = function(fieldArray){
            for(var i = 0; i < fieldArray.length; i++){
                var el = findAncestor(fieldArray[i], 'tab-pane');
                var AriaVal = el.getAttribute('id');
                var tabLink = d.querySelectorAll('[aria-controls="' + AriaVal + '"]');
                var tabMenuItem = findAncestor(tabLink[i], 't3js-tabmenu-item');

                tabMenuItem.style.display = 'none';
            }
        },
        showTab = function(fieldArray,changeColor){
            for(var i = 0; i < fieldArray.length; i++){
                var el = findAncestor(fieldArray[i], 'tab-pane');
                var AriaVal = el.getAttribute('id');
                var tabLink = d.querySelectorAll('[aria-controls="' + AriaVal + '"]');
                var tabMenuItem = findAncestor(tabLink[i], 't3js-tabmenu-item');

                if(tabMenuItem.style.display === 'none') {
                    tabMenuItem.style.display = 'block';
                    if(changeColor && changeColor !== false) {
                        fadeColor(tabMenuItem,'#FF0000','#ededed','1000');
                    }
                }
            }
        },
        showPalette = function(palette,changeColor){
                if(palette.style.display === 'none') {
                    palette.style.display = 'block';
                    if(changeColor && changeColor !== false) {
                        fadeColor(palette,'#FF0000','#ededed','1000');
                    }
                }
        },
        hidePalette = function(palette){
                palette.style.display = 'none';
        },
        fadeColor = function(el,startBgColor, endBgColor, fadeTime){
            var nlbFade_hextable = [ '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F' ]; // used for RGB to Hex and Hex to RGB conversions
            var nlbFade_elemTable = new Array( ); // global array to keep track of faded elements
            var nlbFade_t = new Array( ); // global array to keep track of fading timers
            /*
             * NLB Background Color Fader v1.0
             * Author: Justin Barlow - www.netlobo.com
             * */
            function NLBfadeBg( elementId, startBgColor, endBgColor, fadeTime ){
                var timeBetweenSteps = Math.round( Math.max( fadeTime / 300, 30 ) );
                var nlbFade_elemTableId = nlbFade_elemTable.indexOf( elementId );
                if( nlbFade_elemTableId > -1 )
                {
                    for( var i = 0; i < nlbFade_t[nlbFade_elemTableId].length; i++ )
                        clearTimeout( nlbFade_t[nlbFade_elemTableId][i] );
                }
                else
                {
                    nlbFade_elemTable.push( elementId );
                    nlbFade_elemTableId = nlbFade_elemTable.indexOf( elementId );
                }
                var startBgColorRGB = hexToRGB( startBgColor );
                var endBgColorRGB = hexToRGB( endBgColor );
                var diffRGB = new Array( );
                for( var i = 0; i < 3; i++ )
                    diffRGB[i] = endBgColorRGB[i] - startBgColorRGB[i];
                var steps = Math.ceil( fadeTime / timeBetweenSteps );
                var nlbFade_s = new Array( );
                var setColor = function(el, color){
                    el.style.backgroundColor = color;
                };
                for( var i = 1; i <= steps; i++ )
                {
                    var changes = new Array( );
                    for( var j = 0; j < diffRGB.length; j++ )
                        changes[j] = startBgColorRGB[j] + Math.round( ( diffRGB[j] / steps ) * i );
                    if( i == steps )
                        nlbFade_s[i - 1] = setTimeout( setColor.bind(null,elementId,'transparent'), timeBetweenSteps*(i-1) );
                    else
                        nlbFade_s[i - 1] = setTimeout( setColor.bind(null,elementId,RGBToHex( changes )), timeBetweenSteps*(i-1) );
                    // nlbFade_s[i - 1] = setTimeout( elementId+'.style.backgroundColor = "'+RGBToHex( changes )+'";', timeBetweenSteps*(i-1) );
                }
                nlbFade_t[nlbFade_elemTableId] = nlbFade_s;
            }
            function hexToRGB( hexVal ){
                hexVal = hexVal.toUpperCase( );
                if( hexVal.substring( 0, 1 ) == '#' )
                    hexVal = hexVal.substring( 1 );
                var hexArray = new Array( );
                var rgbArray = new Array( );
                hexArray[0] = hexVal.substring( 0, 2 );
                hexArray[1] = hexVal.substring( 2, 4 );
                hexArray[2] = hexVal.substring( 4, 6 );
                for( var k = 0; k < hexArray.length; k++ )
                {
                    var num = hexArray[k];
                    var res = 0;
                    var j = 0;
                    for( var i = num.length - 1; i >= 0; i-- )
                        res += parseInt( nlbFade_hextable.indexOf( num.charAt( i ) ) ) * Math.pow( 16, j++ );
                    rgbArray[k] = res;
                }
                return rgbArray;
            }
            function RGBToHex( rgbArray ){
                var retval = new Array( );
                for( var j = 0; j < rgbArray.length; j++ )
                {
                    var result = new Array( );
                    var val = rgbArray[j];
                    var i = 0;
                    while( val > 16 )
                    {
                        result[i++] = val%16;
                        val = Math.floor( val/16 );
                    }
                    result[i++] = val%16;
                    var out = '';
                    for( var k = result.length - 1; k >= 0; k-- )
                        out += nlbFade_hextable[result[k]];
                    retval[j] = padLeft( out, '0', 2 );
                }
                out = '#';
                for( var i = 0; i < retval.length; i++ )
                    out += retval[i];
                return out;
            }
            if (!Array.prototype.indexOf) {
                Array.prototype.indexOf = function( val, fromIndex ) {
                    if( typeof( fromIndex ) != 'number' ) fromIndex = 0;
                    for( var index = fromIndex, len = this.length; index < len; index++ )
                        if( this[index] == val ) return index;
                    return -1;
                }
            }
            function padLeft( string, character, paddedWidth ){
                if( string.length >= paddedWidth )
                    return string;
                else
                {
                    while( string.length < paddedWidth )
                        string = character + string;
                }
                return string;
            }
            NLBfadeBg(el,startBgColor, endBgColor, fadeTime);

        },
        addEvent = function(obj, evType, fn) {
            if (obj.addEventListener) {
                obj.addEventListener(evType, fn, false);
                return true;
            } else if (obj.attachEvent) {
                var r = obj.attachEvent("on" + evType, fn);
                return r;
            } else {
                alert("Handler could not be attached");
            }
        };


//remove in production versioN!!
    document.querySelectorAll('.extbase-debugger').forEach(function(el){
        el.ondblclick = function(){removeDbtrees()};
    });
    function removeDbtrees (){
        document.querySelectorAll('.extbase-debugger').forEach(function(el){
            el.remove();
        });
    }

    fRecurrenceChange = function(changeColor){
        var val = parseInt(fRecurrence[0].value);
        switch (true) {
            case (val === 0):
                hideFields(fRecurrenceGroup);
                hideTab(fUdType);
                break;
            case (val === 7):
                showTab(fUdType,changeColor);
                showFields(fUdType,changeColor);
                showFields(fRecurrenceType,changeColor);
                fRecurrenceType[0].onchange();
                break;
            case (val > 0 && val < 7):
                hideTab(fUdType);
                showFields(fRecurrenceType,changeColor);
                fRecurrenceType[0].onchange();
        }
    };
    fRecurrenceTypeChange = function(changeColor){
        switch (fRecurrenceType[0].value) {
            case "0":
                hideFields(fRecurrenceCount);
                hideFields(fRecurrenceUntil);
                break;
            case "1":
                hideFields(fRecurrenceCount);
                showFields(fRecurrenceUntil,changeColor);
                break;
            case "2":
                showFields(fRecurrenceCount,changeColor);
                hideFields(fRecurrenceUntil);
                break;
            default:

        }
    };
    fUdTypeChange = function(changeColor){
        switch (fUdType[0].value) {
            case "0":
                hidePalette(paletteDaily);
                hidePalette(paletteWeekly);
                hidePalette(paletteMonthly);
                hidePalette(paletteYearly);
                break;
            case "1":
                showPalette(paletteDaily);
                hidePalette(paletteWeekly);
                hidePalette(paletteMonthly);
                hidePalette(paletteYearly);
                break;
            case "2":
                hidePalette(paletteDaily);
                showPalette(paletteWeekly);
                hidePalette(paletteMonthly);
                hidePalette(paletteYearly);
                break;
            case "3":
                hidePalette(paletteDaily);
                hidePalette(paletteWeekly);
                showPalette(paletteMonthly);
                hidePalette(paletteYearly);
                break;
            case "4":
                hidePalette(paletteDaily);
                hidePalette(paletteWeekly);
                hidePalette(paletteMonthly);
                showPalette(paletteYearly);
                break;
            default:

        }
    };
    fUdMonthlyBaseChange = function(changeColor){
        switch (fUdMonthlyBase[0].value) {
            case "0":
                hideFields(fUdMonthlyPerday);
                hideFields(fUdMonthlyPerdayWeekdays);
                hideFields(fUdMonthlyPerdateDay);
                hideFields(fUdMonthlyPerdateLastday);
                break;
            case "1":
                showFields(fUdMonthlyPerday);
                showFields(fUdMonthlyPerdayWeekdays);
                hideFields(fUdMonthlyPerdateDay);
                hideFields(fUdMonthlyPerdateLastday);
                break;
            case "2":
                hideFields(fUdMonthlyPerday);
                hideFields(fUdMonthlyPerdayWeekdays);
                showFields(fUdMonthlyPerdateDay);
                showFields(fUdMonthlyPerdateLastday);
                break;
            default:

        }
    };
    fUdYearlyPerdayCheckClick = function(changeColor){
        if(fUdYearlyPerdayCheck[ fUdYearlyPerdayCheck.length -1 ].checked === true ) {
            hideFields(fUdYearlyPerdayWeekdays);
        } else {
            showFields(fUdYearlyPerdayWeekdays);
        }
    };
    fRecurrenceUpdateBehaviorChange = function(){
        var disregardFields = fUdYearlyPerdayCheck = d.querySelectorAll('[data-formengine-input-name$="[disregard_changes_on_saving]"]');
        var disregardChanges = fRecurrenceUpdateBehavior[0].value > 3;
        for(var i = 0; i < disregardFields.length; i++){
            disregardFields[i].checked = disregardChanges;
            disregardFields[i].onclick();
        }
    };


    if(fEventType[0].value !== '') {
        fRecurrence[0].onchange = fRecurrenceChange;
        fRecurrenceType[0].onchange = fRecurrenceTypeChange;
        fUdType[0].onchange = fUdTypeChange;
        fUdMonthlyBase[0].onchange = fUdMonthlyBaseChange;
        fRecurrenceUpdateBehavior[0].onchange = fRecurrenceUpdateBehaviorChange;
        for(var i = 0; i < fUdYearlyPerdayCheck.length; i++){
        }
        addEvent(fUdYearlyPerdayCheck[ fUdYearlyPerdayCheck.length -1 ], "click", fUdYearlyPerdayCheckClick);


        //trigger on record open;
        fRecurrenceType[0].onchange(false);
        fRecurrence[0].onchange(false);
        fUdType[0].onchange(false);
        fUdType[0].onchange();
        fUdMonthlyBase[0].onchange();
        fUdMonthlyBase[0].onchange();
    }

};