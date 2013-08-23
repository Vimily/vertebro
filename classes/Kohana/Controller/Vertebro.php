<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Simple REST controller that maps the different request methods to the
 * correct action. Before doing this the controller checks if the requested
 * method / action exists.
 */
class Kohana_Controller_Vertebro extends Controller {

	/**
	 * @var  Response body to JSON encoded and returned
	 */
	protected $body;

	/**
	 * @var  array  map of methods
	 */
	protected $_method_map = array
	(
		Request::POST   => 'post',
		Request::GET    => 'get',
		Request::PUT    => 'put',
		Request::DELETE => 'delete',
	);

	/**
	 * Checks if the requested method is in the method map and if the mapped
	 * action is also declared in the controller. Throws a HTTP Exception 405
	 * if not so.
	 *
	 * @throws  HTTP_Exception_405
	 */
	public function before()
	{
		// Check method Support
		if ( ! isset($this->_method_map[$this->request->method()]))
			throw HTTP_Exception::factory(405)->allowed($this->_method_map);

		// Generate the action name based on the HTTP Method of the request, and a supplied action
		$action_name = ($this->request->action() === 'index')
			? Arr::get($this->_method_map, $this->request->method())
			: Arr::get($this->_method_map, $this->request->method()).'_'.$this->request->action();


		$this->request->action($action_name);
	}

	public function execute()
	{
		// Execute the correct CRUD action based on the requested method
		try
		{
			// Try and return the normal request
			return parent::execute();
		}
		catch(Vertebro_Exception $e)
		{
			// Assign the error code
			$this->response->status($e->code());

			// Assign the body
			$this->body = array('error' => $e->errors());
		}
		catch(Kohana_Exception $e) {

			if (Kohana::$environment != Kohana::DEVELOPMENT)
			{
				$this->response->status(400);

				// Set a default error
				$this->body = array('error' => 'Something went wrong');
			}
			else {
				$this->body = array('error' => $e->getMessage());
			}
		}

		// Run the after
		$this->after();

		// Return the response we have now
		return $this->response;
	}

	/**
	 * Set the cache-control header, so the response will not be cached.
	 */
	public function after()
	{
		// Set headers to not cache anything
		$this->response->headers('cache-control', 'no-cache, no-store, max-age=0, must-revalidate');

		// Set the content-type header
		$this->response->headers('content-type', 'application/json');

		// Set and encode the body data
		$this->response->body(json_encode($this->body));
	}

} // End Kohana_Controller_Vertebro
