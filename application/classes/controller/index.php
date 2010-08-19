<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Index extends Controller_Site
{
	/**
	 * Front page
	 */
	public function action_index()
	{
		$config = Kohana::config('manual');
		$this->request->redirect("/manual/$config->default_language");
	}
}
