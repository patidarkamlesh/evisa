<?php

namespace Drupal\hello\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class VisaType extends FormBase {
    
    public function getFormId() {
        return 'visa_type_form';
    }
    
    public function buildForm(array $form, FormStateInterface $form_state) {
        $form['visa_type'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Visa Type'),
            '#description' => $this->t('Enter Visa Type'),
            '#required' => TRUE,
        ];

        $form['actions'] = [
            '#type' => 'actions',
        ];

        // Add a submit button that handles the submission of the form.
        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Submit'),
            '#description' => $this->t('Submit, #type = submit'),
        ];
        
        return $form;
    }
    
    public function validateForm(array &$form, FormStateInterface $form_state) {
        parent::validateForm($form, $form_state);
    }
    
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $visa_type = $form_state->getValue('visa_type');
        $result = \Drupal::database()->insert('visa_type')
                ->fields([
                    'visa_type' => $visa_type,
                    'uid' => \Drupal::currentUser()->id(),
                    'created' => date('Y-m-d H:i:s'),
                ])
                ->execute();
        $message = $this->t('Visa Type @visatype has been added', array('@visatype' => $visa_type));
        drupal_set_message($message);

    }

}
