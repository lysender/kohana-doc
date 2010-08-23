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
}
