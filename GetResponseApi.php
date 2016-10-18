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
     * Http-Status code of last request
     * @var int
     */
    private $httpStatus;

    /**
     * JSON encoded response
     *
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

    public function ping() : bool
    {
        $this->getAccounts();
        return ($this->httpStatus === 200) ? true : false;
    }

    public function getAccounts() : void
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
    public function addAccount(array $params) : void
    {
        //$this->call('accounts', 'POST', $params);
    }

    /**
     * Adds a campaign
     * @see  https://apidocs.getresponse.com/v3/resources/campaigns#campaigns.create
     * @param array $params
     */
    public function addCampaign(array $params) : void
    {
        $this->call('campaigns', 'POST', $params);
    }

    /**
     * Set response as Campaign by Id
     * @param  string $id
     */
    public function getCampaign(string $id) : void
    {
        $this->call('campaigns', 'GET', ['id' => $id]);
    }

    /**
     * Set response as all Campaigns
     */
    public function getCampaigns() : void
    {
        $this->call('campaigns');
    }

    /**
     * Search Campaigns by $params
     * @param  array $params
     * @see  https://apidocs.getresponse.com/v3/resources/campaigns#campaigns.get.all
     * @return
     */
    public function searchCampaigns($params)
    {
        $this->call('campaigns', 'GET', $params);
    }

    /**
     * Get Campaign Contacts
     * Test is checked when you have at least one campaign
     * @param  string $id     ID of selected Campaign
     * @param  array $params  Search criteria and other parameters
     */
    public function getCampaignContacts(string $id, array $params) : void
    {
        $this->call('campaigns/'.$id.'/contacts', 'GET', $params);
    }

    /**
     * Get Campaign Contacts
     * Test is checked when you have at least one campaign
     * @param  string $id     ID of selected Campaign
     * @param  array $params  Search criteria and other parameters
     */
    public function getCampaignBlacklist(string $id, array $params) : void
    {
        $this->call('campaigns/'.$id.'/blacklists', 'GET', $params);
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
    ) : void {
        if (empty($method)) {
            $this->httpStatus = 400;
            $response = [
                'httpStatus' => '400',
                'code' => '1010',
                'codeDescription' => 'Error in external resources',
                'message' => 'Invalid api method'
            ];
        } else {

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
            } else if ($httpMethod === 'DELETE') {
                $options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
            }
            if (isset($params) && !empty($params)) {
                if(isset($params['id'])) {
                    $options[CURLOPT_URL] .= '/' . $params['id'];
                    unset($params['id']);
                }
                    $options[CURLOPT_URL] .= '?' . http_build_query($params);
            }

            $resource = curl_init();
            curl_setopt_array($resource, $options);

            $response = curl_exec($resource);
            $this->httpStatus = curl_getinfo($resource, CURLINFO_HTTP_CODE);

            curl_close($resource);
        }
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
