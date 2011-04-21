<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <base href="<?php echo URL::base() ?>" />
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title><?php echo $title ?></title>
<?php foreach ($scripts as $script)
{
	echo '    '.HTML::script(Route::get('release')->uri(array('action' => 'media', 'file' => $script)))."\n";
}
?>
<?php foreach ($styles as $style)
{
	echo '    '.HTML::style(Route::get('release')->uri(array('action' => 'media', 'file' => $style)))."\n";
}
?>
    <link rel="icon" href="<?php echo Route::get('release')->uri(array('action' => 'media', 'file' => 'ico/favicon.ico')) ?>" type="image/x-icon" />
    <link rel="shortcut icon" href="<?php echo Route::get('release')->uri(array('action' => 'media', 'file' => 'ico/favicon.ico')) ?>" type="image/x-icon" />
  </head>
  <body>
    <h1><?php echo $title ?></h1>
    <p><?php echo __('Choosed files list will be saved in <code>:path</code> file automatically', array(':path' => APPPATH.'config'.DIRECTORY_SEPARATOR.'release'.EXT)) ?>.</p>
    <p><?php echo __('Note that files <code>file.ext.rename</code> will be renamed to <code>file.ext</code>') ?></p>
    <?php echo show_tree($files) ?>
    <script type="text/javascript">
      $(document).ready(function(){
        $('#tree').checkboxTree({})
      })
    </script>
  </body>
</html>
<?php
	function show_tree(array $tree, $path = NULL)
	{
		if (sizeof($tree) == 0) return;

		$text = '<ul'.(empty($path) ? ' '.HTML::attributes(array('id' => 'tree')) : '').'>';

		foreach($tree as $key => $val)
		{
			if (is_array($val))
			{
				$text .= '<li><input type="checkbox" name="files[]" value="'.HTML::chars($path.$key.DIRECTORY_SEPARATOR).'" />'.$key.show_tree($val, $path.$key.DIRECTORY_SEPARATOR).'</li>';
			}
			else
			{
				$text .= '<li><input type="checkbox" name="files[]" value="'.HTML::chars($path.$val).'" />'.$val.'</li>';
			}
		}

		return $text.'</ul>';
	}
?>