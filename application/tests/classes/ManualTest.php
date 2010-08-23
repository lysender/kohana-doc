<?php defined('SYSPATH') or die('No direct access allowed.');

class ManualTest extends Kohana_UnitTest_TestCase
{
	public function testObject()
	{
		$manual = new Manual('index', 'en');

		$this->assertType('Manual', $manual);
	}
}
