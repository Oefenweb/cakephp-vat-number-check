<?php
App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('VatNumberCheckHelper', 'VatNumberCheck.View/Helper');

/**
 * VatNumberCheckHelper Test Case
 *
 * @property VatNumberCheck.VatNumberCheckHelper $VatNumberCheck
 */
class VatNumberCheckHelperTest extends CakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$View = new View();
		$this->VatNumberCheck = new VatNumberCheckHelper($View);
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
 * testInput method
 *
 * @return void
 */
	public function testInput() {
		$fieldName = 'Foo.bar';
		$result = $this->VatNumberCheck->input($fieldName);

		// Test name and id properties
		$this->assertPattern('/name\=\"data\[Foo\]\[bar\]\"/', $result);
		$this->assertPattern('/id\=\"FooBar\"/', $result);

		// Test class property -> only + append
		$options = array();
		$result = $this->VatNumberCheck->input($fieldName, $options);
		$this->assertPattern('/class\=\"vat-number-check\"/', $result);

		$options = array('class' => 'foo-bar');
		$result = $this->VatNumberCheck->input($fieldName, $options);
		$this->assertPattern('/class\=\"foo-bar vat-number-check\"/', $result);

		// Test input type
		$options = array('type' => 'radio');
		$result = $this->VatNumberCheck->input($fieldName, $options);
		$this->assertPattern('/type\=\"text\"/', $result);
	}

}
