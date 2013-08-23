<?php defined('SYSPATH') or die('No direct script access.');

class Kohana_Vertebro_Exception extends Kohana_Exception {

	/**
	 *
	 *
	 * @param  int    code
	 * @param  mixed  errors
	 * @return Kohana_Vertebro_Exception
	 */
	public static function factory($errors, $code = 400)
	{
		return new Vertebro_Exception($errors, $code);
	}

	/**
	 * @var  int
	 */
	protected $_code = 400;

	/**
	 * @var mixed
	 */
	protected $_errors;

	/**
	 * @var
	 */
	protected $_message;

	/**
	 *
	 *
	 * @param  int    code
	 * @param  mixed  errors
	 * @return Kohana_Exception
	 */
	public function __construct($error, $code)
	{
		// Set up the Kohana_Exception to ride on
		parent::__construct(json_encode($this->errors($error)), array($error), $this->_code);

		$this->code($code)
			->errors($error);

		return $this;
	}

	public function __toString()
	{
		return json_encode($this->errors());
	}

	/**
	 * Setter & Getter for the code
	 *
	 * @param  int  code  Code to set
	 * @return mixed
	 */
	public function code($code = NULL)
	{
		if( ! $code)
			return $this->_code;

		$this->_code = $code;

		return $this;
	}

	/**
	 * Setter & Getter for the errors
	 *
	 * @param  mixed  errors  Errors to set
	 * @return mixed
	 */
	public function errors($errors = NULL)
	{
		if ( ! $errors)
		{
			$errors = $this->_errors;
			return (is_array($errors)) ? reset($errors) : $errors;
		}

		$this->_errors = $errors;

		return $this;
	}
}
