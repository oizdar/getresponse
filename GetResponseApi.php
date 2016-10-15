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
    }

    /**
     * Adding Account
     *
     * @param array $params Map of getResponse parameters: Possible options:
     *    firstName    string  First name, length 2-64
     *    lastName    string  Last name, length 2-64
     *    phone   string  Phone, length 2-32
     *    companyName string  Company name, length 0-64
     *    street  string  Street, length 2-64
     *    state   string  State, length 0-40
     *    city    string  City
     *    zipCode string  zip code, length 2-9
     *    timeFormat  string  Time format (Fixed values to choose from: 12h, 24h)
     *    numberOfEmployees   string  Number of employees in Your company
     *    industryTag string  id of industry tag
     *
     * @todo  Check and repair
     */
    public function addAccount(array $params)
    {
        //$this->call('accounts', 'POST', $params);
    }

    public function addCampaign($params)
    {
        $this->call('campaigns', 'POST', $params);
    }

    public function getCampaigns()
    {
        $this->call('campaigns');
    }


    /**
     * cURL Call
     * Sets object values with received data (::httpStatus, ::response)
     *
     * @param  string $method     GetResponse method
     * @param  string $httpMethod
     * @param  array  $params     GetResponse params
     * @return void
     *
     * @todo  set Params to GET query
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
            $options[CURLOPT_POST] = true;
            $json = json_encode($params);
            var_dump($json);
            $options[CURLOPT_POSTFIELDS] = $json;
        } elseif ($httpMethod === 'GET') {
            // set query with params
        } elseif ($httpMethod === 'DELETE') {
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
     * @return integer|boolean
     */
    public function getHttpStatus()
    {
        return (isset($this->httpStatus)) ? $this->httpStatus : false;
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
