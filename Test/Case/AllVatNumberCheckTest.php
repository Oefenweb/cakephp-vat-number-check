<?php
/**
 * All VatNumberCheck plugin tests
 */
class AllVatNumberCheckTest extends CakeTestCase {

/**
 * Suite define the tests for this plugin
 *
 * @return void
 */
	public static function suite() {
		$suite = new CakeTestSuite('All VatNumberCheck test');

		$path = CakePlugin::path('VatNumberCheck') . 'Test' . DS . 'Case' . DS;
		$suite->addTestDirectoryRecursive($path);

		return $suite;
	}

}
