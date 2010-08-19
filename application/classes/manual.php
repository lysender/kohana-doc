<?php defined('SYSPATH') or die('No direct script access.');

class Manual
{
	/** 
	 * Full path to the documentation file
	 *
	 * @var string
	 */
	public $file;

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
	 * The hierarchy from the top topic which is the manual
	 * index, down to the current topic's parent
	 *
	 * Index key is the link and value is the title
	 *
	 * @var array
	 */
	protected $_topic_hierarchy;

	/** 
	 * For any given topic / page /article, this is the group
	 * of topics which the current topic belongs
	 *
	 * @var array
	 */
	protected $_topic_group;

	/** 
	 * Child topics for the current topic
	 *
	 * @var array
	 */
	protected $_topics;

	/** 
	 * __construct()
	 *
	 * @param string $page
	 * @param string $language
	 * @return void
	 */
	public function __construct($page, $language)
	{
		$this->language = $language;
		$this->file = $this->_find_file($page);
		$this->page = $page;
		$this->page_meta = $this->_file_meta($page);

		if ( ! $this->file OR ! $this->page_meta)
		{
			throw new Kohana_Request_Exception('Requested documentation page is not found');
		}
	}

	/** 
	 * Returns the navigation info of the current page
	 *
	 * @return array
	 */
	public function basic_nav()
	{
		// Case #1: We are at the index / top page of the documentation
		if ( ! $this->page_meta['parent'])
		{
			// No navigation is needed
			return false;
		}

		$current_topic = $this->page_meta['self'];
		$first_topic = null;
		$last_topic = null;
		$first_child_topic = null;

		$topic_group_count = count($this->_topic_group);

		$this->topic_hierarchy($this->page);
		$this->topic_group($this->page);

		if (!empty($this->_topic_group))
		{
			// Get first topic group entry
			$keys = array_keys($this->_topic_group);
			$first_topic = reset($keys);

			// Get last topic group entry
			$last_topic = array_pop($keys);

			// Get the first child topic if it exists
			if ( ! empty($this->_topics))
			{
				$keys = array_keys($this->_topics);
				$first_child_topic = reset($keys);
			}
		}

		$nav = array(
			'prev'	=> null,
			'next'	=> null
		);

		if ( ! empty($this->_topics))
		{
			// Case #1: The current topic has child topics
			// Next = First child topic
			$nav['next'] = array(
				'link'	=> $first_child_topic,
				'title'	=> $this->_topics[$first_child_topic]
			);
		}
		elseif ($current_topic == $first_topic AND $topic_group_count > 1)
		{
			// Case #2: The current topic is the first topic of the group
			// and that there are more than 1 topic for the group
			// Next = next topic from the group

			// Remove the first topic node so that the final list will
			// have the next link as the first node
			$keys = array_keys($this->_topic_group);
			array_shift($keys);
			$node = reset($keys);
			$nav['next'] = array(
				'link'	=> $this->_topic_group[$node]['link'],
				'title'	=> $this->_topic_group[$node]['title']
			);
		}
		elseif ($topic_group_count == 1)
		{
			// Case #3: There is only 1 topic from the group
			// It is a design decision that a topic should not have
			// only 1 child topic, thus it must be more than one,
			// otherwise, it must be none

			throw new Exception('Design error: A topic should always contain more than 1 child topic');
		}
		elseif ($current_topic != $first_topic AND $current_topic != $last_topic)
		{
			// Case #3: We are in the middle of topics, not first, not last
			// Previous = previous topic of the group
			// Next = next topic of the group
			
			// We need to loop, there's no other way
			$tmp_prev = null;
			$tmp_next = null;
			$tmp = null;
			$found = false;
			
			foreach ($this->_topic_group as $key => $node)
			{
				if ($found)
				{
					$tmp_next = $key;
					break;
				}

				if ($key == $current_topic)
				{
					$tmp_prev = $tmp;
					$found = true;
				}

				$tmp = $key;
			}

			$nav['prev'] = array(
				'link'	=> $this->_topic_group[$tmp_prev]['link'],
				'title'	=> $this->_topic_group[$tmp_prev]['title']
			);
			
			$nav['next'] = array(
				'link'	=> $this->_topic_group[$tmp_next]['link'],
				'title'	=> $this->_topic_group[$tmp_next]['title']
			);
		}
		
		// If no previous link is given, it is assume as the parent topic
		if (empty($nav['prev']))
		{
			$nav['prev'] = array(
				'link'	=> $this->_topic_hierarchy[$this->page_meta['parent']]['link'],
				'title' => $this->_topic_hierarchy[$this->page_meta['parent']]['title']
			);
		}

		// Direct parent and parent of parent is included to the nav
		$tmp_tree = $this->_topic_hierarchy;
		$nav['parent'] = array_pop($tmp_tree);
		if (count($tmp_tree) >= 1)
		{
			$nav['grand_parent'] = array_pop($tmp_tree);
		}
		else
		{
			// Use the parent as the grand parent
			$nav['grand_parent'] = $nav['parent'];
		}

		return $nav;
	}

