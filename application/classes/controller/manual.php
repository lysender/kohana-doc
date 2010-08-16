<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Manual extends Controller_Site
{
	/** 
	 * Language
	 *
	 * @var string
	 */
	public $language;

	/** 
	 * Requested doc page
	 *
	 * @var string
	 */
	public $page;

	/** 
	 * Meta data file for the requested doc page
	 *
	 * @var array
	 */
	public $page_meta;

	/** 
	 * before()
	 */
	public function before()
	{
		$config = Kohana::config('manual');

		// Pre determine the requested page and set it up
		$this->language = $this->request->param('language', $config->default_language);
		$this->page = $this->request->param('page', $config->default_page);

		// Set the translation language
		I18n::$lang = $this->language;

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
		$this->template->title = 'Kohana Documentation';

		$file = $this->file($this->language, $this->page);
		$this->page_meta = $this->file_meta($this->language, $this->page);

		if ( ! $file)
		{
			throw new Kohana_Request_Exception('Requested documentation page is not found');
		}

		$this->view = Markdown(file_get_contents($file));

		// Build the sidebar
		$sidebar_info = array();
		$sidebar_info['doctree_info'] = $this->_doctree_info($this->language, $this->page);
		$sidebar_info['doctree_siblings'] = $this->_doctree_siblings($this->language, $this->page);

		$this->template->sidebar = View::factory('manual/sidebar')
			->bind('sidebar_info', $sidebar_info);

		// Build the basic nav
		$basic_nav = array();

		$this->template->basic_nav = View::factory('manual/nav')
			->bind('basic_nav', $basic_nav);

		// Build the child topics
		$topics = (!empty($this->page_meta['children'])) ? $this->page_meta['children'] : array();

		$this->template->topics = View::factory('manual/topics')
			->bind('topics', $topics);
	}

	/** 
	 * Returns the full path to the doc file
	 * 
	 * @param string $language
	 * @param string $page
	 * @return string
	 */
	public function file($language, $page)
	{
		$path = $language.DIRECTORY_SEPARATOR.$this->_extract_path($page);

		return Kohana::find_file('manual', $path, 'md');
	}

	/** 
	 * Returns the file meta as an array
	 *
	 * @param string $language
	 * @param string $page
	 * @return array
	 */
	public function file_meta($language, $page)
	{
		$path = $language.DIRECTORY_SEPARATOR.$this->_extract_path($page);

		// Find the file
		$file = Kohana::find_file('manual', $path, 'php');

		if ($file)
		{
			return include $file;
		}

		return false;
	}	

	/** 
	 * Returns the navigation info of the current page
	 *
	 * @param string $language
	 * @param string $page
	 * @return array
	 */
	public function basic_nav($language, $page)
	{
		
	}

	/** 
	 * Returns the sidebar navigation info of the current page
	 *
	 * @param string $language
	 * @param string $page
	 * @return array
	 */
	public function sidebar_nav($language, $page)
	{
		$sidebar_info = array();
		$sidebar_info['doctree_info'] = $this->_doctree_info($language, $page);
		$sidebar_info['doctree_siblings'] = $this->_doctree_siblings($language, $page);

		return $sidebar_info;
	}

	/** 
	 * Returns the table of contents for the current page
	 * Only returns the direct child topics / articles
	 *
	 * @param string $language
	 * @param string $page
	 * @return array
	 */
	public function toc($language, $page)
	{
		return $this->page_meta['children'];
	}

	/** 
	 * Returns the current page related pages
	 * Related pages are retrived via parents child nodes
	 *
	 * @param string $language
	 * @param string $page
	 * @return array
	 */
	protected function _doctree_siblings($language, $page)
	{
		if ( ! $this->page_meta['parent'])
		{
			// Cases when there is no parent, ex: root
			return false;
		}

		// Load parent meta
		$current_node = $this->page_meta['self'];
		$parent_node = str_replace(".$current_node", '', $page);

		if ($parent_node == $page)
		{
			return false;
		}
		
		$parent_meta = $this->file_meta($language, $parent_node);
		if (empty($parent_meta))
		{
			throw new Exception("No parent article information is found for $page");
		}

		$siblings = array();

		// Mark the current page / article
		foreach ($parent_meta['children'] as $key => $val)
		{
			$siblings[$key] = array(
				'link'	=> "/manual/$language/$parent_node/$key",
				'title'	=> $parent_meta['children'][$key],
				'currently_viewed' => ($key == $current_node) ? true : false
			);
		}

		return $siblings;
	}

	/** 
	 * Returns the current page sub topics / articles
	 *
	 * @return array
	 */
	protected function _doctree_children()
	{
		return $this->page_meta['children'];
	}

	/** 
	 * Returns an array of nodes starting from the current page
	 * up to the root node to represent the heirarchy of pages
	 * from which the article is traced from root to this page
	 *
	 * @param string $language
	 * @param string $page
	 * @return array
	 */
	protected function _doctree_info($language, $page)
	{
		// Traverse up to the root
		$paths = explode('.', $page);
		$doctree = array();

		// Add the documentation root / index
		$root_meta = $this->file_meta($language, 'index');
		if (empty($root_meta))
		{
			throw new Exception('Unable to load manual index meta data');
		}

		$doctree[] = array(
			'link'	=> "/manual/$language",
			'title' => $root_meta['title']
		);

		// Compile the path tree, starting from root down
		// to the current page
		$concat_path = '';
		foreach ($paths as $path)
		{
			if ($concat_path)
			{
				$concat_path .= '.';
			}

			$concat_path .= $path;
			$meta_file = $this->file_meta($language, $concat_path);

			if (empty($meta_file))
			{
				return false;
			}

			$doctree[] = array(
				'link'		=> "/manual/$language/$concat_path",
				'title'		=> $meta_file['title']
			);
		}

		return $doctree;
	}

	/** 
	 * Returns the relative path for the requested doc
	 * page. Each parts are alpha numeric and must be up to
	 * 4 levels deep only
	 *
	 * @param string $page
	 * @return string
	 */
	protected function _extract_path($page)
	{
		$paths = explode('.', $page);
		if (empty($paths))
		{
			return false;
		}

		// Assert that all parts are alpha numeric and dashes only
		// If an invalid part is found, return false immediately
		foreach ($paths as $node)
		{
			if ( ! Validate::alpha_dash($node))
			{
				return false;
			}
		}

		// Assert that path is only 4 levels deep maximum
		if (count($paths) > 4)
		{
			return false;
		};

		// compile the path
		$path = implode(DIRECTORY_SEPARATOR, $paths);
		return $path;
	}
}
