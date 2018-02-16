<?php

namespace Drupal\evisa\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class CountryPurposeVisaForm extends FormBase {
    /**
     * Country Purpose Visa Form ID
     * @return string
     */
    public function getFormId() {
        return 'country_purpose_visa';
    }
    /**
     * Country Purpose Visa Form for Add / Edit
     * @param array $form
     * @param FormStateInterface $form_state
     * @return array $form
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $dropDown = TRUE;
        //Get all Countries
        $countries =  getAllCountry($dropDown);
        $selected = ($form_state->getValue('country')) ? $form_state->getValue('country') : key($countries);
        $defaultPurpose = ($form_state->getValue('purpose_travel')) ? $form_state->getValue('purpose_travel') : '';
        $defaultVisa = ($form_state->getValue('visa_type')) ? $form_state->getValue('visa_type') : '';
        $visaTypes = getAllVisaType($dropDown);
        $form['country'] = [
            '#type' => 'select',
            '#title' => $this->t('Country'),
            '#description' => $this->t('Select Country'),
            '#options' => $countries,
            '#default_value' => $selected,
            '#required' => TRUE,
            '#ajax' => [
                'callback' => array($this, 'ajax_view_purpose_travel'),
                'wrapper' => 'view-purpose-travel'
            ],
        ];
        $form['purpose_travel'] = [
            '#type' => 'select',
            '#title' => $this->t('Purpose of Travel'),
            '#description' => $this->t('Select Purpose of Travel'),
            '#prefix' => '<div id="view-purpose-travel">',
            '#suffix' => '</div>',
            '#options' => getPurposeBasedCountry($selected,$dropDown),
            '#required' => TRUE,
            '#default_value' => $defaultPurpose,             
        ];
        $form['visa_type'] = [
            '#type' => 'select',
            '#title' => $this->t('Visa Type'),
            '#description' => $this->t('Select Type of Visa'),
            '#options' => $visaTypes,
            '#default_value' => $defaultVisa,
            '#required' => TRUE,
            '#empty' => $this->t('Select'),
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
     * Ajax callback function for purpose of travel
     */
    public function ajax_view_purpose_travel(array &$form, FormStateInterface $form_state) {
        return $form['purpose_travel'];        
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
        $purpose_id   = $form_state->getValue('purpose_travel');
        $visaTypes  = array_values($form_state->getValue('visa_type'));
        //Delete Previous country purpose of Travel Visa Type assignment
        $delQuery = \Drupal::database()->delete('country_purpose_visa_assoc')
                   ->condition('acid', $country_id)
                   ->condition('apid', $purpose_id)
                   ->execute();
        // Insert fresh country Purpose of Travel assignment
        $insertQuery = \Drupal::database()->insert('country_purpose_visa_assoc')
                       ->fields(['acid', 'apid', 'avid']);
                     foreach($visaTypes as $visaType) {
                         $insertQuery->values(['acid' => $country_id, 'apid' => $purpose_id, 'avid' => $visaType]);
                     }
                     $insertQuery->execute();

        $message = $this->t('Country Purpose of travel Visa Type Association has been added');
        drupal_set_message($message);
        //Redirect to Type of visa Page
        $form_state->setRedirect('evisa.countrypurposevisa');
    }
    
}
