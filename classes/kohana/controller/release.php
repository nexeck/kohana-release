<?php
/**
 * Base module controller
 *
 * @package    Leemo/Release
 * @author     Alexey Popov
 * @copyright  (c) 2011 Leemo Studio
 * @license    http://www.opensource.org/licenses/bsd-license.php
 */
class Kohana_Controller_Release extends Controller {

	/**
	 * ZipArchive object
	 * @var ZipArchive
	 */
	protected $zip;

	/**
	 * Temporary release archive name
	 * @var string
	 */
	protected $name;

	public function before()
	{
		parent::before();

		if ( (bool) Kohana::find_file('config', 'release') === TRUE)
		{
			// Instantiate new ZipAchive object
			$this->zip  = new ZipArchive;

			// Generate temporary archive name
			$this->name = date('Y-m-d--H-i-s').'.zip';
		}
	}

	public function action_index()
	{
		if ( ! $this->zip instanceof ZipArchive)
		{
			Request::current()
				->redirect(Route::get('release')->uri(array('action' => 'generate')));
		}
	}

	public function action_generate()
	{
		
	}

	/**
	 * 
	 *
	 * @param <type> $archive_name
	 * @param <type> $folder 
	 */
	protected function zip($archive_name, $folder)
	{
		if ($zip->open($this->name, ZipArchive::CREATE) === TRUE)
		{
			$dir = preg_replace('/[\/]{2,}/', '/', $archive_folder.'/');

			$dirs = array($dir);
			while (sizeof($dirs))
			{
				$dir = current($dirs);
				$zip -> addEmptyDir($dir);

				$dh = opendir($dir);
				while($file = readdir($dh))
				{
					if ($file != '.' && $file != '..')
					{
						if (is_file($file))
						{
							$zip -> addFile($dir.$file, $dir.$file);
						}
						elseif (is_dir($file))
						{
							$dirs[] = $dir.$file."/";
						}
					}
				}

				closedir($dh);
				array_shift($dirs);
			}

			$zip -> close();
		}
	}

} // End of Kohana_Controller_Release