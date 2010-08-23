<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Manual extends Controller_Site
{
	/** 
	 * before()
	 */
	public function before()
	{
		if (defined('MARKDOWN_PARSER_CLASS'))
		{
			throw new Kohana_Exception('Markdown parser already registered. Live documentation will not work in your environment.');
		}

		// Use customized Markdown parser
		define('MARKDOWN_PARSER_CLASS', 'Kodoc_Markdown');

		if ( ! class_exists('Markdown', FALSE))
		{
			// Load Markdown support
			require Kohana::find_file('vendor', 'markdown/markdown');
		}

		parent::before();
	}

	/** 
	 * Online manual action
	 */
	public function action_docs()
	{
		$config = Kohana::config('manual');

		// Pre determine the requested page and set it up
		$language = $this->request->param('language');
		if ( ! $language)
		{
			$this->request->redirect("/manual/$config->default_language");
		}

		$page = $this->request->param('page', $config->default_page);
		unset($config);

		// Set language globally to all views
		$this->template->bind_global('language', $language);

		// Set the translation language
		I18n::$lang = $language;

		// Load the manual page
		$manual = new Manual($page, $language);
		
		// Load title and content
		$this->template->title = $manual->page_meta['title'];
		$this->view = View::factory('manual/doc')
			->bind('content', $content)
			->bind('sidebar', $sidebar)
			->bind('nav', $nav)
			->bind('topics', $topics);

		$content = Markdown(file_get_contents($manual->file));

		// Build the sidebar
		$sidebar_info = $manual->sidebar_nav();
		$sidebar = View::factory('manual/sidebar')
			->bind('sidebar_info', $sidebar_info);

		// Build the child topics
		$current_topics = $manual->current_topics();
		$topics = View::factory('manual/topics')
			->bind('topics', $current_topics);
		
		// Build the basic nav
		$basic_nav = $manual->basic_nav();
		$nav = View::factory('manual/nav')
			->bind('basic_nav', $basic_nav);
	}
}
