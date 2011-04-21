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

	protected $template = 'release';

	protected $styles = array();

	protected $scripts = array();

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
		else
		{
			// Instantiate View object
			$this->template = View::factory($this->template);

			// Basic javascript libraries list
			$this->scripts = array
			(
				'js/jquery-1.5.2.min.js',
				'js/jquery.checkboxtree.min.js'
			);

			// Basic styles list
			$this->styles  = array
			(
				'css/style.css',
				'css/jquery.checkboxtree.min.css'
			);
		}
	}

	public function action_index()
	{
		$this->template->title = __('Choose files to generate release package');

		$this->template
			->bind('styles', $this->styles)
			->bind('scripts', $this->scripts)
			->set('files', $this->get_directory_array(DOCROOT));

		$this->response->body($this->template);
	}

	public function action_download()
	{
		
	}

	/**
	 * @author Kohana team
	 */
	public function action_media()
	{
		// Get the file path from the request
		$file = $this->request->param('file');

		// Find the file extension
		$ext = pathinfo($file, PATHINFO_EXTENSION);

		// Remove the extension from the filename
		$file = substr($file, 0, -(strlen($ext) + 1));

		if ($file = Kohana::find_file('media/release', $file, $ext))
		{
			// Check if the browser sent an "if-none-match: <etag>" header, and tell if the file hasn't changed
			$this->response->check_cache(sha1($this->request->uri()).filemtime($file), $this->request);

			// Send the file content as the response
			$this->response->body(file_get_contents($file));

			// Set the proper headers to allow caching
			$this->response->headers('content-type',  File::mime_by_ext($ext));
			$this->response->headers('last-modified', date('r', filemtime($file)));
		}
		else
		{
			// Return a 404 status
			$this->response->status(404);
		}
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
					if ($file != '.' AND $file != '..')
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

	/**
	 * Recursively reads directory
	 * and retuns files and directories associative array
	 *
	 * @param   string  $path
	 * @param   array   $data
	 * @return  array
	 */
	protected function get_directory_array($path, array $data = NULL)
	{
		$handle = opendir($path);

		while (FALSE !== ($file = readdir($handle)))
		{
			if ( ! in_array($file, array('.', '..', '.git', '.svn', '.cvs')))
			{
				if (is_file($path.DIRECTORY_SEPARATOR.$file))
				{
					$data[] = $file;
				}
				else
				{
					$data[$file] = $this->get_directory_array($path.DIRECTORY_SEPARATOR.$file);
				}
			}
		}

		return $data;
	}

} // End of Kohana_Controller_Release