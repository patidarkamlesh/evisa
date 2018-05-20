<?php

namespace Drupal\evisa\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class EvisaConfigForm extends ConfigFormBase {

    /**
     * Evisa COnfig Form ID
     * @return string
     */
    public function getFormId() {
        return 'evisa_config';
    }

    /**
     * Evisa Editable Configuration form 
     */
    public function getEditableConfigNames() {
        return [
            'evisa.adminsettings',
        ];
    }
    /**
     * Configuration Form
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        
        $config = $this->config('evisa.adminsettings');
        $form['limit'] = [
            '#type' => 'textfield',
            '#title' => t('Item show per page'),
            '#required' => TRUE,
            '#default_value' => $config->get('limit')
        ];
        return parent::buildForm($form, $form_state);
    }
    /**
     * Validate COnfiguration Form
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        parent::validateForm($form, $form_state);
    }
    /**
     * Submit Configuration Form
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        
        //Save COnfiguration data
        $config = $this->config('evisa.adminsettings');
        $config->set('limit', $form_state->getValue('limit'));
        $config->save(); 
        return parent::submitForm($form, $form_state);
    }

}
