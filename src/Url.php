<?php

	namespace Publixe;
	use Publixe;
	use \UnexpectedValueException;
	use \InvalidArgumentException;


/**
 * URL container.
 *
 * @author	Pavex <pavex@ines.cz>
 */
	class Url
	{


/** @var array */
		public static $defaultPorts = array(
			'http' => 80,
			'https' => 443,
			'ftp' => 21,
			'news' => 119,
			'nntp' => 119
		);

/** @var string */
		public $scheme;

/** @var string */
		public $host;

/** @var int */
		public $port = NULL;

/** @var string */
		public $user;

/** @var string */
		public $pass;

/** @var string */
		public $path;

/** @var string */
		public $fragment;

/** @var array */
		private $params = array();





/**
 * @param string
 */
		public function __construct($url = NULL)
		{
			if ($url !== NULL) {
				$this -> setUrl($url);
			}
		}





/**
 * @param string
 */
		public function setUrl($url)
		{
			if (!is_string($url)) {
				throw new UnexpectedValueException("String required.");
			}
			if (($data = @parse_url($url)) === FALSE) {
				throw new UnexpectedValueException(sprintf("Can not parse URL [url=%s]", $url));
			}
			$this -> scheme = isset($data['scheme']) ? strtolower($data['scheme']) : NULL;
			$this -> host = isset($data['host']) ? $data['host'] : NULL;
			$this -> port = isset($data['port']) ? $data['port'] : NULL;
			$this -> user = isset($data['user']) ? $data['user'] : NULL;
			$this -> pass = isset($data['pass']) ? $data['pass'] : NULL;
			$this -> path = isset($data['path']) ? $data['path'] : NULL;
			$this -> fragment = isset($data['fragment']) ? $data['fragment'] : NULL;
			$this -> params = array();

			if (!$this -> port && isset(self::$defaultPorts[$this -> scheme])) {
				$this -> port = self::$defaultPorts[$this -> scheme];
			}
			if ($this -> path === '' && (preg_match('/^http/', $this -> scheme))) {
				$this -> path = '/';
			}
			if (isset($data['query'])) {
				$this -> setQuery($data['query']);
			}
		}





/**
 * @param string
 */
		public function setQuery($query)
		{
			parse_str($query, $this -> params);
		}





/**
 * @return string|NULL
 */
		public function getQuery()
		{
			if (empty($this -> params)) {
				return NULL;
			}
			ksort($this -> params);
			return str_replace("%3A", ":", http_build_query($this -> params));
		}





/**
 * @param array
 * @param bool	Merge with existing params
 */
		public function setParams(array $params, $merge = FALSE)
		{
			$this -> params = $merge ? array_merge($this -> params, $params) : $params;
		}





/**
 * @return array
 */
		public function getParams()
		{
			return $this -> params;
		}





/**
 * @param string
 * @param mixed
 */
		public function setParam($name, $value)
		{
			$this -> params[$name] = $value;
		}





/**
 * @param  string
 * @param  mixed
 * @param  bool
 * @return mixed
 */
		public function getParam($name, $default = NULL, $strict = TRUE)
		{
			return get_array_item($name, $this -> params, $default, $strict);
		}





/**
 * Returns the base-path (/folder1/script.php => /folder1/).
 * @return string
 */
		public function getBasePath()
		{
			$pos = strrpos($this -> path, '/');
			return $pos === FALSE ? '' : substr($this -> path, 0, $pos + 1);
		}





/**
 * Returns the [user[:pass]@]host[:port] part of URL.
 * @return string
 */
		public function getAuthority()
		{
			if ($this -> host) {
				$authority = $this -> host;

				if ($this -> port && !isset(self::$defaultPorts[$this -> scheme])) {
					$authority .= ':' . $this -> port;
				}
				if (!empty($this -> user)) {
					$authority = $this -> user . (empty($this -> pass) ? '' : ':' . $this -> pass)
						. '@' . $authority;
				}
				return $authority;
			}
			return '';
		}





/**
 * @return string
 */
		public function getHostUrl()
		{
			if (($authority = $this -> getAuthority()) !== '') {
				return ($this -> scheme ? $this -> scheme . ':' : '')
					. '//' . $authority;
			}
			return '';
		}





/**
 * @return string
 */
		public function getRelativeUrl()
		{
			return $this -> path
				. (($query = $this -> getQuery()) === NULL ? '' : '?' . $query)
				. (empty($this -> fragment) ? '' : '#' . $this -> fragment);
		}





/**
 * @return string
 */
		public function getAbsoluteUrl()
		{
			return $this -> getHostUrl() . $this -> getRelativeUrl();
		}





/**
 * Return absolute or relative URL if host is available
 * @return string
 */
		public function getUrl()
		{
			if (empty($this -> host)) {
				return $this -> getRelativeUrl();
			}
			return $this -> getAbsoluteUrl();
		}





/**
 * @return string
 */
		public function __toString()
		{
			return $this -> getUrl();
		}





/**
 * Validate url and url protocol
 * @param string
 * @throw \InvalidArgumentException
 */
		public static function validate($url, $protocols = [])
		{
			if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
				throw new InvalidArgumentException('Invalid url');
			}
			if (is_array($protocols) && count($protocols) > 0) {
				$pattern = sprintf('/^(%s)/', implode('|', $protocols));
				if (!preg_match($pattern, $url)) {
					throw new InvalidArgumentException('Invalid protocol');
				}
			}
		}





	}