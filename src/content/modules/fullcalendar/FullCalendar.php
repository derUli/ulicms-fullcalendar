<?php
class FullCalendar extends MainClass {
	const MODULE_NAME = "fullcalendar";
	public function settings() {
		return Template::executeModuleTemplate ( FullCalendar::MODULE_NAME, "admin.php" );
	}
	public function getSettingsHeadline() {
		return get_translation ( "events" );
	}
	public function getSettingsLinkText() {
		return get_translation ( "edit_events" );
	}
	public function render() {
		return Template::executeModuleTemplate ( FullCalendar::MODULE_NAME, "frontend.php" );
	}
	public function adminHead() {
		enqueueStylesheet ( ModuleHelper::buildRessourcePath ( FullCalendar::MODULE_NAME, "lib/fullcalendar.min.css" ) );
		enqueueStylesheet ( ModuleHelper::buildRessourcePath ( FullCalendar::MODULE_NAME, "css/backend.css" ) );
		return getCombinedStylesheetHtml ();
	}
	public function head() {
		enqueueStylesheet ( ModuleHelper::buildRessourcePath ( FullCalendar::MODULE_NAME, "lib/fullcalendar.min.css" ) );
		return getCombinedStylesheetHtml ();
	}
	public function getJsonData($start, $end) {
		$events = array ();
		
		$adapter = CacheUtil::getAdapter ();
		
		if ($adapter and $adapter->get ( CacheUtil::getCurrentUid () )) {
			return $adapter->get ( CacheUtil::getCurrentUid () );
		}
		$sql = "SELECT * FROM `" . tbname ( "events" ) . "` WHERE `start` >= " . intval ( $start ) . " AND `end` <=" . intval ( $end ) . " ORDER BY id";
		$query = db_query ( $sql );
		while ( $row = db_fetch_object ( $query ) ) {
			$obj = array ();
			$obj ["id"] = $row->id;
			$obj ["start"] = date ( "Y-m-d", $row->start );
			$obj ["end"] = date ( "Y-m-d", $row->end );
			$obj ["title"] = $row->title;
			if (! empty ( $row->url ) and $row->url != "http://") {
				$obj ["url"] = $row->url;
			}
			array_push ( $events, $obj );
		}
		
		if ($adapter) {
			$adapter->set ( CacheUtil::getCurrentUid (), $json, CacheUtil::getCachePeriod () );
		}
		return $events;
	}
	public function json() {
		$start = isset ( $_REQUEST ["start"] ) ? strtotime ( $_REQUEST ["start"] ) : null;
		$end = isset ( $_REQUEST ["end"] ) ? strtotime ( $_REQUEST ["end"] ) : null;
		if (! $start) {
			$start = mktime ( 0, 0, 0, date ( "n" ), 1, date ( "y" ) );
		}
		if (! $end) {
			$end = mktime ( 0, 0, 0, date ( "n" ) + 1, 0, date ( "y" ) );
		}
		
		JSONResult ( $this->getJsonData ( $start, $end ) );
	}
	public function addEvent() {
		$event = new Event ();
		if (StringHelper::isNullOrWhitespace ( Request::getVar ( "title" ) )) {
			return HTTPStatusCodeResult ( HttpStatusCode::BAD_REQUEST );
		}
		$event->setTitle ( Request::getVar ( "title" ) );
		$event->setStart ( strtotime ( Request::getVar ( "start" ) ) );
		$event->setEnd ( strtotime ( Request::getVar ( "end" ) ) );
		$event->setUrl ( (Request::getVar ( "url" ) && ! empty ( Request::getVar ( "url" ) )) ? Request::getVar ( "url" ) : null );
		$event->save ();
		
		return HTTPStatusCodeResult ( HttpStatusCode::OK );
	}
	public function changeEventTimespan() {
		$id = Request::getVar ( "id" );
		if ($id <= 0) {
			return HTTPStatusCodeResult ( HttpStatusCode::BAD_REQUEST );
		}
		$event = new Event ( $id );
		if (! $event->getID ()) {
			return HTTPStatusCodeResult ( HttpStatusCode::NOT_FOUND );
		}
		$event->setStart ( strtotime ( Request::getVar ( "start" ) ) );
		$event->setEnd ( strtotime ( Request::getVar ( "end" ) ) );
		$event->save ();
		return HTTPStatusCodeResult ( HttpStatusCode::OK );
	}
	public function renameEvent() {
		$id = Request::getVar ( "id" );
		if ($id <= 0) {
			return HTTPStatusCodeResult ( HttpStatusCode::BAD_REQUEST );
		}
		$event = new Event ( $id );
		if (! $event->getID ()) {
			return HTTPStatusCodeResult ( HttpStatusCode::NOT_FOUND );
		}
		$event->setTitle ( Request::getVar ( "title" ) );
		$event->setUrl ( (Request::getVar ( "url" ) && ! empty ( Request::getVar ( "url" ) )) ? Request::getVar ( "url" ) : null );
		
		$event->save ();
		return HTTPStatusCodeResult ( HttpStatusCode::OK );
	}
	public function deleteEvent() {
		$id = Request::getVar ( "id" );
		if ($id <= 0) {
			return HTTPStatusCodeResult ( HttpStatusCode::BAD_REQUEST );
		}
		$event = new Event ( $id );
		if (! $event->getID ()) {
			return HTTPStatusCodeResult ( HttpStatusCode::NOT_FOUND );
		}
		$event->delete ();
		if ($event->getID ()) {
			return HTTPStatusCodeResult ( HttpStatusCode::EXPECTATION_FAILED );
		}
		return HTTPStatusCodeResult ( HttpStatusCode::OK );
	}
	public function uninstall() {
		$migrator = new DBMigrator ( "package/fullcalendar", ModuleHelper::buildModuleRessourcePath ( FullCalendar::MODULE_NAME, "sql/down" ) );
		$migrator->rollback ();
	}
}