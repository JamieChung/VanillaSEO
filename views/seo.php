<?php if (!defined('APPLICATION')) exit(); ?>
<h1><?php echo T($this->Data['Title']); ?></h1>
<div class="Info">Customize your Vanilla Forum installation with many SEO tweeks. Change the page title, meta descriptions and keywords.</div>
<div class="FilterMenu">
	<?php
		$ToggleName = C('Plugins.SEO.Enabled') ? T('Disable Search Engine Optimization') : T('Enable Search Engine Optimization');
		echo "<div>".Wrap(Anchor($ToggleName, 'plugin/seo/toggle/'.Gdn::Session()->TransientKey(), 'SmallButton'))."</div>";
	?>
</div>
<?php if ( C('Plugins.SEO.Enabled') ) : ?>

<h3><?php echo T('Dynamic Page Titles'); ?></h3>

<ul>
	<li id="CaptchaSettings">
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
	<?php foreach ( $this->DynamicTitles as $field => $title ) : ?>
	<li>
		<?php
			echo $this->Form->Label($title['name'], $field);
			echo Wrap(
					T($title['info'].
					'<br />&mdash; Allowed tags: <strong>%'.implode('%</strong> | <strong>%', $title['fields']).'</strong>'.
					'%'.
					'<br />&mdash; Example URL: <strong>'.implode('</strong>, <strong>', $title['examples']).'</strong>'),
					'div',
					array('class' => 'Info'));
			echo $this->Form->Input($field, 'text');
		?>
	</li>
	<li> &nbsp; </li>
	<?php endforeach; ?>
</ul>
<?php echo $this->Form->Close('Save'); ?>

<?php endif; ?>