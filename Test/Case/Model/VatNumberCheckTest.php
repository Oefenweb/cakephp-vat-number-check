<?php
App::uses('VatNumberCheck', 'VatNumberCheck.Model');

/**
 * VatNumberCheck Test Case
 *
 * @property VatNumberCheck.VatNumberCheck $VatNumberCheck
 */
class VatNumberCheckTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$this->VatNumberCheck = ClassRegistry::init('VatNumberCheck.VatNumberCheck');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->VatNumberCheck);

		parent::tearDown();
	}

/**
 * testNormalize method
 *
 * @return void
 */
	public function testNormalize() {
		// Correct

		$vatNumber = 'NL820345672B01';
		$result = $this->VatNumberCheck->normalize($vatNumber);
		$expected = 'NL820345672B01';

		$this->assertIdentical($expected, $result);

		// To upper case

		$vatNumber = 'NL820345672b01';
		$result = $this->VatNumberCheck->normalize($vatNumber);
		$expected = 'NL820345672B01';

		$this->assertIdentical($expected, $result);

		// Removal of non-alphanumeric

		$vatNumber = 'NL820345672 B01';
		$result = $this->VatNumberCheck->normalize($vatNumber);
		$expected = 'NL820345672B01';

		$this->assertIdentical($expected, $result);

		$vatNumber = 'NL820345672!B01';
		$result = $this->VatNumberCheck->normalize($vatNumber);
		$expected = 'NL820345672B01';

		$this->assertIdentical($expected, $result);
	}

/**
 * testToQueryString method
 *
 * @return void
 */
	public function testToQueryString() {
		// Correct

		$vatNumber = 'NL820345672B01';
		$result = $this->VatNumberCheck->toQueryString($vatNumber);
		$expected = ['ms' => 'NL', 'iso' => 'NL', 'vat' => '820345672B01'];

		$this->assertIdentical($expected, $result);

		// Missing vat

		$vatNumber = 'NL';
		$result = $this->VatNumberCheck->toQueryString($vatNumber);
		$expected = ['ms' => 'NL', 'iso' => 'NL', 'vat' => ''];

		$this->assertIdentical($expected, $result);
	}

/**
 * testConstructUrl method
 *
 * @return void
 */
	public function testConstructUrl() {
		// Correct

		$vatNumber = 'NL820345672B01';
		$result = $this->VatNumberCheck->constructUrl($vatNumber);
		$expected = sprintf(
			'http://ec.europa.eu/taxation_customs/vies/viesquer.do?ms=%s&iso=%s&vat=%s', 'NL', 'NL', '820345672B01'
		);

		$this->assertIdentical($expected, $result);

		// Missing vat

		$vatNumber = 'NL';
		$result = $this->VatNumberCheck->constructUrl($vatNumber);
		$expected = sprintf(
			'http://ec.europa.eu/taxation_customs/vies/viesquer.do?ms=%s&iso=%s&vat=%s', 'NL', 'NL', ''
		);

		$this->assertIdentical($expected, $result);
	}

/**
 * testGetUrlContent method
 *
 * @return void
 */
	public function testGetUrlContent() {
		// Correct

		$url = sprintf(
			'http://ec.europa.eu/taxation_customs/vies/viesquer.do?ms=%s&iso=%s&vat=%s', 'NL', 'NL', '820345672B01'
		);
		$result = $this->VatNumberCheck->getUrlContent($url);

		$this->assertTextContains('<body>', $result);

		// Missing url

		$url = '';
		$result = $this->VatNumberCheck->getUrlContent($url);

		$this->assertFalse($result);
	}

/**
 * testCheck method
 *
 * @return void
 */
	public function testCheck() {
		// Correct

		$vatNumber = 'NL820345672B01';
		$result = $this->VatNumberCheck->check($vatNumber);

		$this->assertTrue($result);

		// Incorrect vat

		$vatNumber = 'NL820345672B02';
		$result = $this->VatNumberCheck->check($vatNumber);

		$this->assertFalse($result);

		// Empty vat

		$vatNumber = '';
		$result = $this->VatNumberCheck->check($vatNumber);

		$this->assertFalse($result);
	}

/**
 * testCheckException method
 *
 * @return void
 * @expectedException InternalErrorException
 */
	public function testCheckException() {
		// Simulate a timeout of `VatNumberCheck::getUrlContent`

		$VatNumberCheck = $this->getMockForModel('VatNumberCheck', ['getUrlContent']);
		$VatNumberCheck->expects($this->any())->method('getUrlContent')->will($this->returnValue(false));

		$vatNumber = 'NL820345672B01';
		$VatNumberCheck->check($vatNumber);
	}

}
