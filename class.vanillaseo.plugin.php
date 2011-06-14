<?php

$PluginInfo['VanillaSEO'] = array (
 	'Name'					=>	'Vanilla SEO',
	'Description'			=>	T('Vanilla SEO is your all in one plugin for optimizing your Vanilla forum for search engines.'),
	'Version'				=>	'0.1',
	'RequiredPlugins'		=>	FALSE,
	'HasLocale'				=>	FALSE,
	'SettingsUrl'			=>	'/dashboard/plugin/seo',
	'SettingsPermission'	=>	'Garden.Settings.Manage',
	'Author'				=>	'Jamie Chung',
	'AuthorEmail'			=>	'me@jamiechung.me',
	'AuthorUrl'				=>	'http://www.jamiechung.me'
);

class VanillaSEOPlugin extends Gdn_Plugin 
{
	// All available %tags%.
	private $tags = array ( 'discussion', 'category', 'garden', 'page' );
	
	// Default titles for each part of the vanilla rewrite scheme.
	public $dynamic_titles = array (
		
		// CATEGORIES
		'categories_all'		=>	array(
					'default' 	=> 'All Categories on %garden%',
					'fields'	=> array('garden'),
					'name'		=> 'All Categories',
					'info'		=> 'Page where all categories are.'
					// Example: /categories/all
		),
		'category_single'		=>	array(
					'default' 	=> '%category% Discussions on %garden%',
					'fields'	=> array('garden', 'category'),
					'name'		=> 'Single Category page',
					'info'		=> 'Category view displaying relevent discussions.'
					// Example: /categories/general-forum, /categories/general-forum/p2, /categories/general-forum/feed.rss
		),
		// 'category_paged'		=>	array(
					// 'default' 	=> '%category% Discussions on Page %page of %garden%',
					// 'fields'	=> array('garden', 'page'),
					// 'name'		=> 'Paged Category',
					// 'info'		=> 'Viewing discussions on additional pages of gategories.'
		// ),
		'category_discussions'	=>	array(
					'default' 	=> 'View Discussions and Categories on %garden%',
					'fields'	=> array('garden'),
					'name'		=> 'Sample Categories',
					'info'		=> 'Showing all categories and a few discussions from each category.'
					// Example: /categories
		),
		
		'activity'				=> array(
					'default'	=> 'Recent Activity on %garden%',
					'fields'	=> array('garden'),
					'name'		=> 'Recent Activity',
					'info'		=> 'Page listing recent activity on your vanilla forum.'
					// Example:	/activity
		),
		
		'discussions'			=> array(
					'default'	=> 'Recent Discussions on %garden%',
					'fields'	=> array('garden'),
					'name'		=> 'Discussions Home Page',
					'info'		=> 'Page listing recent discussions on your vanilla forum.'
					// Example:	/activity
		),
	);

/*
 * 
 * Moving this array for the time being.
		'single_discussion' 		=>	'%discussion% - %category% on %garden_title%',
		'page_discussion' 			=>	'%discussion% - Page %page% - %category% on %garden_title%,',
		'page_discussion' 			=>	'%discussion% - %category% on Page %page% of %garden_title%',
		'single_tag' 				=>	'',
		'page_tag' 					=>	'',
		'home_discussions'			=>	'HOME Collegiate Talk is awesome!',
		'bookmarked_discussions'	=>	'BOOKMARKED',
		'my_discussions' 			=>	'MY Collegiate Talk is awesome!',
		'all_categories'			=>	'ALL CAT',
		'single_category' 			=>	'SINGLE_CAT',
		'page_category' 			=>	'PAGE_CAT',
 * 
 * 
 * 
 * 
		'category_single'			=>	'SINGLE_CAT',
		'category_paged'			=>	'PAGED_CAT',
		'category_discussions'		=>	'CAT_DISCUSSIONS',
		
		// DISCUSSIONS
		
		
		// TAGS
		
		// OTHER PAGES
		'activity'					=> 'SHOW ACTIVITY'
 * 
 */

 	public function GetTitle ( $type )
	{
		if ( C('Plugins.SEO.DynamicTitles.'.$type) )
		{
			return stripslashes(strip_tags(C('Plugins.SEO.DynamicTitles.'.$type)));
		}
		else
		{
			return $this->dynamic_titles[$type]['default'];
		}
	}
 	
	public function Base_GetAppSettingsMenuItems_Handler ( $Sender )
	{
		$Menu = $Sender->EventArguments['SideMenu'];
		$Menu->AddItem('Site Settings', T('Settings'));
		$Menu->AddLink('Site Settings', T('Search Engine Optimization'), 'plugin/seo', 'Garden.Settings.Manage');
	}
	
	public function PluginController_SEO_Create ( $Sender )
	{
		$Sender->Permission('Garden.Settings.Manage');
		$Sender->Title(T('Search Engine Optimization'));
		$Sender->AddSideMenu('plugin/seo');
		
		$Sender->Form = new Gdn_Form();
		
		$this->Dispatch($Sender, $Sender->RequestArgs);
	}
	
