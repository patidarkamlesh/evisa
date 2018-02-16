<?php

namespace Drupal\hello\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class CountryTypeAssoc extends FormBase {
    
    public function getFormId() {
        return 'country_type_assoc';
    }
    
    public function buildForm(array $form, FormStateInterface $form_state) {
       /* $form['visa_type'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Visa Type'),
            '#description' => $this->t('Select Visa Type'),
            '#required' => TRUE,
            '#autocomplete_route_name' => 'hello.visaautocomplete',
            '#autocomplete_route_parameters' => array('field_name' => 'visa_type', 'count' => 10),
        ];
      */
        $countries =  $this->getCountry();
        $visaTypes =  $this->getVisaType();
        //print_r($countries); exit;
        $form['country'] = [
            '#type' => 'select',
            '#title' => $this->t('Country'),
            '#description' => $this->t('Select Country'),
            '#options' => $countries,
            '#required' => TRUE,
        ];
        
        $form['visa_type'] = [
            '#type' => 'select',
            '#title' => $this->t('Visa Type'),
            '#description' => $this->t('Select Visa Type'),
            '#options' => $visaTypes,
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
    
    public function validateForm(array &$form, FormStateInterface $form_state) {
        parent::validateForm($form, $form_state);
    }
    
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $country_id = $form_state->getValue('country');
        $visa_types = $form_state->getValue('visa_type');
        //Delete Previous country visa assignment
        $delQuery = \Drupal::database()->delete('country_visa_type_assoc')
                   ->condition('acid', $country_id)
                   ->execute();
        // Insert fresh country visa assignment
        $insertQuery = \Drupal::database()->insert('country_visa_type_assoc')
                       ->fields(['acid', 'avid']);
                     foreach($visa_types as $visa_type) {
                         $insertQuery->values(['acid' => $country_id, 'avid' => $visa_type]);
                     }
                     $insertQuery->execute();
        /*foreach($visa_types as $visa_type) {
         $query = \Drupal::database()->merge('country_visa_type_assoc')
                ->key(array('acid' => $country))
                ->fields(['acid' => $country, 'avid' => $visa_type])
                ->execute();           
        }*/

        $message = $this->t('Visa Type @visatype has been added', array('@visatype' => $country));
        drupal_set_message($message);

    }
    
    public function getCountry() {
      $results = [];  
      $countryResult = \Drupal::database()->select('country', 'c')
                       ->fields('c', array('cid','country_name', 'created'))
                       ->execute();
        foreach($countryResult as $row) {
            $results[$row->cid] = $row->country_name;
        }
        return $results;
    }
    public function getVisaType() {
      $results = [];  
      $visaTypeResult = \Drupal::database()->select('visa_type', 'vt')
                       ->fields('vt', array('vid','visa_type', 'created'))
                       ->execute();
        foreach($visaTypeResult as $row) {
            $results[$row->vid] = $row->visa_type;
        }
        return $results;
    }
    
}
