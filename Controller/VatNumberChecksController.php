<?php
App::uses('VatNumberChecksAppController', 'VatNumberCheck.Controller');

/**
 * VatNumberChecks Controller
 *
 * @property VatNumberCheck.VatNumberCheck $VatNumberCheck
 */
class VatNumberChecksController extends VatNumberChecksAppController {

	public $components = array('RequestHandler');

/**
 *
 * @var boolean
 */
	//public $autoRender = false;

/**
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();

		if (in_array($this->request->action, array('check'), true)) {
			// Disable Security component checks
			if ($this->Components->enabled('Security')) {
				$this->Components->disable('Security');
			}

			// Allow action without authentication
			if ($this->Components->enabled('Auth')) {
				$this->Auth->allow($this->request->action);
			}
		}
	}

/**
 *
 * @return void
 */
	public function check() {
		$vatNumber = $this->request->data('vatNumber');
		$vatNumber = $this->VatNumberCheck->normalize($vatNumber);

		$jsonData = array_merge(compact('vatNumber'), array('status' => 'failure'));
		try {
			$vatNumberValid = $this->VatNumberCheck->check($vatNumber);
			if ($vatNumberValid) {
				$jsonData = array_merge(compact('vatNumber'), array('status' => 'ok'));
			}
		} catch (InternalErrorException $e) {
			$this->response->statusCode(503);
		}

		$this->set(compact('jsonData'));
		$this->set('_serialize', 'jsonData');
	}

}
