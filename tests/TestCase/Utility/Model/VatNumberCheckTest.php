<?php
namespace VatNumberCheck\Test\TestCase\Utility\Model;

use Cake\Network\Exception\InternalErrorException;
use Cake\TestSuite\TestCase;
use VatNumberCheck\Utility\Model\VatNumberCheck;

/**
 * VatNumberCheck Test Case
 *
 * @property \VatNumberCheck\Utility\Model\VatNumberCheck $VatNumberCheck
 */
class VatNumberCheckTest extends TestCase {

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

        $this->VatNumberCheck = new VatNumberCheck();
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
     * Tests `normalize`.
     *
     * @return void
     */
    public function testNormalize() {
        // Correct

        $vatNumber = 'NL820345672B01';
        $actual = $this->VatNumberCheck->normalize($vatNumber);
        $expected = 'NL820345672B01';

        $this->assertEquals($expected, $actual);

        // To upper case

        $vatNumber = 'NL820345672b01';
        $actual = $this->VatNumberCheck->normalize($vatNumber);
        $expected = 'NL820345672B01';

        $this->assertEquals($expected, $actual);

        // Removal of non-alphanumeric

        $vatNumber = 'NL820345672 B01';
        $actual = $this->VatNumberCheck->normalize($vatNumber);
        $expected = 'NL820345672B01';

        $this->assertEquals($expected, $actual);

        $vatNumber = 'NL820345672!B01';
        $actual = $this->VatNumberCheck->normalize($vatNumber);
        $expected = 'NL820345672B01';

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests `toQueryString`.
     *
     * @return void
     */
    public function testToQueryString() {
        // Correct

        $vatNumber = 'NL820345672B01';
        $actual = $this->VatNumberCheck->toQueryString($vatNumber);
        $expected = ['ms' => 'NL', 'iso' => 'NL', 'vat' => '820345672B01'];

        $this->assertEquals($expected, $actual);

        // Missing vat

        $vatNumber = 'NL';
        $actual = $this->VatNumberCheck->toQueryString($vatNumber);
        $expected = ['ms' => 'NL', 'iso' => 'NL', 'vat' => ''];

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests `constructUrl`.
     *
     * @return void
     */
    public function testConstructUrl() {
        // Correct

        $vatNumber = 'NL820345672B01';
        $actual = $this->VatNumberCheck->constructUrl($vatNumber);
        $expected = sprintf('%s?ms=%s&iso=%s&vat=%s', VatNumberCheck::CHECK_URL, 'NL', 'NL', '820345672B01');

        $this->assertEquals($expected, $actual);

        // Missing vat

        $vatNumber = 'NL';
        $actual = $this->VatNumberCheck->constructUrl($vatNumber);
        $expected = sprintf('%s?ms=%s&iso=%s&vat=%s', VatNumberCheck::CHECK_URL, 'NL', 'NL', '');

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests `getUrlContent`.
     *
     * @return void
     */
    public function testGetUrlContent() {
        // Correct

        $url = sprintf('%s?ms=%s&iso=%s&vat=%s', VatNumberCheck::CHECK_URL, 'NL', 'NL', '820345672B01');
        $actual = $this->VatNumberCheck->getUrlContent($url);

        $this->assertTextContains('<body>', $actual);

        // Missing url

        $url = '';
        $actual = $this->VatNumberCheck->getUrlContent($url);

        $this->assertFalse($actual);
    }

    /**
     * Tests `check`.
     *
     * @return void
     */
    public function testCheck() {
        // Correct

        $vatNumber = 'NL820345672B01';
        $actual = $this->VatNumberCheck->check($vatNumber);

        $this->assertTrue($actual);

        // Incorrect vat

        $vatNumber = 'NL820345672B02';
        $actual = $this->VatNumberCheck->check($vatNumber);

        $this->assertFalse($actual);

        // Empty vat

        $vatNumber = '';
        $actual = $this->VatNumberCheck->check($vatNumber);

        $this->assertFalse($actual);
    }

    /**
     * Tests `check`.
     *
     * @return void
     */
    public function testCheckException() {
        // Simulate a timeout of `getUrlContent`
        $this->expectException(InternalErrorException::class);

        $VatNumberCheck = $this->createMock(VatNumberCheck::class);
        $VatNumberCheck->expects($this->any())->method('getUrlContent')->will($this->returnValue(false));

        $vatNumber = 'NL820345672B01';
        $VatNumberCheck->check($vatNumber);
    }
}
