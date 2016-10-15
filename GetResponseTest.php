<?php

require('GetResponseApi.php');

/** File with defined const API_KEY  */
require('config.php');

use \Math\Matrix;

class GetResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
    * configured object
    *
    * @var GetResponseApi
    */
    private $getresponse;

    public function setUp()
    {
        $this->getresponse = new GetResponseApi(API_KEY);
    }
    public function testEmptyHttpStatus()
    {
        $this->assertFalse(false, $this->getresponse->getHttpStatus());
    }
    public function testPing()
    {
        $this->assertEquals(true, $this->getresponse->ping());
        $this->getresponse = new GetResponseApi('APIKEY');
        $this->assertEquals(false, $this->getresponse->ping());
    }

    public function testReturnResponse()
    {
        $this->getresponse->getAccounts();

        $object = $this->getresponse->returnResponse();
        $this->assertInstanceOf('StdClass', $object);
        $this->assertTrue(isset($object->accountId));

        $array = $this->getresponse->returnResponse(false);
        $this->assertTrue(is_array($array));
        $this->assertFalse(empty($array));
        $this->assertTrue(isset($array['accountId']));

        $this->assertEquals($array['accountId'], $object->accountId);
    }

    /**
     * Doesn't work ?? UNPARSABLE JSON BODY?
     *
    {
        $accountValues = [
            'firstName'=> 'John',
            'lastName'=> 'Smith',
            'companyName'=> 'CELAR',
        ];
        $this->getresponse->addAccount($accountValues);
        $object = $this->getresponse->returnResponse();
        var_dump($object);
        $this->assertEquals(200, $this->getresponse->getHttpStatus());

    }*/

    public function testGetAccounts()
    {
        $responseObject = $this->getresponse->getAccounts();
        $this->assertEquals(200, $this->getresponse->getHttpStatus());
    }

    /**
     * Tests Adding Campaign. Cannot remove campaign by API, Can you do this only
     * on GetResponse Website. In Account Settings. Uncoment only when sure.
     *
    public function testAddCampaign()
    {
        $params = [
            'name' => 'test_campaig_test',
            'languageCode' => 'PL',
            'profile' => [
                'description' => 'Short Campaign description',
                'title' => 'Campaign Title'
            ]
        ];

        $this->getresponse->addCampaign($params);
        $this->assertEquals(201, $this->getresponse->getHttpStatus());

    }*/
    public function testGetCampaigns()
    {
        $this->getresponse->getCampaigns();
        $this->assertEquals(200, $this->getresponse->getHttpStatus());
        $this->assertGreaterThan(0, count($this->getresponse->returnResponse()));
    }
}
