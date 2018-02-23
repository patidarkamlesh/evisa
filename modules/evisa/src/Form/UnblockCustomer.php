<?php

namespace Drupal\evisa\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class UnblockCustomer extends FormBase {
    /**
     * Unblock Customer Form ID
     * @return string
     */
    public function getFormId() {
        return 'unblock_customer';
    }
    /**
     * Unblock Customer Form to unblock customer
     * @param array $form
     * @param FormStateInterface $form_state
     * @return array $form
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $blockId = \Drupal::request()->get('bid');
        $blockData =  getBlockedData($blockId);
        $form['customer_id'] = [
            '#title' => $this->t('Customer Name'),
            '#type' => 'item',
            '#markup' => $blockData['customer_name']
        ];
        $form['block_id'] = [
            '#type' => 'hidden',
            '#value' => $blockData['id']
        ];
        $form['block_date'] = [
            '#title' => $this->t('Block Date'),
            '#type' => 'item',
            '#markup' => date('d-m-Y', $blockData['block_date'])
        ];
        $form['unblock_comment'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Unblock Comment'),
          '#required' => TRUE,
        ];
        $form['actions'] = [
            '#type' => 'actions',
        ];
        // Add a submit button that handles the submission of the form.
        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Unblock'),
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
        $comment = $form_state->getValue('unblock_comment');
        $block_id = $form_state->getValue('block_id');
        $block = 0;
        $blockCust = \Drupal::database()->update('block_customer')
                ->fields([
                    'comment' => $comment,
                    'block' => $block,
                    'unblock_date' => strtotime(date('Y-m-d')),
                    'updated_user_id' => \Drupal::currentUser()->id(),
                    'updated' => date('Y-m-d H:i:s'),
                ])
                ->condition('id', $block_id)
                ->execute();
        //Redirect to view block 
        $form_state->setRedirect('evisa.block');
    }

}
