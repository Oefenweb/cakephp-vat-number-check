<?php
namespace VatNumberCheck\Controller;

use Cake\Event\Event;
use Cake\Network\Exception\InternalErrorException;
use VatNumberCheck\Utility\Model\VatNumberCheck;

/**
 * VatNumberChecks Controller
 *
 * @property \TextRenderers\Utility\Model\VatNumberCheck $VatNumberCheck
 */
class VatNumberChecksController extends AppController
{
    /**
     * An array of names of components to load.
     *
     * @var array
     */
    public $components = ['RequestHandler'];

    /**
     * Initializes some handy variables.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->VatNumberCheck = new VatNumberCheck();
    }

    /**
     * Before action logic.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return \Cake\Network\Response|null|void
     * @throws \Cake\Network\Exception\BadRequestException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        if (in_array($this->request->action, ['check'], true)) {
            // Disable Security, Csrf component checks
            if ($this->components()->has('Security')) {
                $this->components()->unload('Security');
            }
            if ($this->components()->has('Csrf')) {
                $this->components()->unload('Csrf');
            }
            // Allow action without authentication
            if ($this->components()->has('Auth')) {
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
