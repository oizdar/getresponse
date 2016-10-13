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

	}
}