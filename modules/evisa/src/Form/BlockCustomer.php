<?php

namespace Drupal\evisa\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class BlockCustomer extends FormBase {
    /**
     * Block Customer Form ID
     * @return string
     */
    public function getFormId() {
        return 'block_customer';
    }
    /**
     * Block Customer Form to block
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
        ];
        $form['block_date'] = [
          '#type' => 'date',
          '#title' => $this->t('Block Date'),
          '#required' => TRUE,
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
        $block_date = strtotime($form_state->getValue('block_date'));
        $block = 0;
        $blockCust = \Drupal::database()->merge('block_customer')
                ->key(['customer_id' => $customer_id])
                ->fields([
                    'block_date' => $block_date,
                    'block' => $block,
                    'updated_user_id' => \Drupal::currentUser()->id(),
                    'updated' => date('Y-m-d H:i:s'),
                    'comment' => '',
                    'unblock_date' => 0
                ])
                ->execute();
        //Redirect to view block 
        $form_state->setRedirect('evisa.block');
    }

}
