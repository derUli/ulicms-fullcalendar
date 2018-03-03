<?php
class EventTest extends PHPUnit_Framework_TestCase {
	public function testGetAll() {
		$this->assertTrue ( is_array ( Event::getAll () ) );
	}
	public function testCreateEditAndDelete() {
		$event = new Event ();
		$this->assertNull ( $event->getID () );
		$event->setTitle ( "My Birthday" );
		$event->setStart ( mktime ( 0, 0, 0, 7, 27, 2018 ) );
		$event->setEnd ( mktime ( 23, 59, 59, 7, 27, 2018 ) );
		$event->setUrl ( "http://www.happy-birthday.com" );
		$event->save ();
		
		$this->assertNotNull ( $event->getID () );
		$id = $event->getID ();
		
		$event = new Event ( $id );
		$this->assertEquals ( "My Birthday", $event->getTitle () );
		$this->assertEquals ( mktime ( 0, 0, 0, 7, 27, 2018 ), $event->getStart () );
		$this->assertEquals ( mktime ( 23, 59, 59, 7, 27, 2018 ), $event->getEnd () );
		$this->assertEquals ( "http://www.happy-birthday.com", $event->getUrl () );
		
		$event = new Event ( $id );
		
		$this->assertNotNull ( $event->getID () );
		$event->setTitle ( "New Year" );
		$event->setStart ( mktime ( 0, 0, 0, 1, 1, 2018 ) );
		$event->setEnd ( mktime ( 23, 59, 59, 1, 1, 2018 ) );
		$event->setUrl ( "http://www.new-year.com" );
		$event->save ();
		
		$event = new Event ( $id );
		
		$this->assertEquals ( "New Year", $event->getTitle () );
		$this->assertEquals ( mktime ( 0, 0, 0, 1, 1, 2018 ), $event->getStart () );
		$this->assertEquals ( mktime ( 23, 59, 59, 1, 1, 2018 ), $event->getEnd () );
		$this->assertEquals ( "http://www.new-year.com", $event->getUrl () );
		
		$controller = ModuleHelper::getMainController ( FullCalendar::MODULE_NAME );
		$json = $controller->getJson ( strtotime ( "2018-01-01" ), strtotime ( "2018-12-31" ) );
		$jsonTest = json_decode ( $json, true );
		$arr = array_values ( array_slice ( $jsonTest, - 1 ) ) [0];
		$this->assertEquals ( "New Year", $arr ["title"] );
		
		$this->assertTrue ( json_last_error () === JSON_ERROR_NONE );
		
		$event->delete ();
		$this->assertNull ( $event->getID () );
		$event = new Event ( $id );
		
		$this->assertNull ( $event->getID () );
	}
}
	