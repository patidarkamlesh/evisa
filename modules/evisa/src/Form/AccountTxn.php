<?php

namespace Drupal\evisa\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use \Drupal\Core\Link;

class AccountTxn extends FormBase {
    /**
     * Account Transaction FORM ID
     * @return string
     */
    public function getFormId() {
        return 'account_txn';
    }
    /**
     * Account transaction for View data
     * @param array $form
     * @param FormStateInterface $form_state
     * @return array $form
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        // Add Credit Link
        $form['add_credit'] = [
            '#markup' => "<p><a class='use-ajax' data-dialog-type='modal' href='".$GLOBALS['base_url']."/eadmin/account/form'>Add Credit for customer</a></p>",
        ];
        $query = \Drupal::database()->select('account_txn', 'at');
        $query->join('node_field_data', 'nf', 'nf.nid = at.customer_id');
        $query->fields('at', ['txn_type', 'debit','credit', 'txn_date', 'txn_reason', 'cum_amount']);
        $query->addField('nf','title', 'customer_name');
        $query->orderBy('at.id', 'DESC');
        $accountTxns = $query->execute()->fetchAll();
        //create table header
        $header_table = [
            'customer_name' => t('Customer Name'),
            'txn_type' => t('Transaction Type'),
            'debit' => t('Debit'),
            'credit' => t('Credit'),
            'txn_date' => t('Transaction Date'),
            'txn_reason' => t('Transaction'),
            'cum_amount' => t('Cumulative Total')
        ];
        $rows = [];
        foreach ($accountTxns as $accountTxn) {
            $rows[] = [
                'customer_name' => $accountTxn->customer_name,
                'txn_type' => ($accountTxn->txn_type == 'C' ? 'Credit' : 'Debit'),
                'debit' => $accountTxn->debit,
                'credit' => $accountTxn->credit,
                'txn_date' => $accountTxn->txn_date,
                'txn_reason' => $accountTxn->txn_reason,
                'cum_amount' => $accountTxn->cum_amount,
            ];
        }
        //display Visa Type table
        $form['table'] = [
            '#type' => 'table',
            '#header' => $header_table,
            '#rows' => $rows,
            '#empty' => t('No records found'),
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

    }

}
