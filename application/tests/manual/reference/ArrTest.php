<?php defined('SYSPATH') or die('No direct access allowed.');

class Reference_ArrTest extends Kohana_UnitTest_TestCase
{
	public function testIsAssoc()
	{
		$post = array(
		    'username'  => 'john.doe',
		    'password'  => 'secret'
		);

		// Returns true
		$this->assertTrue(Arr::is_assoc($post));

		$list_str = array('foo', 'bar');
		$list_num = array(1, 6, 12);

		// Returns false
		$this->assertFalse(Arr::is_assoc($list_str));
		$this->assertFalse(Arr::is_assoc($list_num));

	}

	public function testPath()
	{
		$post = array(
		    'user'      => array(
        		'id'        => 'john.doe',
		        'name'      => 'John Doe II'
		    ),
		    'via'       => array(
		        'id'        => 'mobile',
        		'name'      => 'Mobile API'
		    )
		);

		// Returns "john.doe"
		$user_id = Arr::path($post, 'user.id');
		$this->assertEquals($user_id, $post['user']['id']);

		// Returns "Mobile API"
		$posted_from = Arr::path($post, 'via.name');
		$this->assertEquals($posted_from, $post['via']['name']);
	}

	public function testPathWildCard()
	{
		$post = array(
		    'user'      => array(
		        'id'        => 'john.doe',
		        'name'      => 'John Doe II'
		    ),
		    'via'       => array(
		        'id'        => 'mobile',
		        'name'      => 'Mobile API'
		    ),
		    'thumbs'    => array(
		        0           => array(
		            'width'     => 100,
		            'height'    => 50,
		            'url'       => 'http://media.wx.yz/c4d.jpg'
		        ),
		        1           => array(
		            'width'     => 110,
		            'height'    => 50,
		            'url'       => 'http://media.wx.yz/20b.jpg'
		        )
		    )
		);

		// Returns all thumbnail URLs as an array
		$thumbnails = Arr::path($post, 'thumbs.*.url');

		$this->assertEquals(
			$thumbnails,
			array($post['thumbs'][0]['url'], $post['thumbs'][1]['url'])
		);
	}

	public function testUnshift()
	{
		$array = array(
			'name' 	=> 'John Doe',
			'email'	=> 'john@example.com'
		);

		$copy = $array;

		Arr::unshift($array, 'id', 'john.doe');

		$this->assertNotEquals($array, $copy);

		// Test the first element
		$first = reset($array);

		$this->assertEquals($first, $array['id']);
	}

	public function testMapTrim()
	{
		$data = array(
			'post'	=> array(
				'from'	=> '   Mobile API   ',
				'via'	=> '  Some Provider  '
			),
			'date_posted' => '   2010-08-24  '
		);

		$copy = $data;
		$this->assertEquals($copy, $data);

		$data = Arr::map('trim', $data);

		$this->assertNotEquals($copy, $data);
	}

	public function testMapCustomClass()
	{
		$data = array(
			'total'		=> '100',
			'details'	=> array(
				'food'		=> '50',
				'drinks'	=> '',
				'tip'		=> 50,
				'extra'		=> null
			)
		);

		$copy = $data;
		$this->assertEquals($data, $copy);

		$testCallback = new ArrTest_Callback;

		$data = Arr::map(array($testCallback, 'to_int'), $data);

		$this->assertNotEquals($data, $copy);
	}

	public function testMerge()
	{
	
		$john = array('name' => 'john', 'children' => array('fred', 'paul', 'sally', 'jane'));
		$mary = array('name' => 'mary', 'children' => array('jane'));
	 
		// John and Mary are married, merge them together
		$john = Arr::merge($john, $mary);
	 
		// The output of $john will now be:
		// array('name' => 'mary', 'children' => array('fred', 'paul', 'sally', 'jane'))

		$children_count = count($john['children']);

		$this->assertEquals($children_count, 4);
	}

	public function testOverwrite()
	{
		
	}
}

class ArrTest_Callback
{
	public function to_int($value)
	{
		return (int) $value;
	}
}
