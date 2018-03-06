<?php

namespace Drupal\evisa\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class AccountForm extends FormBase {
    /**
     * Account Add Form ID
     * @return string
     */
    public function getFormId() {
        return 'account_form';
    }
    /**
     * Add Account Form
     * @param array $form
     * @param FormStateInterface $form_state
     * @return array $form
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $form['customer_id'] = [
            '#title' => $this->t('Customer Name'),
            '#type' => 'entity_autocomplete',
            '#target_type' => 'node',
            '#selection_settings' => [
              'target_bundles' => ['customer']
            ],
            '#required' => TRUE,
            '#size' => '18'
        ];        
        $form['price'] = [
            '#type' => 'number',
            '#title' => 'Price',
            '#description' => 'Enter Price',
            '#min' => 0,
            '#required' => TRUE,
        ];
        $form['remark'] = [
            '#type' => 'textfield',
            '#title' => 'Remark',
            '#description' => 'Enter remark',
            '#size' => '18'
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
        $customer_id = $form_state->getValue('customer_id');
        $remark = $form_state->getValue('remark');
        $price = $form_state->getValue('price');
        $cumAmount = getCumAmount($customer_id);
        $cumAmount += $price;
        // Insert Credit
        $result = \Drupal::database()->insert('account_txn')
                ->fields([
                    'customer_id' => $customer_id,
                    'credit' => $price,
                    'txn_reason' => $remark,
                    'uid' => \Drupal::currentUser()->id(),
                    'txn_date' => date('Y-m-d H:i:s'),
                    'txn_type' => 'C',
                    'cum_amount' => $cumAmount 
                ])
                ->execute();

        $message = $this->t('Amount Added for Customer');
        drupal_set_message($message);
        //Redirect to Type of visa Page
        $form_state->setRedirect('evisa.account');
    }

}