<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Index extends Controller_Site
{
	/**
	 * Front page
	 */
	public function action_index()
	{
		$this->template->title = 'Welcome';
		$this->template->description = 'Documentation project for Kohana v3 PHP Framework';
		$this->template->keywords = 'kohana, documentation, tutorial';
		
		$this->view = View::factory('index/index');
	}
}
