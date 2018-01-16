<?php
namespace VatNumberCheck\Test\TestCase\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\TestSuite\IntegrationTestCase;
use VatNumberCheck\Utility\Model\VatNumberCheck;

/**
 * VatNumberChecksController Test Case
 *
 * @property \VatNumberCheck\Controller\VatNumberChecksController $VatNumberChecks
 */
class VatNumberChecksControllerTest extends IntegrationTestCase {

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

        $this->enableCsrfToken();
        $this->enableSecurityToken();
    }

    /**
     * Tests `/vat_number_check/vat_number_checks/check.json`.
     *
     *  Post request, correct vat
     *
     * @return void
     */
    public function testCheck0() {
        $data = ['vatNumber' => 'NL820345672B01'];
        $this->post('/vat_number_check/vat_number_checks/check.json', $data);

        $this->assertResponseOk();

        $expected = array_merge($data, ['status' => 'ok']);

        // Test response body
        $this->assertEquals($expected, json_decode($this->_response->body(), true));

        // Test response code
        $this->assertResponseCode(200);
    }

    /**
     * Tests `/vat_number_check/vat_number_checks/check.json`.
     *
     *  Get request
     *
     * @return void
     */
    public function testCheck1() {
        $data = ['vatNumber' => ''];
        $this->get('/vat_number_check/vat_number_checks/check.json', $data);

        $this->assertResponseOk();

        $expected = array_merge($data, ['status' => 'failure']);

        $this->assertEquals($expected, json_decode($this->_response->body(), true));
    }

    /**
     * Tests `/vat_number_check/vat_number_checks/check.json`.
     *
     *  Post request, incorrect vat
     *
     * @return void
     */
    public function testCheck2() {
        $data = ['vatNumber' => 'NL820345672B02'];
        $this->post('/vat_number_check/vat_number_checks/check.json', $data);

        $this->assertResponseOk();

        // $expected = array_merge($data, ['status' => 'ok']);
        $expected = array_merge($data, ['status' => 'failure']);

        // Test response body
        $this->assertEquals($expected, json_decode($this->_response->body(), true));
    }

    /**
     * Tests `/vat_number_check/vat_number_checks/check.json`.
     *
     *  Post request, correct vat, timeout
     *
     * @return void
     */
    public function testCheck3() {
        $this->configRequest(['environment' => ['USE_MOCKED_GET_URL_CONTENT' => true]]);

        $data = ['vatNumber' => 'NL820345672B01'];
        $this->post('/vat_number_check/vat_number_checks/check.json', $data);

        $this->assertResponseFailure();

        $expected = array_merge($data, ['status' => 'failure']);

        // Test response body
        $this->assertEquals($expected, json_decode($this->_response->body(), true));

        // Test response code
        $this->assertResponseCode(503);
    }

    /**
     *
     * {@inheritDoc}
     */
    public function controllerSpy($event, $controller = null)
    {
        parent::controllerSpy($event, $controller);

        if (isset($this->_controller)) {
            if ($this->_controller->request->env('USE_MOCKED_GET_URL_CONTENT')) {
                $VatNumberCheck = $this->getMockBuilder(VatNumberCheck::class)->setMethods(['getUrlContent'])->getMock();
                $VatNumberCheck->expects($this->any())->method('getUrlContent')->willReturn(false);

                $this->_controller->VatNumberCheck = $VatNumberCheck;
            }
        }
    }
}
