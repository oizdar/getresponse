<?php

require('GetResponseApi.php');

/** File with defined const API_KEY  */
require('cofig.php');

use PHPUnit\Framework\TestCase;
use \Math\Matrix;

class GetResponseTest extends TestCase
{
	
	/**
	 * configured object
	 * 
	 * @var GetResponseApi
	 */
	private $getresponse;

	public function setUp()
	{
		$this->getresponse new GetResponseApi(API_KEY);
	}

	public function testConnection()
	{
		$this->assertEquals(true, $this->getresponse->ping());
		

	}
}