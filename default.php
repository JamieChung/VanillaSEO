<?php

$PluginInfo['VanillaSEO'] = array
(
 	'Name' 				=>	'Vanilla SEO',
	'Description'			=>	T('Vanilla SEO is your all in one plugin for optimizing your Vanilla forum for search engines.'),
	'Version'				=>	'0.1',
	'RequiredPlugins'		=>	FALSE,
	'HasLocale'			=>	FALSE,
	'SettingsUrl'			=>	FALSE,
	'SettingsPermission'	=>	FALSE,
	'Author'				=>	'Jamie Chung',
	'AuthorEmail'			=>	'me@jamiechung.me',
	'AuthorUrl'			=>	'http://www.jamiechung.me'
);

class VanillaSEO extends Gdn_Plugin 
{
	private $tags = array ( 'discussion', 'category', 'garden', 'page');
	private $titles = array(
		'single_discussion' => '%discussion% - %category% on %garden_title%',
		'page_discussion' => '%discussion% - Page %page% - %category% on %garden_title%,',
		'page_discussion' => '%discussion% - %category% on Page %page% of %garden_title%',
		'single_tag' => '',
		'page_tag' => '',
		'home_discussions' => 'Collegiate Talk is awesome!',
		'my_discussions' => 'MY Collegiate Talk is awesome!',
		'single_category' => '',
		'page_category' => ''
	);
	
	public function SettingsController_Render_Before($Sender)
	{
		if (Gdn::Dispatcher()->Application() == 'vanilla' && Gdn::Dispatcher()->ControllerMethod() == 'addcategory')
		{
			
		}
	}
	
	public function DiscussionsController_Render_Before ( $Sender )
	{
		$data = array();
		switch ( Gdn::Dispatcher()->ControllerMethod() )
		{
			case 'mine':
				$type = 'my_discussions';
				break;
				
			default:
				$type = 'home_discussions';
				break;
		}
		$this->ParseTitle($Sender, $data, $type );
	}
	
	private function ParseTitle ( &$Sender, $data, $type )
	{
		if ( !isset($this->titles[$type]) )
			return;
		
		$title = $this->titles[$type];
		foreach ( array_keys($data) as $field )
		{
			$title = str_replace('%'.$field.'%', $data[$field], $title);
		}
		
		$Sender->Head->Title($title);
	}
	
	public function DiscussionController_Render_Before ( $Sender )
	{
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

