<?php

class GetResponseApi
{
	private $apiKey;
	private $apiUrl = 'https://api.getresponse.com/v3';
	private $domain = null;

	private $httpStatus = null;
	
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
		$this->accounts();
		return ($this->httpStatus === 200) ? true : false;
			
	}

	public function accounts()
	{
		$accounts = $this->call('accounts');

	}

	private function call(string $method)
	{
		if (empty($method)) {
			$this->httpStatus = 400;
            return [
                'httpStatus' => '400',
                'code' => '1010',
                'codeDescription' => 'Error in external resources',
                'message' => 'Invalid api method'
            ];
        }

        return 200;
	}
}