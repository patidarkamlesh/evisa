<?php

namespace Drupal\evisa\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class VisaTypeForm extends FormBase {
    /**
     * Visa Type Form ID
     * @return string
     */
    public function getFormId() {
        return 'visa_type_form';
    }
    /**
     * Visa Type Form for Add / Edit
     * @param array $form
     * @param FormStateInterface $form_state
     * @return array $form
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        
        $editId = \Drupal::request()->query->get('type');
        if(isset($editId) && is_numeric($editId)) {
          $record = getVisaTypeFromId($editId);  
          $hiddenId = $record['vtid'];
        } else {
            $hiddenId = 0;
        }
        $form['visa_type'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Type of Visa'),
            '#description' => $this->t('Enter Type of Visa'),
            '#required' => TRUE,
            '#default_value' => (isset($record['visa_type']) && $editId) ? $record['visa_type'] : '',
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
            '#description' => $this->t('Submit, #type = submit'),
        ];
        
        return $form;
    }
    /**
     * Validate Visa Type
     * @param array $form
     * @param FormStateInterface $form_state
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
      if (empty($form_state->getValue('visa_type'))) {
        $form_state->setErrorByName('visa_type', $this->t('Type of Visa should not be Empty.'));
      }        
    }
    /**
     * Insert / update visa type into database 
     * @param array $form
     * @param FormStateInterface $form_state
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $visa_type = $form_state->getValue('visa_type');
        $visatypeId    = $form_state->getValue('editid');
        //Update if Purpose ID set else Insert
        if(isset($visatypeId) && !empty($visatypeId)) {
        $result = \Drupal::database()->update('visa_types')
                    ->fields([
                        'visa_type' => $visa_type,
                        'uid' => \Drupal::currentUser()->id(),
                        'updated' => date('Y-m-d H:i:s'),
                    ])
                    ->condition('vtid', $visatypeId)
                    ->execute();            
           $message = $this->t('Visa Type @type has been updated', array('@type' => $visa_type)); 
        } else {
        $result = \Drupal::database()->insert('visa_types')
                    ->fields([
                        'visa_type' => $visa_type,
                        'status_id' => 1,
                        'uid' => \Drupal::currentUser()->id(),
                        'created' => date('Y-m-d H:i:s'),
                        'updated' => date('Y-m-d H:i:s'),
                    ])
                    ->execute();
            $message = $this->t('Visa Type @type has been added', array('@type' => $visa_type));
        }
        drupal_set_message($message);
        //Redirect to Type of visa Page
        $form_state->setRedirect('evisa.visatype');
    }

}
