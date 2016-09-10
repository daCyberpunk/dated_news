# dated_news
Extends the TYPO3 versatile news system extension tx_news with a calendar view


##REQUIREMENTS:

1. TYPO3 CMS 7.6 or higher
2. tx_news 4.0.0 or higher
3. vhs 3.0.0 or higher


##INSTALLATION

The extension needs to be installed as any other extension of TYPO3 CMS:

1. Switch to the module “Extension Manager”.
2. Get the extension
	1. Get it from the Extension Manager: Press the “Retrieve/Update” button and search for the extension key dated_news and import the extension from the repository.

##PREPARATION 

###INCLUDE STATIC TYPOSCRIPT

The extension ships some TypoScript code which needs to be included.

1. Switch to the root page of your site.
2. Switch to the Template module and select *Info/Modify*.
3. Press the link *Edit the whole template record* and switch to the tab Includes.
4. Select *datednews (dated_news)* at the field Include static (from extensions):


###CONFIGURATION

#####Constants

The Extension adds some new constants to the extension news which are configurable in constants editor.

plugin.tx_news.settings.

constant | type | description
---------|------|--------------
twentyfourhour | boolean | default: 1; if checked (default) the time in calendar will be shown in 24 hours format (e.g. 23:00); If not the 23:00 is changing to 11:00pm.
tooltipPreStyle | option | here you can choose some preconfigured styles for the tooltip which is shwon if you hover an event in calendar.
uiTheme | option | here you can choose some preconfigured styles for the calendar (not the tooltips). These styles are jQuery Themes. For the option *custom* you need to set *uiThemeCustom* as well. Please see *uiThemeCustom* for more information.
uiThemeCustom | string | here you can write down the name of your own calendar theme, maybe an other jQuery theme. The extension looks for an folder with this name in *typo3conf/ext/news_calendar/Resources/Public/CSS/jqueryThemes/* and there for the files *jquery-ui.css* and *jquery-ui.theme.css* to load. 



####Frontend Plugin

The Extension adds a new action to the news frontend plugin called "DatedNews Calendar". choose it. 


#####Tag based Filtering

The Extension adds the possibility to filter events based on their tags you can add to the news items.
You just have to build a list of tag-items where every item has the class *dated-news-filter* and a data attribute *data-dn-filter* with the tag title in it. Luckily the news extension comes allready with a tag-template. The code you add there could look like the following:

    <f:if condition="{tags}">

        <div class="button-group filters-button-group">
    
            <f:for each="{tags}" as="tag">
    
                <button class="dated-news-filter button" data-dn-filter="{tag.title}">{tag.title}</button>
    
            </f:for>
    
        </div>
    
    </f:if>

An Example is already included. You just can choose the Template layout "taglist for filtering Calendarview" in the Template Tab of the frontend Plugin. 

###Add events

The Extension adds some fields to the news item. You can find them in the Tab called _event_.

field | type | description
---------|------|--------------
Show in Calendar | check | Show Event in calendar or even not
Event Start / End | DateTime | exacttime is important if its not an all day event. Otherwise the date is enough
Full Time Event | check |  if checked the devent is handled as a full day event and no time is displayed
Event Location | text | where the event takes place
Textcolor / backgroundcolor | text | if the event shall be displayed with an other color as the standard one. Just insert words e.g. _yellow_. You can also insert hex or RGB notation



Clear system cache and enjoy!
Please contact me if something is still wrong. I'll try to fix it asap!