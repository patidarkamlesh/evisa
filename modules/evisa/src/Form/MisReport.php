<?php

namespace Drupal\evisa\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use \Drupal\Core\Link;
//use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MisReport extends FormBase {
    /**
     * MIS Report Form ID
     * @return string
     */
    public function getFormId() {
        return 'mis_report';
    }
    /**
     * MIS Report Form for View data
     * @param array $form
     * @param FormStateInterface $form_state
     * @return array $form
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $custdata = \Drupal::request()->get('customer_id');
        $roles = \Drupal::currentUser()->getRoles();

        $form['filter'] = [
            '#type' => 'fieldset',
            '#collapsible' => TRUE,
            '#collapsed' => TRUE,
            '#title' => t('Search option')
        ];
        if(in_array('agent', $roles)) {
            
        } else {
        $form['filter']['customer_id'] = [
            '#title' => $this->t('Customer Name'),
            '#type' => 'entity_autocomplete',
            '#target_type' => 'node',
            '#selection_settings' => [
              'target_bundles' => ['customer']
            ],
            '#required' => TRUE,
            '#default_value' =>(!empty($custdata)) ? \Drupal::entityTypeManager()->getStorage('node')->load($custdata): ''
            
        ];        
        }
        $form['filter']['submit'] = [
            '#type' => 'submit',
            '#value' => t('Search'),
        ];
        $form['filter']['reset'] = [
            '#type' => 'link',
            '#title' => $this->t('Reset'),
            '#attributes' => array(
                'class' => array('button'),
            ),
            '#url' => Url::fromRoute('evisa.mis'),
        ];
        
        $num_per_page = 10;
        $query = \Drupal::database()->select('account_txn', 'ac');
        $query->leftJoin('visa_report', 'vr', 'ac.visa_id = vr.visa_id');
        $query->join('node_field_data', 'nf', 'nf.nid = ac.customer_id');
        $query->fields('ac', ['id','visa_id','debit','credit','txn_date','cum_amount', 'txn_reason', 'txn_type']);
        $query->fields('vr', ['id','visa_id','customer_name','customer_id','destination_name','purpose_name','visa_type_name','nationality','visa_price','urgent','name','passport_no', 'app_ref']);
        $query->fields('nf', ['title']);
        if (!empty($custdata)) {
            $query->condition('ac.customer_id', $custdata);
        }
        
        if(in_array('agent', $roles)){
          $query->condition('ac.customer_id', getCustomerId());
        }
        $total = $query->countQuery()->execute()->fetchField();
        $page = pager_default_initialize($total, $num_per_page);

        $offset = $num_per_page * $page;
        $query->range($offset, $num_per_page);
        $query->orderBy('ac.id', 'ASC');        
        $misReports = $query->execute()->fetchAll();
        //print_r($misReports); exit;
        //create table header
        $header_table = [
            'customer_name' => t('Customer Name'),
            'bill_date' => t('Bill Date'),
            'bill_no' => t('Bill No'),
            'app_ref' => t('Application Reference'),
            'name' => t('Pax name'),
            'passport' => t('Visa Passport'),
            'txn_remark' => t('Txn Remark'),
            'country_name' => t('Visa Country'),
            'purpose' => t('Visa Category'),
            'visa_type' => t('Visa type'),
            'debit' => t('Dr'),
            'credit' => t('Cr'),
            'cum_amount' => t('Balance'),
        ];
        $rows = [];
        
        foreach ($misReports as $misReport) {
            $rows[] = [
                'customer_name' => $misReport->title,
                'bill_date' => date('d-M-Y', strtotime($misReport->txn_date)),  
                'bill_no' => ($misReport->txn_type == 'D') ? 'VS /'.$misReport->id : '',
                'app_ref' => $misReport->app_ref,
                'name' => $misReport->name,
                'passport' => $misReport->passport_no,
                'txn_remark' => $misReport->txn_reason,
                'country_name' => $misReport->destination_name,
                'purpose' => $misReport->purpose_name,
                'visa_type' => $misReport->visa_type_name,
                'debit' => !empty($misReport->debit) ? $misReport->debit : '',
                'credit' => !empty($misReport->credit) ? $misReport->credit : '',
                'cum_amount' => $misReport->cum_amount,
            ];
        }
        //display Visa Type table
        $form['table'] = [
            '#type' => 'table',
            '#header' => $header_table,
            '#rows' => $rows,
            '#empty' => t('No records found'),
        ];
        
        $form['pager'] = [
            '#type' => 'pager'
        ];
        if (count($rows)) {
            $form['export'] = [
                '#title' => t('Export'),
                '#type' => 'link',
                '#url' => Url::fromRoute('evisa.download.excel'),
                '#attributes' => [
                    'target' => '_blank',
                    'class' => 'button'
                ]
            ];
        }
        return $form;
    }
    /**
     * Validate Cisa Report Form
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
        $customer_id = trim($form_state->getValue('customer_id'));
        $query = [];
        if (!empty($customer_id)) {
            $query = ['customer_id' => $customer_id];
        }
        $form_state->setRedirect(
                'evisa.mis', [], ['query' => $query]
        );
        //Commented due to pagination & filter to work 
        //$form_state->setRebuild(TRUE);
    }

}
