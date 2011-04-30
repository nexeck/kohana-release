<?php echo '<?php defined(\'SYSPATH\') OR die(\'No direct access allowed.\');' ?>
<?php

	$search = array
	(
		APPPATH,
		MODPATH,
		SYSPATH,
		DOCROOT,
		'/',
		'\\',
		EXT
	);

	$replace = array
	(
		'APPPATH.\'',
		'MODPATH.\'',
		'SYSPATH.\'',
		'DOCROOT.\'',
		'\'.DIRECTORY_SEPARATOR.\'',
		'\'.DIRECTORY_SEPARATOR.\'',
		'\'.EXT.\''
	);

?>


return array
(
	// Release files
	'files'      => array
	(
<?php foreach ($files as $file): ?>
		<?php
			echo str_replace(".''", '', str_replace($search, $replace, $file).'\'');
		?>,
<?php endforeach ?>
	),

	// Ignored extensions
	'extensions' => array
	(
<?php foreach ($extensions as $extension): ?>
		'<?php echo $extension ?>',
<?php endforeach ?>
	),

	// Ignored filenames and directory names
	'names' => array
	(
<?php foreach ($names as $name): ?>
		'<?php echo $name ?>',
<?php endforeach ?>
	),
);
