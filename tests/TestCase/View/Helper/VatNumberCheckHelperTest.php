<?php
namespace VatNumberCheck\Test\TestCase\View\Helper;

use Cake\TestSuite\TestCase;
use Cake\View\View;
use VatNumberCheck\View\Helper\VatNumberCheckHelper;

/**
 * VatNumberCheckHelper Test Case.
 *
 * @property \VatNumberCheck\Test\TestCase\View\Helper $VatNumberCheck
 */
class VatNumberCheckHelperTest extends TestCase
{

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $View = new View();
        $this->VatNumberCheck = new VatNumberCheckHelper($View);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->VatNumberCheck);

        parent::tearDown();
    }

    /**
     * Tests `input`.
     *
     * @return void
     */
    public function testInput()
    {
        $fieldName = 'Foo.bar';
        $actual = $this->VatNumberCheck->input($fieldName);

        // Test name and id properties
        $this->assertTrue(preg_match('/name\=\"Foo\[bar\]"/', $actual) === 1);
        $this->assertTrue(preg_match('/id\=\"foo-bar\"/', $actual) === 1);

        // Test class property -> only + append
        $options = [];
        $actual = $this->VatNumberCheck->input($fieldName, $options);
        $this->assertTrue(preg_match('/class\=\"vat-number-check\"/', $actual) === 1);

        $options = ['class' => 'foo-bar'];
        $actual = $this->VatNumberCheck->input($fieldName, $options);
        $this->assertTrue(preg_match('/class\=\"foo-bar vat-number-check\"/', $actual) === 1);

        // Test input type
        $options = ['type' => 'radio'];
        $actual = $this->VatNumberCheck->input($fieldName, $options);
        $this->assertTrue(preg_match('/type\=\"text\"/', $actual) === 1);
    }
}
