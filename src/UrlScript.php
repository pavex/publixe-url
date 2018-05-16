<?php

	namespace Publixe;
	use Publixe;


/**
 * URL container.
 *
 * @author	Pavex <pavex@ines.cz>
 */
	class UrlScript extends Publixe\Url
	{


/** @var string */
		public $scriptPath = '/';





/**
 * @param string
 * @param string
 */
		public function __construct($url = NULL, $script_path = NULL)
		{
			parent::__construct($url);
			$this -> scriptPath = $script_path;
		}





/**
 * Returns the base-path (/folder1/script.php => /folder1/).
 * @return string
 */
		public function getBasePath()
		{
			return $this -> scriptPath;
		}





/**
 * Returns the base-url, etc. for <base> element.
 * @return string
 */
		public function getBaseUrl()
		{
			return $this -> getHostUrl() . $this -> getBasePath();
		}





/**
 * @return string
 */
		public function getRelativeUrl()
		{
			$len = strlen($this -> getBasePath());
			$path = substr($this -> path, 0, $len) == $this -> getBasePath() 
				? substr($this -> path, $len) : $this -> path;

			return $path
				. (($query = $this -> getQuery()) === NULL ? '' : '?' . $query)
				. (empty($this -> fragment) ? '' : '#' . $this -> fragment);
		}





/**
 * @return string
 */
		public function getAbsoluteUrl()
		{
			return $this -> getHostUrl() . parent::getRelativeUrl();
		}





/**
 * @return bool
 */
		public function isBase()
		{
			return $this -> path == $this -> scriptPath;
		}





	}
