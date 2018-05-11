<?php

namespace Drupal\evisa\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class DocumentVisaEdit extends FormBase {
    /**
     * Document Visa Form ID
     * @return string
     */
    public function getFormId() {
        return 'document_visa_edit';
    }
    /**
     * Document Visa Form for Edit
     * @param array $form
     * @param FormStateInterface $form_state
     * @return array $form
     */
    public function buildForm(array $form, FormStateInterface $form_state, $vdid = NULL) {
        $dropDown = TRUE;
        $visaDocs = getVisaDoc($vdid);
        //Get all Countries
        $form['country_name'] = [
            '#type' => 'item',
            '#title' => $this->t('Country Name'),
            '#markup' => $visaDocs['country_name'],
        ];
        $form['purpose_travel'] = [
            '#type' => 'item',
            '#title' => $this->t('Purpose of Travel'),
            '#markup' => $visaDocs['purpose_travel'],
        ];
        $form['visa_type'] = [
            '#type' => 'item',
            '#title' => $this->t('Type of Visa'),
            '#markup' => $visaDocs['visa_type'],
        ];
        $form['document'] = [
            '#type' => 'text_format',
            '#title' => t('Document Checklist'),
            '#format' => 'full_html',
            '#default_value' => $visaDocs['document']
        ];
        $form['country_id'] = [
            '#type' => 'hidden',
            '#value' => $visaDocs['country_id'],
        ];
        $form['purpose_id'] = [
            '#type' => 'hidden',
            '#value' => $visaDocs['purpose_id'],
        ];
        $form['visa_type_id'] = [
            '#type' => 'hidden',
            '#value' => $visaDocs['visa_type_id'],
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
        $country_id = $form_state->getValue('country_id');
        $purpose_id   = $form_state->getValue('purpose_id');
        $visa_type_id  = $form_state->getValue('visa_type_id');
        $document  = $form_state->getValue('document')['value'];
        // Update Visa Documents
        $updateQuery = \Drupal::database()->update('visa_document')
                    ->fields([
                        'document' => $document,
                        'updated_user_id' => \Drupal::currentUser()->id(),
                        'updated' => date('Y-m-d H:i:s'),
                    ])
                    ->condition('country_id', $country_id)
                    ->condition('purpose_id', $purpose_id)
                    ->condition('visa_type_id', $visa_type_id)
                    ->execute();

        $message = $this->t('Visa Document has been updated');
        drupal_set_message($message);
        //Redirect to Type of visa Page
        $form_state->setRedirect('evisa.documentvisa');
    }
    
}
