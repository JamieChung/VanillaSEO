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
<?php endif; ?>

<?php echo $this->Form->Open(); ?>

<?php echo $this->Form->Errors(); var_dump($$Fields); ?>
<ul>
	<?php foreach ( $Fields as $field ) : ?>
	<li><?php echo $field['name']; ?></li>
	<?php endforeach; ?>
</ul>
<?php echo $this->Form->Close(); ?>