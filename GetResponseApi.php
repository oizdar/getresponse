<?php

class GetResponseApi
{
	/**
	 * GetResponse autentication parameters
	 * @var  string $apiKey
	 * @var  string $apiUrl
	 * @var  string $domain 	customer domain (enterprise account users)
	 */
	private $apiKey;
	private $apiUrl = 'https://api.getresponse.com/v3';
	private $domain = null;

	/**
	 * Http Status code last request
	 * @var int
	 */
	private $httpStatus;

	/**
	 * Array of JSON encoded responses
 	 * @var array of strings
 	 */
	private $response;

/**
 * @param string      $apiKey GetResponse acoount Api-Key
 * @param string|null $apiUrl Change only for Enterprise accounts otherwise use default
 * @param string|null $domain Enterprise accounts only
 */
	public function __construct(
		string $apiKey,
		string $apiUrl = null,
		string $domain = null
	) {
		$this->apiKey = $apiKey;

		if (!empty($apiUrl)) {
			$this->apiUrl = $apiUrl;
		}
		if (!empty($domain)) {
			$this->domain = $domain;
		}
	}

	public function ping()
	{
		$this->getAccounts();
		return ($this->httpStatus === 200) ? true : false;

	}

	public function getAccounts()
	{
		$this->call('accounts');
		return $this;
	}

	/**
	 * cURL Call
	 * Sets object values with received data (::httpStatus, ::response)
	 *
	 * @param  string $method     GetResponse method
	 * @param  string $httpMethod
	 * @param  array  $params     GetResponse params
	 * @return void
	 */
	private function call(
		string $method,
		string $httpMethod = 'GET',
		array $params = []
	) {
		if (empty($method)) {
			$this->httpStatus = 400;
            return [
                'httpStatus' => '400',
                'code' => '1010',
                'codeDescription' => 'Error in external resources',
                'message' => 'Invalid api method'
            ];
        }

        $url = $this->apiUrl . '/' . $method;
		$options = [
			CURLOPT_URL => $url,
			CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => ['X-Auth-Token: api-key ' . $this->apiKey, 'Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,

		];

		if ($httpMethod === 'POST') {
            $options[CURLOPT_POST] = 1;
            $options[CURLOPT_POSTFIELDS] = $params;
        } else if ($httpMethod === 'DELETE') {
            $options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
        }

		$resource = curl_init();
		curl_setopt_array($resource, $options);

		$response = curl_exec($resource);
		$this->httpStatus = curl_getinfo($resource, CURLINFO_HTTP_CODE);

		curl_close($resource);

		$this->response = $response;
	}

	/**
	 * Returns last call Response
	 *
	 * @param  boolean $object  	For true returns splObject otherwise array
	 * @return stdClass|array 	Response as array or object
	 */
	public function returnResponse(bool $object = true)
	{
		if($object) {
			$response = (isset($this->response))
				? json_decode($this->response)
				: new StdClass();
		} else {
			$response = (isset($this->response))
				? json_decode($this->response, true)
				: array();
		}
		return $response;
	}
}