	public function Controller_Index ( $Sender )
	{
		$Sender->DynamicTitles = $this->dynamic_titles;
		
		if ( C('Plugins.SEO.Enabled') )
		{
			if ( $Sender->Form->AuthenticatedPostBack() === TRUE )
			{
				foreach ( $this->dynamic_titles as $field => $info )
				{
					SaveToConfig('Plugins.SEO.DynamicTitles.'.$field, htmlspecialchars(addslashes($Sender->Form->GetValue($field))));
				}
				
				$Sender->StatusMessage = T('Your settings have been saved.');
			}
			
			foreach ( $this->dynamic_titles as $field => $info )
			{
				$Sender->Form->SetFormValue($field, $this->GetTitle($field));
			}
			
		}
			
		$Sender->Render($this->GetView('seo.php'));
	}
	
	public function Controller_Toggle ( $Sender )
	{
		if ( Gdn::Session()->ValidateTransientKey(GetValue(1, $Sender->RequestArgs)) )
		{
			if ( C('Plugins.SEO.Enabled') )
			{
				RemoveFromConfig('Plugins.SEO.Enabled');
			}
			else
			{
				SaveToConfig('Plugins.SEO.Enabled', TRUE);
			}
		}
		
		redirect('plugin/seo');
	}
	
	public function PagerModule_GetOffset_Create ( $Sender )
	{
		return $Sender->Offset;
	}
	
	public function CategoriesController_Render_Before ( $Sender )
	{
		$data = array();
		switch ( Gdn::Dispatcher()->ControllerMethod() )
		{
			case 'all':
				$type = 'categories_all';
				break;
			default:
				if ( isset($Sender->Data['Category']) )
				{
					$type = 'category_single';
					$data['category'] = $Sender->Category->Name;
				}
				else
				{
					$type = 'category_discussions';
				}
				break;
		}
		
		$this->ParseTitle($Sender, $data, $type);
	}
	
	public function ActivityController_Render_Before ( $Sender )
	{
		$this->ParseTitle($Sender, '', 'activity');
	}
	
	public function DiscussionsController_Render_Before ( $Sender )
	{
		$data = array();
		switch ( Gdn::Dispatcher()->ControllerMethod() )
		{
			// We don't need these personal pages yet because no Search Engines visit them.
			/*
			case 'mine':
				$type = 'my_discussions';
				break;
			case 'bookmarked':
				$type = 'bookmarked_discussions';		
				break;
			*/
			
			case 'index':
				$type = 'discussions';
				break;
		}
		$this->ParseTitle($Sender, $data, $type );
	}
	
	private function ParseTitle ( &$Sender, $data, $type )
	{
		if ( !isset($this->dynamic_titles[$type]) )
			return;
		
		$dynamic = $this->dynamic_titles[$type];
		$title = $this->GetTitle($type);
		
		if ( !isset($data['garden']) )
		{
			$data['garden'] = C('Garden.Title');
		}
		
		foreach ( $dynamic['fields'] as $field )
		{
			$title = str_replace('%'.$field.'%', isset($data[$field]) ? $data[$field] : '', $title);
		}
		
		$Sender->Head->Title($title);
	}
	
	public function Enabled ()
	{
		return ( C('Plugins.SEO.Enabled') == TRUE );
	}
	
	public function DiscussionController_Render_Before ( $Sender )
	{
		if ( !$this->Enabled() )
			return;
			
		$tags = array();
		
		// Check if we have tags from current discussion.
		if ( isset($Sender->Discussion->Tags) )
		{
			$tags += explode(' ', $Sender->Discussion->Tags);
		}
		
		// Calculate Page for Single discussion.
		$Offset = (int) $Sender->Offset;
		$Limit = (int) C('Vanilla.Comments.PerPage', 50);
		$page = (int) PageNumber($Offset, $Limit);		
		if ( $page <= 0 )
			$page = 1;
		
		$tags = array_unique($tags);
		if ( count($tags) > 0 )
		{
			$Sender->Head->AddTag('meta', array('name' => 'keywords', 'content' => implode(', ', $tags)));
		}		
		
		$Sender->Head->AddTag('meta', array('name' => 'description', 'content'=> $Sender->Discussion->Name));
		
		$data = array (
			'discussion' => $Sender->Discussion->Name,
			'category' => $Sender->Discussion->Category,
			'garden_title' => C('Garden.Title'),
		);
		
		$type = 'single_discussion';
		
		// We are on a 2+ page discussion.
		if ( $page > 1 )
		{
			$data['page'] = $page;
			$type = 'page_discussion';			
		}
		
		$this->ParseTitle($Sender, $data, $type);
	}
	
	public function Setup ()
	{
		
	}
}

