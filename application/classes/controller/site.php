<?php defined('SYSPATH') or die('No direct script access.');

abstract class Controller_Site extends Controller_Template
{
	/**
	 * @var string
	 */
	public $template = 'site/template';
	
	/**
	 * @var string
	 */
	public $header = 'site/header';
	
	/**
	 * @var Kohana_View
	 */
	public $view;
	
	/**
	 * @var string
	 */
	public $footer = 'site/footer';
	
	/**
	 * @var Auth
	 */
	public $auth;
	
	/** 
	 * before()
	 *
	 * Called before action is called
	 */
	public function before()
	{
		// make sure template is initialized first
		parent::before();
		
		// initialize current page URL
		// we are not using query string so uri is only used
		$this->current_page = $this->request->uri();

		if ($this->auto_render)
		{
			$this->template->styles = array(
				'/media/css/screen.css'	=> 'screen, projection',
				'/media/css/print.css'	=> 'print',
				'/media/css/style.css'	=> 'screen, projection'
			);

			$this->template->scripts = array(
				'/media/js/jquery-1.4.2.min.js'
			);
		}
	}

	/**
	 * after()
	 * 
	 * @see system/classes/kohana/controller/Kohana_Controller_Template#after()
	 */
	public function after()
	{
		if ($this->auto_render)
		{			
			// template disyplay logic
			$this->template->header = View::factory($this->header);
			$this->template->content = $this->view;
			
			$this->template->footer = View::factory($this->footer);			
		}

		return parent::after();
	}
}