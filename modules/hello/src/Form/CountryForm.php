<?php

namespace Drupal\hello\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class CountryForm extends FormBase {
    
    public function getFormId() {
        return 'country_form';
    }
    
    public function buildForm(array $form, FormStateInterface $form_state) {
         $form['fieldname'] = [
              '#markup' => "My Value Goes Here",
         ];
        $form['country_name'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Country Name'),
            '#description' => $this->t('Enter Country Name'),
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
        //parent::validateForm($form, $form_state);
      if (empty($form_state->getValue('country_name'))) {
        $form_state->setErrorByName('country_name', $this->t('Country Name is Empty.'));
      }        
    }
    
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $country_name = $form_state->getValue('country_name');
        $result = \Drupal::database()->insert('country')
                ->fields([
                    'country_name' => $country_name,
                    'uid' => \Drupal::currentUser()->id(),
                    'created' => date('Y-m-d H:i:s'),
                ])
                ->execute();
        $message = $this->t('Country @country has been added', array('@country' => $country_name));
        drupal_set_message($message);

    }

}
