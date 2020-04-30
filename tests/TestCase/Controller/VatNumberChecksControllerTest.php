<?php
namespace VatNumberCheck\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use VatNumberCheck\Utility\Model\VatNumberCheck;

/**
 * VatNumberChecksController Test Case.
 *
 * @property \VatNumberCheck\Controller\VatNumberChecksController $VatNumberChecks
 */
class VatNumberChecksControllerTest extends TestCase
{
    use IntegrationTestTrait;

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
    public function setUp()
    {
        parent::setUp();
        $this->useHttpServer(true);
    }

    /**
     * The URL to call in the tests of `check`
     *
     * @var string
     */
    const CHECK_VAT_URL = '/vat_number_check/vat_number_checks/check.json';

    /**
     * Tests `/vat_number_check/vat_number_checks/check.json`.
     *
     *  Post request, correct VAT.
     *
     * @return void
     */
    public function testCheckPostCorrectVat()
    {
        $data = ['vatNumber' => 'NL820345672B01'];
        $this->post(static::CHECK_VAT_URL, $data);

        $expected = array_merge($data, ['status' => 'ok']);

        $this->assertResponseOk();
        $this->assertEquals($expected, json_decode($this->_response->getBody(), true));
        $this->assertResponseCode(200);
    }

    /**
     * Tests `/vat_number_check/vat_number_checks/check.json`.
     *
     *  Get request.
     *
     * @return void
     */
    public function testCheckGet()
    {
        $data = ['vatNumber' => ''];
        $this->get(static::CHECK_VAT_URL, $data);

        $expected = array_merge($data, ['status' => 'failure']);

        $this->assertResponseOk();
        $this->assertEquals($expected, json_decode($this->_response->getBody(), true));
        $this->assertResponseCode(200);
    }

    /**
     * Tests `/vat_number_check/vat_number_checks/check.json`.
     *
     *  Post request, incorrect VAT.
     *
     * @return void
     */
    public function testCheckPostIncorrectVat()
    {
        $data = ['vatNumber' => 'NL820345672B02'];
        $this->post(static::CHECK_VAT_URL, $data);

        $expected = array_merge($data, ['status' => 'failure']);

        $this->assertResponseOk();
        $this->assertEquals($expected, json_decode($this->_response->getBody(), true));
        $this->assertResponseCode(200);
    }

    /**
     * Tests `/vat_number_check/vat_number_checks/check.json`.
     *
     *  Post request, correct VAT, timeout.
     *
     * @return void
     */
    public function testCheckPostCorrectVatTimeout()
    {
        // Ugly, but I don't see any other way
        $this->configRequest(['environment' => ['USE_MOCKED_GET_URL_CONTENT' => true]]);

        $data = ['vatNumber' => 'NL820345672B01'];
        $this->post(static::CHECK_VAT_URL, $data);

        $expected = array_merge($data, ['status' => 'failure']);

        $this->assertResponseFailure();
        $this->assertEquals($expected, json_decode($this->_response->getBody(), true));
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
