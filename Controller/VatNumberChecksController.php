<?php
App::uses('VatNumberChecksAppController', 'VatNumberCheck.Controller');

/**
 * VatNumberChecks Controller
 *
 * @property VatNumberCheck.VatNumberCheck $VatNumberCheck
 */
class VatNumberChecksController extends VatNumberChecksAppController {

/**
 * Constructor
 *
 * @param CakeRequest $request Request instance.
 * @param CakeResponse $response Response instance.
 */
	public function __construct($request = null, $response = null) {
		parent::__construct($request, $response);
		if (!$this->Components->attached('RequestHandler')
		) {
			$this->RequestHandler = $this->Components->load('RequestHandler');
		}
	}

/**
 * Called before the controller action.
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();

		if (in_array($this->request->action, ['check'], true)) {
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
 * Checks a given vat number (from POST data).
 *
 * @return void
 */
	public function check() {
		$vatNumber = $this->request->data('vatNumber');
		$vatNumber = $this->VatNumberCheck->normalize($vatNumber);

		$jsonData = array_merge(compact('vatNumber'), ['status' => 'failure']);
		try {
			$vatNumberValid = $this->VatNumberCheck->check($vatNumber);
			if ($vatNumberValid) {
				$jsonData = array_merge(compact('vatNumber'), ['status' => 'ok']);
			}
		} catch (InternalErrorException $e) {
			$this->response->statusCode(503);
		}

		$this->set(compact('jsonData'));
		$this->set('_serialize', 'jsonData');
	}

}
