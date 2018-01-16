<?php
namespace VatNumberCheck\Test\TestCase\Utility\Model;

use Cake\Network\Exception\InternalErrorException;
use Cake\TestSuite\TestCase;
use VatNumberCheck\Utility\Model\VatNumberCheck;
use InvalidArgumentException;

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
     * @todo Use data provider
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

        $this->assertEquals('NL', $actual['memberStateCode']);
        $this->assertEquals('820345672B01', $actual['number']);
        $this->assertTrue(count($actual) > 2);

        // Missing vat

        $vatNumber = 'NL';
        $actual = $this->VatNumberCheck->toQueryString($vatNumber);

        $this->assertEquals('NL', $actual['memberStateCode']);
        $this->assertEquals('', $actual['number']);
        $this->assertTrue(count($actual) > 2);
    }

    /**
     * Tests `getUrlContent`.
     *
     *  Correct
     *
     * @return void
     */
    public function testGetUrlContentCorrect() {
        // Correct
        $actual = $this->VatNumberCheck->getUrlContent(VatNumberCheck::CHECK_URL, []);
        $this->assertTextContains('<body>', $actual);
    }

    /**
     * Tests `getUrlContent`.
     *
     *  Missing url
     *
     * @return void
     */
    public function testGetUrlContentMissingUrl() {
        $this->expectException(InvalidArgumentException::class);

        $actual = $this->VatNumberCheck->getUrlContent('', []);
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
     *  Simulate a timeout of `getUrlContent`
     *
     * @return void
     */
    public function testCheckException() {
        $this->expectException(InternalErrorException::class);

        $VatNumberCheck = $this->getMockBuilder(VatNumberCheck::class)->setMethods(['getUrlContent'])->getMock();
        $VatNumberCheck->expects($this->any())->method('getUrlContent')->willReturn(false);

        $vatNumber = 'NL820345672B01';
        $VatNumberCheck->check($vatNumber);
    }
}
