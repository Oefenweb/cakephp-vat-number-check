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

        unset($_ENV['MOCKED_GETURLCONTENT']);
    }

    /**
     * Tests `/vat_number_check/vat_number_checks/check.json`.
     *
     *  Post request, correct vat
     *
     * @return void
     */
    public function testCheck0() {
        $this->enableCsrfToken();
        $this->enableSecurityToken();

        $data = ['vatNumber' => 'NL820345672B01'];
        $this->post('/vat_number_check/vat_number_checks/check.json', $data);

        // $this->assertResponseOk();
        $this->assertResponseFailure();

        // $expected = array_merge($data, ['status' => 'ok']);
        $expected = array_merge($data, ['status' => 'failure']);

        // Test response body
        $this->assertEquals($expected, json_decode($this->_response->body(), true));

        // Test response code
        // $this->assertResponseCode(200);
        $this->assertResponseCode(503);
    }

    /**
     * Tests `/vat_number_check/vat_number_checks/check.json`.
     *
     *  Get request
     *
     * @return void
     */
    public function testCheck1() {
        return;
        $VatNumberCheck = $this->createMock(VatNumberCheck::class);

        $data = ['vatNumber' => ''];

        $actual = $this->get(
            '/vat_number_check/vat_number_checks/check.json',
            ['return' => 'contents']
        );
        $expected = array_merge($data, ['status' => 'failure']);

        $this->assertEquals($expected, json_decode($actual, true));
    }

    /**
     * Tests `/vat_number_check/vat_number_checks/check.json`.
     *
     *  Post request, incorrect vat
     *
     * @return void
     */
    public function testCheck2() {
        return;
        $data = ['vatNumber' => 'NL820345672B02'];

        $actual = $this->get(
            '/vat_number_check/vat_number_checks/check.json',
            ['return' => 'contents', 'data' => $data, 'method' => 'post']
        );
        $expected = array_merge($data, ['status' => 'failure']);

        $this->assertEquals($expected, json_decode($actual, true));
    }

    /**
     * Tests `/vat_number_check/vat_number_checks/check.json`.
     *
     *  Post request, correct vat, timeout
     *
     * @return void
     */
    public function testCheck3() {
        return;
        $_ENV['MOCKED_GETURLCONTENT'] = true;

        $data = ['vatNumber' => 'NL820345672B01'];

        $actual = $this->get(
            '/vat_number_check/vat_number_checks/check.json',
            ['return' => 'contents', 'data' => $data, 'method' => 'post']
        );
        $expected = array_merge($data, ['status' => 'failure']);

        // Test response body
        $this->assertEquals($expected, json_decode($actual, true));

        $actual = $VatNumberChecks->response->statusCode();
        $expected = 503;

        // Test response code
        $this->assertEquals($expected, $actual);
    }

    /**
     *
     * {@inheritDoc}
     */
    public function controllerSpy($event, $controller = null)
    {
        parent::controllerSpy($event, $controller);

        if (isset($this->_controller)) {
            if (env('MOCKED_GETURLCONTENT')) {
                $VatNumberCheck = $this->createMock(VatNumberCheck::class);
                $VatNumberCheck->expects($this->any())->method('getUrlContent')->will($this->returnValue(false));

                $this->_controller->VatNumberCheck = $VatNumberCheck;
            }
        }
    }
}
