<?php if (!defined('APPLICATION')) exit(); ?>
<h1><?php echo T($this->Data['Title']); ?></h1>
<div class="Info">Customize your Vanilla Forum installation with many SEO tweeks. Change the page title on various pages to better reflect the discussions on your vanilla forum.</div>
<div class="FilterMenu">
	<?php
		$ToggleName = C('Plugins.SEO.Enabled') ? T('Disable Search Engine Optimization') : T('Enable Search Engine Optimization');
		echo "<div>".Wrap(Anchor($ToggleName, 'plugin/seo/toggle/'.Gdn::Session()->TransientKey(), 'SmallButton'))."</div>";
	?>
</div>
<?php if ( C('Plugins.SEO.Enabled') ) : ?>

<h3><?php echo T('Dynamic Page Titles'); ?></h3>

<ul>
	<li>
      <div class="Info"><?php echo T('This is a list of all the possible tags and their relative descriptions.'); ?></div>
      <table class="Label AltColumns">
         <tbody>
<?php foreach ( $this->DynamicTitleTags as $field => $description ) : ?>

			<tr class="Alt">
               <th><?php echo T('%'.$field.'%'); ?></th>
               <td class="Alt"><?php echo T($description); ?></td>
            </tr>
            
<?php endforeach; ?>         	
            
         </tbody>
       </table>
   </li>
</ul>


<?php echo $this->Form->Open(); ?>

<?php echo $this->Form->Errors(); ?>
<ul>
	<li>
      <table class="Label AltColumns">
      	
      	<thead>
            <tr>
               <th><?php echo T('Dynamic Page Title'); ?></th>
               <th>Custom Title</th>
               <th>Parsed Tags &amp; Examples</th>
               <th>Plugin Default</th>
            </tr>
         </thead>
         <tbody>


	<?php foreach ( $this->DynamicTitles as $field => $title ) : ?>
	<tr>
		<?php
			echo Wrap($this->Form->Label($title['name'], $field).
						'<br />'.T($title['info']), 'td');
			echo Wrap($this->Form->Input($field, 'text').'<br /><br />Allowed Tags: <br /><strong>%'.implode('%</strong> <strong>%', $title['fields']).'%</strong>', 'td', array('class' => 'Info'));
			echo Wrap(T('Example URL: <br /><strong>'.implode('</strong> <br /><strong>', $title['examples']).'</strong>'),
					'td',
					array('class' => 'Info'));
			echo Wrap(T($title['default']), 'td');
		?>
	</tr>
	<?php endforeach; ?>

         </tbody>
       </table>
   </li>
</ul>
<?php echo $this->Form->Close('Save'); ?>

<?php endif; ?>