	/** 
	 * Returns the sidebar navigation info of the current page
	 *
	 * @return array
	 */
	public function sidebar_nav()
	{
		$sidebar_info = array();
		$sidebar_info['topic_hierarchy'] = $this->topic_hierarchy($this->page);
		$sidebar_info['topic_group'] = $this->topic_group($this->page);

		return $sidebar_info;
	}

	/** 
	 * Returns the child topics for the current topic / article
	 *
	 * @return array
	 */
	public function topics()
	{
		if ($this->_topics === null)
		{
			$this->_topics = $this->_topics();
		}

		return $this->_topics;
	}

	/** 
	 * Returns the topics for the current article
	 * Only returns the direct child topics / articles
	 *
	 * @return array
	 */
	protected function _topics()
	{
		$topics = array();

		// Get the current path
		$path = "/manual/$this->language/";

		// Only add the current page when it is not index
		// Then add a dot at the end to concatenate the children
		if ($this->page != 'index')
		{
			$path .= "$this->page.";
		}

		foreach ($this->page_meta['children'] as $page => $title)
		{
			$topics[$path.$page] = $title;
		}

		return $topics;
	}

	/** 
	 * Returns the current topic's group in which it belongs
	 *
	 * @param string $page
	 * @return array
	 */
	public function topic_group($page)
	{
		if ( ! $this->page_meta['parent'])
		{
			// Cases when there is no parent, ex: root
			return false;
		}

		if ($this->_topic_group === null)
		{
			$this->_topic_group = $this->_topic_group($page, $this->page_meta['self']);
		}

		return $this->_topic_group;
	}

	/** 
	 * Returns the current topic's related topics
	 * Related topics are retrived via parents child topics
	 *
	 * @param string $page
	 * @param string $current_node
	 * @return array
	 */
	protected function _topic_group($page, $current_node)
	{
		// Load parent meta
		$parent_meta = null;
		$parent_prefix = null;

		if ($this->page_meta['parent'] == 'index')
		{
			$parent_prefix = '';
			$parent_meta = $this->_file_meta('index');
		}
		else
		{
			$parent_node = str_replace(".$current_node", '', $page);
			$parent_meta = $this->_file_meta($parent_node);
			$parent_prefix = "$parent_node.";
		}

		if (empty($parent_meta))
		{
			throw new Exception("No topic group information is found for $page");
		}

		$topics = array();

		// Build the links
		foreach ($parent_meta['children'] as $key => $val)
		{
			// Also marks the currently viewed article
			$topics[$key] = array(
				'link'	=> "/manual/$this->language/$parent_prefix$key",
				'title'	=> $parent_meta['children'][$key],
				'currently_viewed' => ($key == $current_node) ? true : false
			);
		}

		return $topics;
	}

	/** 
	 * Returns the topic hierarchy from root down
	 * to the current topic's parent topic
	 *
	 * @param string $page
	 * @return array
	 */
	public function topic_hierarchy($page)
	{
		if ($this->_topic_hierarchy === null)
		{
			$this->_topic_hierarchy = $this->_topic_hierarchy($page);
		}

		return $this->_topic_hierarchy;
	}

	/** 
	 * Returns the links from the root topic down to the 
	 * current topic's parent
	 *
	 * @param string $page
	 * @return array
	 */
	protected function _topic_hierarchy($page)
	{
		// Traverse up to the root
		$paths = explode('.', $page);
		$hierarchy = array();

		// Add the documentation root / index
		$root_meta = $this->_file_meta('index');
		if (empty($root_meta))
		{
			throw new Exception('Unable to load manual index meta data');
		}

		$hierarchy['index'] = array(
			'link'	=> "/manual/$this->language",
			'title' => $root_meta['title']
		);

		// Remove the last node from the path since it represents
		// the current page and we don't need them in the heirarchy
		array_pop($paths);

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
			$meta_file = $this->_file_meta($concat_path);

			if (empty($meta_file))
			{
				return false;
			}

			$hierarchy[$path] = array(
				'link'		=> "/manual/$this->language/$concat_path",
				'title'		=> $meta_file['title']
			);
		}

		return $hierarchy;
	}

	/** 
	 * Returns the full path to the doc file
	 * 
	 * @param string $page
	 * @return string
	 */
	protected function _find_file($page)
	{
		$path = $this->language.DIRECTORY_SEPARATOR.$this->_extract_path($page);

		return Kohana::find_file('manual', $path, 'md');
	}

	/** 
	 * Returns the file meta as an array
	 *
	 * @param string $page
	 * @return array
	 */
	protected function _file_meta($page)
	{
		$path = $this->language.DIRECTORY_SEPARATOR.$this->_extract_path($page);

		// Find the file
		$file = Kohana::find_file('manual', $path, 'php');

		if ($file)
		{
			return include $file;
		}

		return false;
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
