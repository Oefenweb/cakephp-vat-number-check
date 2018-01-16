<?php
namespace VatNumberCheck\Test\TestCase\Utility\Model;

use Cake\Network\Exception\InternalErrorException;
use Cake\TestSuite\TestCase;
use VatNumberCheck\Utility\Model\VatNumberCheck;
use InvalidArgumentException;

/**
 * VatNumberCheck Test Case.
 *
 * @property \VatNumberCheck\Utility\Model\VatNumberCheck $VatNumberCheck
 */
class VatNumberCheckTest extends TestCase {

    /**
     * Fixtures.
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
     * @dataProvider normalizeProvider
     */
    public function testNormalize($vatNumber, $expected) {
        $actual = $this->VatNumberCheck->normalize($vatNumber);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Data provider for `testNormalize`.
     *
     * @return array
     */
    public function normalizeProvider(): array {
        return [
            // Correct
            ['NL820345672B01', 'NL820345672B01'],
            // To upper case
            ['NL820345672b01', 'NL820345672B01'],
            // Removal of non-alphanumeric
            ['NL820345672 B01', 'NL820345672B01'],
            ['NL820345672!B01', 'NL820345672B01'],
        ];
    }

    /**
     * Tests `toQueryString`.
     *
     * @return void
     * @dataProvider toQueryStringProvider
     */
    public function testToQueryString($vatNumber, $memberStateCode, $number) {
        $actual = $this->VatNumberCheck->toQueryString($vatNumber);

        $this->assertEquals($memberStateCode, $actual['memberStateCode']);
        $this->assertEquals($number, $actual['number']);
        $this->assertTrue(count($actual) > 2);
    }

    /**
     * Data provider for `testToQueryString`.
     *
     * @return array
     */
    public function toQueryStringProvider(): array {
        return [
            // Correct
            ['NL820345672B01', 'NL', '820345672B01'],
            // Mssing VAT
            ['NL', 'NL', ''],
        ];
    }

    /**
     * Tests `getUrlContent`.
     *
     *  Correct.
     *
     * @return void
     */
    public function testGetUrlContentCorrect() {
        $actual = $this->VatNumberCheck->getUrlContent(VatNumberCheck::CHECK_URL, []);
        $this->assertTextContains('<body>', $actual);
    }

    /**
     * Tests `getUrlContent`.
     *
     *  Missing url.
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
     * @dataProvider checkProvider
     */
    public function testCheck($vatNumber, $expected) {
        $actual = $this->VatNumberCheck->check($vatNumber);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Data provider for `testCheck`.
     *
     * @return array
     */
    public function checkProvider(): array {
        return [
            // Correct
            ['NL820345672B01', true],
            // Incorrect VAT
            ['NL820345672B02', false],
            // Empty VAT
            ['', false],
        ];
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
