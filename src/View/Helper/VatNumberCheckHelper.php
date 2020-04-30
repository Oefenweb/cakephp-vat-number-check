<?php
namespace VatNumberCheck\View\Helper;

use Cake\View\Helper;

/**
 * VatNumberCheck Helper.
 *
 * @property \Cake\View\Helper\FormHelper $Form
 * @property \Cake\View\Helper\HtmlHelper $Html
 * @property \Cake\View\Helper\UrlHelper $Url
 */
class VatNumberCheckHelper extends Helper
{

    /**
     * An array of names of helpers to load.
     *
     * @var array<int,string>
     */
    public $helpers = ['Html', 'Form', 'Url'];

    /**
     * The number of times this helper is called.
     *
     * @var int
     */
    protected $helperCount = 0;

    /**
     * The css class name to trigger `check` logic.
     *
     * @var string
     */
    protected $inputClass = 'vat-number-check';

    /**
     * Generates a vat number check form field.
     *
     *  See `FormHelper::input`.
     *
     * @param string $fieldName This should be `Modelname.fieldname`
     * @param array<string,mixed> $options Each type of input takes different options
     * @return string Html output for a form field
     */
    public function input(string $fieldName, array $options = []): string
    {
        $this->helperCount += 1;
        if ($this->helperCount === 1) {
            $this->_addJs();
        }

        $options = array_merge($options, ['type' => 'text']);

        $class = $this->inputClass;
        if (empty($options['class'])) {
            $options['class'] = $class;
        } else {
            $options['class'] = sprintf('%s %s', $options['class'], $class);
        }

        return $this->Form->control($fieldName, $options);
    }

    /**
     * Adds the needed javascript to the DOM (once).
     *
     * @return void
     */
    protected function _addJs()
    {
        $checkUrl = $this->Url->build([
            'plugin' => 'VatNumberCheck',
            'controller' => 'VatNumberChecks',
            'action' => 'check',
            '_ext' => 'json',
        ]);
        $checkImages = [
            'ok' => $this->Url->build('/vat_number_check/img/ok.png'),
            'failure' => $this->Url->build('/vat_number_check/img/failure.png'),
            'serviceUnavailable' => $this->Url->build('/vat_number_check/img/service-unavailable.png'),
        ];

        $script = "
            /* jshint jquery:true */

            jQuery.noConflict();
            (function($) {
                $(function () {
                    var options = {
                        elementSelector: '" . sprintf('input.%s', $this->inputClass) . "',
                        checkUrl: '" . $checkUrl . "',
                        checkImages: " . json_encode($checkImages) . ",
                    };
                    var vatNumberCheck = new VatNumberCheck(options);
                });
            })(jQuery);
        ";

        $this->Html->script([
            'VatNumberCheck.jquery.min',
            'VatNumberCheck.klass.min',
            'VatNumberCheck.vat_number_check',
        ], ['inline' => false, 'once' => true]);
        $this->Html->scriptBlock($script, ['inline' => false]);
    }
}
