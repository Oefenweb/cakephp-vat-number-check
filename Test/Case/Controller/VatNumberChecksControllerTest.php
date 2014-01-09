<?php
App::uses('VatNumberChecksController', 'VatNumberCheck.Controller');

/**
 * VatNumberChecksController Test Case
 *
 * @property VatNumberCheck.VatNumberChecksController $VatNumberChecks
 */
class VatNumberChecksControllerTest extends ControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array();

/**
 * testCheck method
 *
 * @return void
 */
	public function testCheck() {
		// Post request, correct vat

		$VatNumberChecks = $this->generate('VatNumberCheck.VatNumberChecks');
		$VatNumberChecks->VatNumberCheck = ClassRegistry::init('VatNumberCheck.VatNumberCheck');

		$data = array('vatNumber' => 'NL820345672B01');

		$result = $this->testAction(
			'/vat_number_check/vat_number_checks/check.json',
			array('return' => 'contents', 'data' => $data, 'method' => 'post')
		);
		$expected = json_encode(array_merge($data, array('status' => 'ok')));

		// Test response body
		$this->assertIdentical($expected, $result);

		$result = $VatNumberChecks->response->statusCode();
		$expected = 200;

		// Test response code
		$this->assertIdentical($expected, $result);

		// Get request

		$VatNumberChecks = $this->generate('VatNumberCheck.VatNumberChecks');
		$VatNumberChecks->VatNumberCheck = ClassRegistry::init('VatNumberCheck.VatNumberCheck');

		$data = array('vatNumber' => '');

		$result = $this->testAction(
			'/vat_number_check/vat_number_checks/check.json',
			array('return' => 'contents')
		);
		$expected = json_encode(array_merge($data, array('status' => 'failure')));

		$this->assertIdentical($expected, $result);

		// Post request, incorrect vat

		$VatNumberChecks = $this->generate('VatNumberCheck.VatNumberChecks');
		$VatNumberChecks->VatNumberCheck = ClassRegistry::init('VatNumberCheck.VatNumberCheck');

		$data = array('vatNumber' => 'NL820345672B02');

		$result = $this->testAction(
			'/vat_number_check/vat_number_checks/check.json',
			array('return' => 'contents', 'data' => $data, 'method' => 'post')
		);
		$expected = json_encode(array_merge($data, array('status' => 'failure')));

		$this->assertIdentical($expected, $result);

		// Post request, correct vat, timeout

		$VatNumberChecks = $this->generate('VatNumberCheck.VatNumberChecks', array(
			'models' => array(
				'VatNumberCheck.VatNumberCheck' => array('getUrlContent')
			)
		));
		$VatNumberChecks->VatNumberCheck->expects($this->any())->method('getUrlContent')->will($this->returnValue(false));

		$data = array('vatNumber' => 'NL820345672B01');

		$result = $this->testAction(
			'/vat_number_check/vat_number_checks/check.json',
			array('return' => 'contents', 'data' => $data, 'method' => 'post')
		);
		$expected = json_encode(array_merge($data, array('status' => 'failure')));

		// Test response body
		$this->assertIdentical($expected, $result);

		$result = $VatNumberChecks->response->statusCode();
		$expected = 503;

		// Test response code
		$this->assertIdentical($expected, $result);
	}

}
