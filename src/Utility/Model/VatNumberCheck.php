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
	const CHECK_URL = 'http://ec.europa.eu/taxation_customs/vies/vatResponse.html';

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
     * Splits a VAT number into query string (data) parameters.
     *
     * @param string $vatNumber A VAT number
     * @return array Query string parameters
     */
	public function toQueryString($vatNumber) {
        $memberStateCode = (string)substr($vatNumber, 0, 2);
		$number = (string)substr($vatNumber, 2);
        $action = 'check';
        $check = 'Verify';

		return compact(
            'memberStateCode', 'number',
            'traderName', 'traderStreet', 'traderPostalCode', 'traderCity',
            'requesterMemberStateCode', 'requesterNumber',
            'action', 'check'
        );
	}

    /**
     * Downloads a given url.
     *
     * @param string $url An url
     * @param string $data POST data
     * @return bool|string Request body on success (string) otherwise false (boolean)
     */
	public function getUrlContent(string $url, array $data) {
		$config = (array)Configure::read('VatNumberCheck.socketConfig');
		$HttpSocket = new Client($config);

		try {
			$response = $HttpSocket->post($url, $data);

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
		$url = static::CHECK_URL;
		$data = $this->toQueryString($vatNumber);

        $urlContent = $this->getUrlContent($url, $data);
		if ($urlContent) {
			return (strpos($urlContent, 'Yes, valid VAT number') !== false);
		}

		throw new InternalErrorException(__d('vat_number_check', 'Service unavailable'));
	}
}
