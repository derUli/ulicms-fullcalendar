<?php
// load locale
$locale = getSystemLanguage ();
$langFolder = ModuleHelper::buildRessourcePath ( FullCalendar::MODULE_NAME, "lib/locale" );
$langFile = $langFolder . "/{$locale}.js";
if (! file_exists ( $langFile )) {
	$locale = "en-us";
	$langFile = $langFolder . "/en-us.js";
}

?>
<?php csrf_token_html();?>
<div class="text-right">
	<button type="button" id="delete-event"
		class="fc-button fc-button-default fc-state-default">
		<span><?php translate("recycle_bin");?>
</span>
	</button>
</div>
<div data-locale="<?php esc($locale);?>" id="calendar" class="voffset2"
	data-url="<?php echo ModuleHelper::buildMethodCallUrl(FullCalendar::class, "json");?>"
	data-add-url="<?php echo ModuleHelper::buildMethodCallUrl(FullCalendar::class, "addEvent");?>"
	data-change-event-timespan-url="<?php echo ModuleHelper::buildMethodCallUrl(FullCalendar::class, "changeEventTimespan");?>"
	data-delete-event-url="<?php echo ModuleHelper::buildMethodCallUrl(FullCalendar::class, "deleteEvent");?>"
	data-rename-url="<?php echo ModuleHelper::buildMethodCallUrl(FullCalendar::class, "renameEvent");?>"></div>


<?php
$translation = new JSTranslation ();
$translation->addKey ( "event_title" );
$translation->addKey ( "event_url" );
$translation->addKey ( "ask_for_delete" );
$translation->renderJS ();

enqueueScriptFile ( ModuleHelper::buildRessourcePath ( FullCalendar::MODULE_NAME, "lib/lib/moment.min.js" ) );
enqueueScriptFile ( ModuleHelper::buildRessourcePath ( FullCalendar::MODULE_NAME, "lib/fullcalendar.min.js" ) );
enqueueScriptFile ( $langFile );

enqueueScriptFile ( ModuleHelper::buildRessourcePath ( FullCalendar::MODULE_NAME, "js/backend.js" ) );
combinedScriptHtml ();
