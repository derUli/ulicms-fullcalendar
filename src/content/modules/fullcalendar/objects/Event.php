<?php
class Event extends Model {
	private $title;
	private $start;
	private $end;
	private $url;
	public function loadByID($id) {
		$query = Database::pQuery ( "select * from `{prefix}events` where id = ?", array (
				intval ( $id ) 
		), true );
		if (Database::getNumRows ( $query ) > 0) {
			$this->fillVars ( $query );
		} else {
			$this->fillVars ( null );
		}
	}
	public static function getAll() {
		$items = array ();
		$query = Database::pQuery ( "select id from `{prefix}events` where id = ?", array (
				intval ( $id ) 
		), true );
		while ( $row = Database::fetchObject ( $query ) ) {
			$items [] = new Model ( $row->id );
		}
		return $items;
	}
	
	protected function fillVars($query = null) {
		if ($query) {
			$result = Database::fetchObject ( $query );
			$this->setID ( $result->id );
			$this->setTitle ( $result->title );
			$this->setStart ( $result->start );
			$this->setEnd ( $result->end );
			$this->setUrl ( $result->url );
		} else {
			$this->setID ( null );
			$this->setTitle ( null );
			$this->setStart ( null );
			$this->setEnd ( null );
			$this->setUrl ( null );
		}
	}
	public function getTitle() {
		return $this->title;
	}
	public function getStart() {
		return $this->start;
	}
	public function getEnd() {
		return $this->end;
	}
	public function getUrl() {
		return $this->url;
	}
	public function setTitle($val) {
		$this->title = ! is_null ( $val ) ? strval ( $val ) : null;
	}
	public function setStart($val) {
		$this->start = ! is_null ( $val ) ? intval ( $val ) : null;
	}
	public function setEnd($val) {
		$this->end = ! is_null ( $val ) ? intval ( $val ) : null;
	}
	public function setUrl($val) {
		$this->url = ! is_null ( $val ) ? strval ( $val ) : null;
	}
	protected function insert() {
		Database::pQuery ( "insert into `{prefix}events` (title, start, end, url) values(?,?,?,?)", array (
				$this->getTitle (),
				$this->getStart (),
				$this->getEnd (),
				$this->getUrl () 
		), true );
		$this->setID ( Database::getLastInsertID () );
	}
	protected function update() {
		Database::pQuery ( "update `{prefix}events` 
				set title = ?, 
					start = ?,
					end = ?,
					url = ?
					where id = ?
				", array (
				$this->getTitle (),
				$this->getStart (),
				$this->getEnd (),
				$this->getUrl (),
				$this->getID () 
		), true );
	}
	public function delete() {
		if (! is_null ( $this->getID () )) {
			Database::pQuery ( "delete from `{prefix}events` where id = ?", array (
					$this->getID () 
			), true );
			$this->setID ( null );
		}
	}
	
}