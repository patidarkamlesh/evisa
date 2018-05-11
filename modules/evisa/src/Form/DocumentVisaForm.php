<?php

namespace Drupal\evisa\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class DocumentVisaForm extends FormBase {
    /**
     * Document Visa Form ID
     * @return string
     */
    public function getFormId() {
        return 'document_visa';
    }
    /**
     * Document Visa Form for Add
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
            '#empty_option' => $this->t('Select'),
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
            '#empty_option' => $this->t('Select'),
            '#ajax' => [
                'callback' => array($this, 'ajax_view_visa_type'),
                'wrapper' => 'view-visa-type'
            ],
        ];
        $form['visa_type'] = [
            '#type' => 'select',
            '#title' => $this->t('Visa Type'),
            '#description' => $this->t('Select Type of Visa'),
            '#prefix' => '<div id="view-visa-type">',
            '#suffix' => '</div>',            
            '#options' => getVisaBasedCountryPurpose($selected, $defaultPurpose, $dropDown),
            '#default_value' => $defaultVisa,
            '#required' => TRUE,
            '#empty_option' => $this->t('Select'),
        ];
        $form['document'] = [
            '#type' => 'text_format',
            '#title' => t('Document Checklist'),
            '#format' => 'full_html',
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
     * Ajax callback function for Visa type
     */
    public function ajax_view_visa_type(array &$form, FormStateInterface $form_state) {
        return $form['visa_type'];        
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
        $visa_type_id  = $form_state->getValue('visa_type');
        //$document  = htmlentities($form_state->getValue('document')['value']);
        $document  = $form_state->getValue('document')['value'];
        // Insert Visa Documents
        $insertQuery = \Drupal::database()->insert('visa_document')
                    ->fields([
                        'country_id' => $country_id,
                        'purpose_id' => $purpose_id,
                        'visa_type_id' => $visa_type_id,
                        'document' => $document,
                        'created_user_id' => \Drupal::currentUser()->id(),
                        'created' => date('Y-m-d H:i:s'),
                    ])
                    ->execute();

        $message = $this->t('Visa Document has been added');
        drupal_set_message($message);
        //Redirect to Type of visa Page
        $form_state->setRedirect('evisa.documentvisa');
    }
    
}
