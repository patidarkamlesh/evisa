<?php

namespace Drupal\evisa\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class VendorForm extends Formbase {

    /**
     * Vendor form ID
     */
    public function getFormId() {
        return 'vendor_form';
    }

    /**
     * Vendor Form for Add / Edit
     * @param array $form
     * @param FormStateInterface $form_state
     * @return array $form
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $editId = \Drupal::request()->query->get('vdid');
        if (isset($editId) && is_numeric($editId)) {
            $record = getVendorFromId((int) $editId);
            $hiddenId = $record['vdid'];
        } else {
            $hiddenId = 0;
        }
        $form['editid'] = [
            '#type' => 'hidden',
            '#value' => $hiddenId
        ];
        $form['vendor_name'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Vendor Name'),
            '#description' => $this->t('Enter Vendor Name'),
            '#required' => TRUE,
            '#default_value' => (isset($record['vendor_name'])) ? $record['vendor_name'] : '',
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
     * Insert / update country into database 
     * @param array $form
     * @param FormStateInterface $form_state
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $vendor_name = $form_state->getValue('vendor_name');
        $vendorId = $form_state->getValue('editid');
        //Update if Country ID set else Insert
        if (isset($vendorId) && !empty($vendorId)) {
            $result = \Drupal::database()->update('vendor')
                    ->fields([
                        'vendor_name' => $vendor_name,
                    ])
                    ->condition('vdid', $vendorId)
                    ->execute();
            $message = $this->t('Vendor name has been updated');
        } else {
            $query = \Drupal::database()->insert('vendor')
                    ->fields([
                        'vendor_name' => $vendor_name,
                    ])
                    ->execute();
            $message = 'Vendor has been added';
        }
        drupal_set_message($message);
        //Redirect to Vendor Page
        $form_state->setRedirect('evisa.vendor');
    }

}
