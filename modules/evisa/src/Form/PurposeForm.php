<?php

namespace Drupal\evisa\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class PurposeForm extends FormBase {
    /**
     * Purpose of Travel Form ID
     * @return string
     */
    public function getFormId() {
        return 'purpose_form';
    }
    /**
     * Purpose of Travel Form for Add / Edit
     * @param array $form
     * @param FormStateInterface $form_state
     * @return array $form
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        
        $editId = \Drupal::request()->query->get('purpose');
        if(isset($editId) && is_numeric($editId)) {
          $record = getPurposeFromId($editId);  
          $hiddenId = $record['pid'];
        } else {
            $hiddenId = 0;
        }
        $form['purpose_travel'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Purpose of Travel'),
            '#description' => $this->t('Enter Purpose of Travel'),
            '#required' => TRUE,
            '#default_value' => (isset($record['purpose_travel']) && $editId) ? $record['purpose_travel'] : '',
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
     * Validate Purpose of Travel
     * @param array $form
     * @param FormStateInterface $form_state
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
      if (empty($form_state->getValue('purpose_travel'))) {
        $form_state->setErrorByName('purpose_travel', $this->t('Purpose of Travel should not be Empty.'));
      }        
    }
    /**
     * Insert / update purpose of travel into database 
     * @param array $form
     * @param FormStateInterface $form_state
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $purpose_travel = $form_state->getValue('purpose_travel');
        $purposeId    = $form_state->getValue('editid');
        //Update if Purpose ID set else Insert
        if(isset($purposeId) && !empty($purposeId)) {
        $result = \Drupal::database()->update('purpose_of_travel')
                    ->fields([
                        'purpose_travel' => $purpose_travel,
                        'uid' => \Drupal::currentUser()->id(),
                        'updated' => date('Y-m-d H:i:s'),
                    ])
                    ->condition('pid', $purposeId)
                    ->execute();            
           $message = $this->t('Purpose of Travel @purpose has been updated', array('@purpose' => $purpose_travel)); 
        } else {
        $result = \Drupal::database()->insert('purpose_of_travel')
                    ->fields([
                        'purpose_travel' => $purpose_travel,
                        'status_id' => 1,
                        'uid' => \Drupal::currentUser()->id(),
                        'created' => date('Y-m-d H:i:s'),
                        'updated' => date('Y-m-d H:i:s'),
                    ])
                    ->execute();
            $message = $this->t('Purpose of Travel @purpose has been added', array('@purpose' => $purpose_travel));
        }
        drupal_set_message($message);
        //Redirect to Purpose of Travel Page
        $form_state->setRedirect('evisa.purpose');
    }

}
