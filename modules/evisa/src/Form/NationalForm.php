<?php

namespace Drupal\evisa\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class NationalForm extends FormBase {
    /**
     * nationality Form ID
     * @return string
     */
    public function getFormId() {
        return 'national_form';
    }
    /**
     * nationality Form for Add / Edit
     * @param array $form
     * @param FormStateInterface $form_state
     * @return array $form
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        
        $editId = \Drupal::request()->query->get('national');
        if(isset($editId) && is_numeric($editId)) {
          $record = getNationalityFromId($editId);  
          $hiddenId = $record['naid'];
        } else {
            $hiddenId = 0;
        }
        $form['nationality_name'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Nationality'),
            '#description' => $this->t('Enter Nationality'),
            '#required' => TRUE,
            '#default_value' => (isset($record['nationality_name']) && $editId) ? $record['nationality_name'] : '',
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
     * Validate Nationality
     * @param array $form
     * @param FormStateInterface $form_state
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
      if (empty($form_state->getValue('nationality_name'))) {
        $form_state->setErrorByName('nationality_name', $this->t('Nationality should not be Empty.'));
      }        
    }
    /**
     * Insert / update nationality into database 
     * @param array $form
     * @param FormStateInterface $form_state
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $nationality_name = $form_state->getValue('nationality_name');
        $nationalId    = $form_state->getValue('editid');
        //Update if nationality ID set else Insert
        if(isset($nationalId) && !empty($nationalId)) {
        $result = \Drupal::database()->update('nationality')
                    ->fields([
                        'nationality_name' => $nationality_name,
                        'uid' => \Drupal::currentUser()->id(),
                        'updated' => date('Y-m-d H:i:s'),
                    ])
                    ->condition('naid', $nationalId)
                    ->execute();            
           $message = $this->t('Nationality @nationality has been updated', array('@nationality' => $nationality_name)); 
        } else {
        $result = \Drupal::database()->insert('nationality')
                    ->fields([
                        'nationality_name' => $nationality_name,
                        'status_id' => 1,
                        'uid' => \Drupal::currentUser()->id(),
                        'created' => date('Y-m-d H:i:s'),
                        'updated' => date('Y-m-d H:i:s'),
                    ])
                    ->execute();
            $message = $this->t('Nationality @nationality has been added', array('@nationality' => $nationality_name));
        }
        drupal_set_message($message);
        //Redirect to nationality Page
        $form_state->setRedirect('evisa.nationality');
    }

}
