<?php

namespace Drupal\evisa\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class CountryPurposeForm extends FormBase {
    /**
     * Country Purpose Form ID
     * @return string
     */
    public function getFormId() {
        return 'country_purpose';
    }
    /**
     * Country Purpose Form for Add / Edit
     * @param array $form
     * @param FormStateInterface $form_state
     * @return array $form
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        //Get all Countries
        $countries =  $this->getAllCountry();
        //Get all Purpose of travel
        $purposes =   $this->getAllPurpose();
        $form['country'] = [
            '#type' => 'select',
            '#title' => $this->t('Country'),
            '#description' => $this->t('Select Country'),
            '#options' => $countries,
            '#required' => TRUE,
        ];
        
        $form['purpose_travel'] = [
            '#type' => 'select',
            '#title' => $this->t('Purpose of Travel'),
            '#description' => $this->t('Select Purpose of Travel'),
            '#options' => $purposes,
            '#required' => TRUE,
            '#multiple' => TRUE            
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
    /**
     * Validate Country Purpose Form
     * @param array $form
     * @param FormStateInterface $form_state
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        parent::validateForm($form, $form_state);
    }
    /**
     * Insert / update Country Purpose of Travel Association into database 
     * @param array $form
     * @param FormStateInterface $form_state
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $country_id = $form_state->getValue('country');
        $purposes = array_values($form_state->getValue('purpose_travel'));
        //Delete Previous country purpose of Travel assignment
        $delQuery = \Drupal::database()->delete('country_purpose_assoc')
                   ->condition('acid', $country_id)
                   ->execute();
        // Insert fresh country Purpose of Travel assignment
        $insertQuery = \Drupal::database()->insert('country_purpose_assoc')
                       ->fields(['acid', 'apid']);
                     foreach($purposes as $purpose) {
                         $insertQuery->values(['acid' => $country_id, 'apid' => $purpose]);
                     }
                     $insertQuery->execute();

        $message = $this->t('Country Visa Type Association has been added');
        drupal_set_message($message);
        //Redirect to Type of visa Page
        $form_state->setRedirect('evisa.countrypurpose');
    }
    /**
     * Get all Country for Drop-Down
     * @return Array for options
     */
    public function getAllCountry() {
      $results = [];  
      $countryResult = \Drupal::database()->select('country', 'c')
                       ->fields('c', array('cid','country_name'))
                       ->execute();
        foreach($countryResult as $row) {
            $results[$row->cid] = $row->country_name;
        }
        return $results;
    }
    /**
     * Get all Purpose of Travel for Drop-Down
     * @return Array for options
     */
    public function getAllPurpose() {
      $results = [];  
      $purposeResult = \Drupal::database()->select('purpose_of_travel', 'p')
                       ->fields('p', array('pid','purpose_travel'))
                       ->execute();
        foreach($purposeResult as $row) {
            $results[$row->pid] = $row->purpose_travel;
        }
        return $results;
    }
    
    
}
