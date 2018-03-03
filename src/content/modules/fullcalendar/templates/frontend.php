<?php
// load locale
$locale = getCurrentLanguage ();
$langFolder = ModuleHelper::buildRessourcePath ( FullCalendar::MODULE_NAME, "lib/locale" );
$langFile = $langFolder . "/{$locale}.js";
if (! file_exists ( $langFile )) {
	$locale = "en-us";
	$langFile = $langFolder . "/en-us.js";
}

?>
<div data-locale="<?php esc( $locale);?>" id="calendar"
	data-url="<?php echo ModuleHelper::buildMethodCallUrl(FullCalendar::class, "json");?>"></div>

<?php
enqueueScriptFile ( ModuleHelper::buildRessourcePath ( FullCalendar::MODULE_NAME, "lib/lib/moment.min.js" ) );
enqueueScriptFile ( ModuleHelper::buildRessourcePath ( FullCalendar::MODULE_NAME, "lib/fullcalendar.min.js" ) );
enqueueScriptFile ( $langFile );

enqueueScriptFile ( ModuleHelper::buildRessourcePath ( FullCalendar::MODULE_NAME, "js/frontend.js" ) );
combinedScriptHtml ();
?>