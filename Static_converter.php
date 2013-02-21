<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Static_converter Class
 *
 * 静的なファイル(JS, CSSなど)のソース内部にあるURLパスをBASE_URLに合わせて置換する
 *
 * @package  		CodeIgniter
 * @subpackage		Libraries
 * @category		Libraries
 * @author			Lee JunHo
 * @license			http://github.com/shori0917/static_converter/
 * @link			http://github.com/shori0917/
 */

class Static_converter
{
	private $_ci;

	public $replace_str = '';

	/**
	 * Constructor - Sets Preferences
	 *
	 * The constructor can be passed an array of config values
	 */
	function __construct($config = array())
	{
		$this->_ci =& get_instance();

		if ( ! empty($config)) {
			$this->initialize($config);
		}

		log_message('debug', 'Static_converter class Initialized');
	}

	// --------------------------------------------------------------------

	function initialize($config) {
		foreach ($config as $key => $value) {
			$this->$key = $value;
		}
	}

	// --------------------------------------------------------------------

	function getdirlist($dirpath='' , $flag = true ) {

		if ( strcmp($dirpath,'')==0 ) die('dir name is undefind.');

		$files = array();
		$dirs = array();

		if( ($dir = @opendir($dirpath) ) == FALSE ) {
			die( "dir {$dirpath} not found.");
		}

		while ( ($file=readdir( $dir )) !== FALSE ) {
			if (is_dir($dirpath.DIRECTORY_SEPARATOR.$file) ) {
				if( strpos( $file ,'.' ) !== 0 ) {
					array_push($dirs, $this->getdirlist($dirpath.DIRECTORY_SEPARATOR.$file.DIRECTORY_SEPARATOR, $flag ));
				}
			}else {
				if( $flag ){
					$this->converter($dirpath.$file);

					array_push($files, $dirpath.$file);
				}else{
					if (strpos($file , '.' ) !==0) {
						$this->converter($dirpath.$file);

						array_push($files, $dirpath.$file);
					}
				}
			}
		}

		closedir($dir);
		return array("files"=> $files, "dirs"=> $dirs);
	}

	// --------------------------------------------------------------------

	function converter($filepath= '') {

		if (strcmp($filepath,'')==0) {

			log_message('error', 'ファイルが存在しません : ' . $filepath);
			return;
		}
		$path_info = pathinfo($filepath);

		// バックアップファイルがある場合は除く
		if (preg_match('/back/', $path_info['filename'])) {
			return;
		}

		$new_filepath = $path_info['dirname'].DIRECTORY_SEPARATOR.$path_info['filename'].'.back.'.$path_info['extension'];

		if(!is_readable($new_filepath)) {
			if (!copy($filepath, $new_filepath)) {
				log_message('error', 'ファイルがコピーに失敗しました : ' . $filepath);
				return;
			}
		}
		else {
			return;
		}

		$fp = @fopen($filepath, 'r');

		$string = '';

		if ($fp) {
			while ($tmp = fgets($fp)) {
				$string .= $tmp;
			}

			fclose($fp);

			$fp = @fopen($filepath, 'w');

			$pattern = '({domain})';
			$replacement = $this->replace_str;

			$string = str_replace($pattern, $replacement, $string);

			for ($written = 0; $written < strlen($string); $written += $fwrite) {
				$fwrite = fwrite($fp, substr($string, $written));
				if ($fwrite === false) {
					break;
				}
			}

			fclose($fp);
		}
	}

	// --------------------------------------------------------------------
}

// END Static_converter class
