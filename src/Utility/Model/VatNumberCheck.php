<?php
namespace VatNumberCheck\Utility\Model;

use Cake\Core\Configure;
use Cake\Http\Client;
use Cake\Network\Exception\InternalErrorException;
use VatNumberCheck\Utility\Model\App;

/**
 * VatNumberCheck Model
 *
 */
class VatNumberCheck extends App
{
    /**
     * Url to check vat numbers.
     *
     */
	const CHECK_URL = 'https://ec.europa.eu/taxation_customs/vies/viesquer.do';

    /**
     * Normalizes a VAT number.
     *
     * @param string $vatNumber A VAT number
     * @return string A (normalized) VAT number
     */
	public function normalize($vatNumber) {
		$vatNumber = strtoupper($vatNumber);
		$vatNumber = preg_replace('/[^A-Z0-9]/', '', $vatNumber);

		return $vatNumber;
	}

    /**
     * Splits a VAT number into querystring parameters.
     *
     * @param string $vatNumber A VAT number
     * @return array Querystring parameters
     */
	public function toQueryString($vatNumber) {
		$ms = (string)substr($vatNumber, 0, 2);
		$iso = $ms;
		$vat = (string)substr($vatNumber, 2);

		return compact('ms', 'iso', 'vat');
	}

    /**
     * Constructs an url for a given vat number.
     *
     * @param string $vatNumber A VAT number
     * @return string An url
     */
	public function constructUrl($vatNumber) {
		$queryString = $this->toQueryString($vatNumber);
		$queryString = http_build_query($queryString);

		return static::CHECK_URL . '?' . $queryString;
	}

    /**
     * Downloads a given url.
     *
     * @param string $url An url
     * @return mixed Request body on success (string) otherwise false (boolean)
     */
	public function getUrlContent($url) {
		$config = (array)Configure::read('VatNumberCheck.socketConfig');
		$HttpSocket = new Client($config);

		try {
			$response = $HttpSocket->get($url);

			if ($response->isOk()) {
				return $response->body;
			}
		} catch (Exception $e) {
		}

		return false;
	}

    /**
     * Checks a given VAT number.
     *
     * @param string $vatNumber A VAT number
     * @return bool Valid or not
     * @throws \Cake\Network\Exception\InternalErrorException
     */
	public function check($vatNumber) {
		$url = $this->constructUrl($vatNumber);
		$urlContent = $this->getUrlContent($url);

		if ($urlContent) {
			return (strpos($urlContent, 'Yes, valid VAT number') !== false);
		}

		throw new InternalErrorException(__d('vat_number_check', 'Service unavailable'));
	}
}
