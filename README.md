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
><f:if condition="{settings.templateLayout} == 99">  
    <f:for each="{news}" as="newsItem" iteration="iterator">
        <f:render partial="List/CalendarItem" arguments="{newsItem: newsItem,settings:settings,iterator:iterator}" />
    </f:for>
    <nc:javascript.calendar uiThemeCustom="{settings.uiThemeCustom}" uiTheme="{settings.uiTheme}" tooltipPreStyle="{settings.tooltipPreStyle}" twentyfourhour="{settings.twentyfourhour}"/>
</f:if> 


Add a file named *CalendarItem.html* in folder *Partials/List* with following content:
>{namespace nc=FR\NewsCalendar\ViewHelpers}
>{namespace n=Tx_News_ViewHelpers}
>
><f:if condition="{newsItem.showincalendar}">
>
><nc:javascript.event url="{n:link(newsItem: newsItem, settings: settings, uriOnly: 1)}" uid="{newsItem.uid}" title="{newsItem.title}" start="{newsItem.eventstart}" end="{newsItem.eventend}" description="{newsItem.teaser -> f:format.crop(maxCharacters: '{settings.cropMaxCharacters}', respectWordBoundaries:'1') -> f:format.html()}" color="{newsItem.backgroundcolor}" textcolor="{newsItem.textcolor}" fulltime="{newsItem.fulltime}" />
>
></f:if>

Clear system cache and enjoy!



>page.20.2.NO.stdWrap.cObject.10 < page.10

>page.10 >

4. Add following line to your content markers/variables in your TypoScript Setup: 

>select.pidInList.field = uid

	it may now look like:

	CONTENT = CONTENT

    CONTENT{

        table=tt_content

        select.orderBy=sorting

        select.where=colPos=1

        select.languageField = sys_language_uid

        select.pidInList.field = uid 

    }

5. Make sure, your page structure looks as follows:
    
    - root page (NOT a shortcut, just standard page)
        
        - Main Menu Separator
        
            - page 1 (Section 1)
        
                - subpage 1 (slide 1)
        
                - subpage 2 (slide 2)
        
                - subpage 3 (slide 3)
        
            - page 2 (Section 2)
        
                - subpage 1 (slide 1)
        
            - page 3 (Section 3)
        
                - subpage 1 (slide 1)
        
                - subpage 2 (slide 2)
        
            and so on.




##CONTENT

1. Sections

Your content marker or fluid variables you still define in your own page template as u are used to it.

Content has to be putted on the slides, not on the sections. Every section needs minimum one slide to appear. So have no empty sections 

2. Header / Footer

To get a header or footer with for example 150px height, you have to set the constant 

>plugin.tx_fronepagebasic.settings.paddingTop = 150px 

for the header, or

>plugin.tx_fronepagebasic.settings.paddingBottom = 150px 

for getting a footer.

Now you can put content to the header.html respective footer.html in the templateRootPath. 

Its a fluid template. All variables will be defined in your typoscript via:

> page.40.10.20.variables

for the header and

> page.40.30.20.variables

for the footer. 

All constants of the extensions are available in Fluid with {settings}


3. Additional overlay

for whom who need:

Set the overlay constant to true.

>plugin.tx_fronepagebasic.settings.overlay = true

Now you can put content to the overlay.html in the templateRootPath. 

Its a fluid template. All variables will be defined in your typoscript via:

> page.40.20.20.variables


4. Content in header / Footer / Overlay

Its highly recommended to create a page with appropriate backendlayout in backend for these contents

in defining your content variable you add *select.pidInList* Example:

We created a page with id = 29 in backend and a backendlayout where the colpos for the header is 998

>page.40.10.20.variables{
>  content = CONTENT

>  content{

>    table=tt_content

>    select.orderBy=sorting

>    select.where=colPos=998

>    select.languageField = sys_language_uid

>    select.pidInList = 29

>  }

>}

now you can access it as usual in Fluid Templating.

5. Different non-fixed headers for each section

On each page in the onepage tab you can add a value for the header with its meassures in px, em or %.
If you're using this kind of header, its recommended to use the fixed header (see point 4 of this manual).

you can now add content to this header by using the TypoScript path:

<   page.20.1.IFSUB.stdWrap.cObject.10.15

This can be a content object or anything else you wish. 
f.e. you can add the CONTENT object and add a backend Layout to the section page where you add your header contents. :) 


##Multilanguage:
There shouldn't be a problem with. The only need is, the root page needs to be translated in all available languages.
This extension is tested with a standard realURL-configuration. 


##Constants
Most of them come directly with the same name from the fullpage.js plugin and you can find really could explanation here: [Visit fullpage.js on Github](https://github.com/alvarotrigo/fullPage.js)