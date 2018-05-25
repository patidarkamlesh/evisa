<?php

namespace Drupal\evisa\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class CountryForm extends FormBase {
    /**
     * Country Form ID
     * @return string
     */
    public function getFormId() {
        return 'country_form';
    }
    /**
     * Country Form for Add / Edit
     * @param array $form
     * @param FormStateInterface $form_state
     * @return array $form
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        
        $editId = \Drupal::request()->query->get('country');
        if(isset($editId) && is_numeric($editId)) {
          $record = getCountryFromId($editId);  
          $hiddenId = $record['cid'];
        } else {
            $hiddenId = 0;
        }
        $form['country_name'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Country Name'),
            '#description' => $this->t('Enter Country Name'),
            '#required' => TRUE,
            '#default_value' => (isset($record['country_name']) && $editId) ? $record['country_name'] : '',
        ];
        $form['editid'] = [
           '#type' => 'hidden',
           '#value' => $hiddenId 
        ]; 
        
        $form['actions'] = [
            '#type' => 'actions',
        ];

        // Add a submit button that handles the submission of the form.
        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Submit'),
            '#description' => $this->t('Submit'),
        ];
        
        return $form;
    }
    /**
     * Validate Country
     * @param array $form
     * @param FormStateInterface $form_state
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
      if (empty($form_state->getValue('country_name'))) {
        $form_state->setErrorByName('country_name', $this->t('Country Name should not be Empty.'));
      }        
    }
    /**
     * Insert / update country into database 
     * @param array $form
     * @param FormStateInterface $form_state
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $country_name = $form_state->getValue('country_name');
        $countryId    = $form_state->getValue('editid');
        //Update if Country ID set else Insert
        if(isset($countryId) && !empty($countryId)) {
        $result = \Drupal::database()->update('country')
                    ->fields([
                        'country_name' => $country_name,
                        'uid' => \Drupal::currentUser()->id(),
                        'updated' => date('Y-m-d H:i:s'),
                    ])
                    ->condition('cid', $countryId)
                    ->execute();            
           $message = $this->t('Country @country has been updated', array('@country' => $country_name)); 
        } else {
        $result = \Drupal::database()->insert('country')
                    ->fields([
                        'country_name' => $country_name,
                        'status_id' => 1,
                        'uid' => \Drupal::currentUser()->id(),
                        'created' => date('Y-m-d H:i:s'),
                        'updated' => date('Y-m-d H:i:s'),
                    ])
                    ->execute();
            $message = $this->t('Country @country has been added', array('@country' => $country_name));
        }
        drupal_set_message($message);
        //Redirect to Country Page
        $form_state->setRedirect('evisa.country');
    }

}
