# news_calendar
Extends the TYPO3 versatile news system extension tx_news with a calendar view


##REQUIREMENTS:

1. TYPO3 CMS 6.2
2. tx_news 3.2
3. jQuery and jQuery UI Javascript files (not shipped with this package)


##INSTALLATION

The extension needs to be installed as any other extension of TYPO3 CMS:

1. Switch to the module “Extension Manager”.
2. Get the extension
	1. Get it from the Extension Manager: Press the “Retrieve/Update” button and search for the extension key news_calendar and import the extension from the repository.

##PREPARATION 

INCLUDE STATIC TYPOSCRIPT
The extension ships some TypoScript code which needs to be included.

1. Switch to the root page of your site.
2. Switch to the Template module and select *Info/Modify*.
3. Press the link *Edit the whole template record* and switch to the tab Includes.
4. Select *newscalendar (news_calendar)* at the field Include static (from extensions):


##CONFIGURATION

1. Constants

The Extension adds some new constants to the extension news which are configurable in constants editor.

plugin.tx_news.settings.

constant | type | description
---------|------|--------------
twentyfourhour | boolean | default: 1; if checked (default) the time in calendar will be shown in 24 hours format (e.g. 23:00); If not the 23:00 is changing to 11:00pm.
tooltipPreStyle | option | here you can choose some preconfigured styles for the tooltip which is shwon if you hover an event in calendar.
uiTheme | option | here you can choose some preconfigured styles for the calendar (not the tooltips). These styles are jQuery Themes. For the option *custom* you need to set *uiThemeCustom* as well. Please see *uiThemeCustom* for more information.
uiThemeCustom | string | here you can write down the name of your own calendar theme, maybe an other jQuery theme. The extension looks for an folder with this name in *typo3conf/ext/news_calendar/Resources/Public/CSS/jqueryThemes/* and there for the files *jquery-ui.css* and *jquery-ui.theme.css* to load. 



2. Template and Fluid

The Extension adds a new option to the template Selector of the news frontend plugin with number 99 and simply called *calendar*. Soo just add the tx_news frontend plugin to the page you wish to see the calendar and configure it as usual. On tab *Template* you choose *calendar* as Template Layout. 

Add following code to Templates/News/List.html
    {namespace nc=FR\NewsCalendar\ViewHelpers}

    <f:if condition="{settings.templateLayout} == 99">  

        <f:for each="{news}" as="newsItem" iteration="iterator">

            <f:render partial="List/CalendarItem" arguments="{newsItem: newsItem,settings:settings,iterator:iterator}" />

        </f:for>

        <nc:javascript.calendar uiThemeCustom="{settings.uiThemeCustom}" uiTheme="{settings.uiTheme}" tooltipPreStyle="{settings.tooltipPreStyle}" twentyfourhour="{settings.twentyfourhour}"/>

    </f:if> 


Add a file named *CalendarItem.html* in folder *Partials/List* with following content:

    {namespace nc=FR\NewsCalendar\ViewHelpers}

    {namespace n=Tx_News_ViewHelpers}

    <f:if condition="{newsItem.showincalendar}">

        <nc:javascript.event url="{n:link(newsItem: newsItem, settings: settings, uriOnly: 1)}" uid="{newsItem.uid}" title="{newsItem.title}" start="{newsItem.eventstart}" end="{newsItem.eventend}" description="{newsItem.teaser -> f:format.crop(maxCharacters: '{settings.cropMaxCharacters}', respectWordBoundaries:'1') -> f:format.html()}" color="{newsItem.backgroundcolor}" textcolor="{newsItem.textcolor}" fulltime="{newsItem.fulltime}" />

    </f:if>

3. News Item

The Extension adds some fields to the news item. You can find them in the Tab called _event_.
field | type | description
---------|------|--------------



Clear system cache and enjoy!