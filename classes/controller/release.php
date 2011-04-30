<?php
/**
 * Base module controller
 *
 * @package    Leemo/Release
 * @author     Alexey Popov
 * @copyright  (c) 2011 Leemo Studio
 * @license    http://www.opensource.org/licenses/bsd-license.php
 */
class Controller_Release extends Controller {

	/**
	 * ZipArchive object
	 * @var ZipArchive
	 */
	protected $_zip;

	/**
	 * Temporary release archive name
	 * @var string
	 */
	protected $_name;

	protected $_template = 'release/index';

	protected $_styles = array();

	protected $_scripts = array();

	protected $_config = array();

	public function before()
	{
		parent::before();

		// Instantiate new ZipAchive object
		$this->_zip  = new ZipArchive;

		// Generate temporary archive name
		$this->_name = Kohana::$cache_dir.DIRECTORY_SEPARATOR.date('Y-m-d--H-i-s').'.zip';

		// Instantiate View object
		$this->_template = View::factory($this->_template);

		$this->_config = Kohana::config('release');

		foreach (array('names', 'extensions', 'files') as $param)
		{
			if ( ! isset($this->_config[$param]) OR
				! is_array($this->_config[$param]))
			{
				$this->_config[$param] = array();
			}
		}

		// Basic javascript libraries list
		$this->_scripts = array
		(
			'js/jquery-1.5.2.min.js',
			'js/jquery.checkboxtree.min.js'
		);

		// Basic styles list
		$this->_styles  = array
		(
			'css/style.css',
			'css/jquery.checkboxtree.min.css'
		);
	}

	public function action_index()
	{
		if ($_POST)
		{
			$this
				->_save($_POST['files'])
				->_zip($_POST['files']);

			// Send the file content as the response
			$this->response->body(file_get_contents($this->_name));

			// Set the proper headers to allow caching
			$this->response->headers('content-type',  File::mime_by_ext('zip'));
			$this->response->headers('last-modified', date('r', filemtime($this->_name)));

			$this->response->headers('content-disposition', 'attachment; filename="'.pathinfo($this->_name, PATHINFO_FILENAME).'.zip"');

			// Remove temporary archive file
			@unlink($this->_name);

			return;
		}

		$this->_template->title = __('Choose files to generate release package');

		$this->_template
			->bind('styles', $this->_styles)
			->bind('scripts', $this->_scripts)
			->set('files', $this->get_directory_array(DOCROOT));

		$this->response->body($this->_template);
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
		$file = substr($file, 0, - (strlen($ext) + 1));

		if ($file = Kohana::find_file('media', 'release/'.$file, $ext))
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
	 * Creates a temporary zip archive in cache directory
	 *
	 * @param   array   $files  Files to release
	 */
	protected function _zip(array $files = array())
	{
		if ($this->_zip->open($this->_name, ZipArchive::CREATE) === TRUE)
		{
			foreach ($files as $file)
			{
				$file = rtrim($file, DIRECTORY_SEPARATOR);

				if (is_dir($file))
				{
					$this->_zip->addEmptyDir(str_replace(DOCROOT, '', $file));
				}
				elseif (is_file($file))
				{
					$this->_zip->addFile($file, str_replace(array(DOCROOT, '.rename'), '', $file));
				}
			}

			$this->_zip->close();
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
	protected function get_directory_array($path, $data = array())
	{
		$handle = opendir($path);

		while (FALSE !== ($file = readdir($handle)))
		{
			if ( ! in_array($file, $this->_config['names']) AND $file != '.' AND $file != '..')
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

	/**
	 * Saves release settings into application config
	 *
	 * @param   array   $data  Settings array
	 * @return  Controller_Release
	 */
	protected function _save(array $files = NULL)
	{
		$data = View::factory('release/config')
			->set('files', $files)
			->set('names', is_array($this->_config['names']) ? $this->_config['names'] : array())
			->set('extensions', is_array($this->_config['extensions']) ? $this->_config['names'] : array());

		$file = APPPATH.'config'.DIRECTORY_SEPARATOR.'release'.EXT;

		if ( ! is_dir(pathinfo($file, PATHINFO_DIRNAME)))
		{
			mkdir(pathinfo($file, PATHINFO_DIRNAME));
		}

		@unlink($file);

		// Creating empty file if it is not exists
		// If exists - this operation will make no harm to it
		fclose(fopen($file, "a+b"));

		// File blocking
		if( ! ($f = fopen($file, "r+b")))
		{
			throw new Kohana_Exception('Can\'t open cache file :file', array(
				':file' => $file
			));
		}

		// Waiting a monopole owning
		flock($f, LOCK_EX);

		// Writing file
		fwrite($f, $data);

		fclose($f);

		return $this;
	}

} // End of Kohana_Controller_Release