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

	public function testConnection()
	{
		$this->assertEquals(true, $this->getresponse->ping());
		$this->getresponse = new GetResponseApi('APIKEY');
		$this->assertEquals(false, $this->getresponse->ping());
	}

	public function testGetAccounts()
	{
		$responseObject = $this->getresponse->getAccounts();
		$this->assertInstanceOf('GetResponseApi', $responseObject);
		$this->assertSame($this->getresponse, $responseObject);

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

}
