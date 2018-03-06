<?php

namespace Drupal\evisa\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class PriceAssignmentForm extends FormBase {
    /**
     * Country Purpose Visa Form ID
     * @return string
     */
    public function getFormId() {
        return 'price_assignment';
    }
    /**
     * Price Assignment Form for Add / Edit
     * @param array $form
     * @param FormStateInterface $form_state
     * @return array $form
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $dropDown = TRUE;
        //Get all Countries
        $countries =  getAllCountry($dropDown);
        $selected = ($form_state->getValue('country')) ? $form_state->getValue('country') : key($countries);
        $defaultPurpose = ($form_state->getValue('purpose_travel')) ? $form_state->getValue('purpose_travel') : 0;
        $defaultVisa = ($form_state->getValue('visa_type')) ? $form_state->getValue('visa_type') : 0;
        $form['customer_id'] = [
            '#title' => $this->t('Customer Name'),
            '#type' => 'entity_autocomplete',
            '#target_type' => 'node',
            '#selection_settings' => [
              'target_bundles' => ['customer']
            ],
            '#required' => TRUE,
        ];        
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
            '#validated' => TRUE,
            '#default_value' => $defaultPurpose,
            '#empty_option' => $this->t('Select'),
            '#ajax' => [
                'event' => 'change',
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
            '#options' => getVisaBasedCountryPurpose($selected,$defaultPurpose,$dropDown),
            '#default_value' => $defaultVisa,
            '#required' => TRUE,
            '#validated' => TRUE,
            '#empty_option' => $this->t('Select'),
        ];
        $form['price'] = [
            '#type' => 'number',
            '#title' => 'Price',
            '#description' => 'Enter Price',
            '#min' => 0,
            '#required' => TRUE
        ];
        $form['urgent_price'] = [
            '#type' => 'number',
            '#title' => 'Urgent Price',
            '#description' => 'Enter Urgent Price',
            '#min' => 0,
            '#required' => TRUE
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
     * Ajax callback function for Visa Type
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
        $customer_id = $form_state->getValue('customer_id');
        $country_id  = $form_state->getValue('country');
        $purpose_id  = $form_state->getValue('purpose_travel');
        $visaTypes   = $form_state->getValue('visa_type');
        $query = \Drupal::database()->select('price_assignment', 'pa')
                   ->fields('pa', ['id', 'price'])
                   ->condition('customer_id', $customer_id)
                   ->condition('country_id', $country_id)
                   ->condition('purpose_id', $purpose_id)
                   ->condition('visa_type_id', $visaTypes);
        $record = $query->execute()->fetchAssoc();
        if(!empty($record)) {
            $form_state->setErrorByName('customer_id', $this->t('Price Already set for this customer'));
        }
    }
    /**
     * Insert / update Country Purpose of Travel Association into database 
     * @param array $form
     * @param FormStateInterface $form_state
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $customer_id = $form_state->getValue('customer_id');
        $country_id = $form_state->getValue('country');
        $purpose_id = $form_state->getValue('purpose_travel');
        $visaTypes = $form_state->getValue('visa_type');
        $price = $form_state->getValue('price');
        $urgent_price = $form_state->getValue('urgent_price');
        // Insert Price assignment
        $result = \Drupal::database()->insert('price_assignment')
                ->fields([
                    'customer_id' => $customer_id,
                    'country_id' => $country_id,
                    'purpose_id' => $purpose_id,
                    'visa_type_id' => $visaTypes,
                    'price' => $price,
                    'urgent_price' => $urgent_price,
                    'created_user_id' => \Drupal::currentUser()->id(),
                    'created' => date('Y-m-d H:i:s'),
                    'updated_user_id' => \Drupal::currentUser()->id(),
                    'updated' => date('Y-m-d H:i:s'),
                ])
                ->execute();

        $message = $this->t('Price Assignment done for Customer');
        drupal_set_message($message);
        //Redirect to Type of visa Page
        $form_state->setRedirect('evisa.priceassignment');
    }

}
