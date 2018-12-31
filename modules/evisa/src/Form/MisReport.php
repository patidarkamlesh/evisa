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
        $fromDate = \Drupal::request()->get('fd');
        $toDate = \Drupal::request()->get('td');
        $roles = \Drupal::currentUser()->getRoles();

        $form['filter'] = [
            '#type' => 'fieldset',
            '#collapsible' => TRUE,
            '#collapsed' => TRUE,
            '#title' => t('Search option')
        ];
        if(!in_array('agent', $roles)) {
        $form['filter']['customer_id'] = [
            '#title' => $this->t('Customer Name'),
            '#type' => 'entity_autocomplete',
            '#target_type' => 'node',
            '#size' => '72',
            '#selection_settings' => [
              'target_bundles' => ['customer']
            ],
            '#default_value' =>(!empty($custdata)) ? \Drupal::entityTypeManager()->getStorage('node')->load($custdata): '',
        ];
        }
        $form['filter']['from_date'] = [
            '#title' => $this->t('From Date'),
            '#type' => 'date',
            '#default_value' => (!empty($fromDate)) ? $fromDate : ''
        ];
        $form['filter']['to_date'] = [
            '#title' => $this->t('To Date'),
            '#type' => 'date',
            '#default_value' => (!empty($toDate)) ? $toDate : ''
        ];
        $form['filter']['submit'] = [
            '#type' => 'submit',
            '#value' => t('Search'),
        ];
        $form['filter']['reset'] = [
            '#type' => 'link',
            '#title' => $this->t('Reset'),
            '#attributes' => array(
                'class' => array('btn btn-primary'),
            ),
            '#url' => Url::fromRoute('evisa.mis'),
        ];
        
        $num_per_page = \Drupal::config('evisa.adminsettings')->get('limit');
        $query = \Drupal::database()->select('account_txn', 'ac');
        $query->leftJoin('visa_report', 'vr', 'ac.visa_id = vr.visa_id');
        $query->join('node_field_data', 'nf', 'nf.nid = ac.customer_id');
        $query->fields('ac', ['id','visa_id','debit','credit','txn_date','cum_amount', 'txn_reason', 'txn_type']);
        $query->fields('vr', ['id','visa_id','customer_name','customer_id','destination_name','purpose_name','visa_type_name','nationality','visa_price','urgent','name','passport_no', 'app_ref']);
        $query->fields('nf', ['title']);
        if (!empty($custdata)) {
            $query->condition('ac.customer_id', $custdata);
        }
        if (!empty($fromDate)) {
            $query->condition('ac.txn_date', $fromDate, '>=');
        }
        if (!empty($toDate)) {
            $query->condition('ac.txn_date', $toDate, '<=');
        }
        
        if(in_array('agent', $roles)){
          $query->condition('ac.customer_id', getCustomerId());
        }
        $total = $query->countQuery()->execute()->fetchField();
        $page = pager_default_initialize($total, $num_per_page);

        $offset = $num_per_page * $page;
        $query->range($offset, $num_per_page);
        $query->orderBy('ac.id', 'DESC');   
        
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
            $invoicePDF = Url::fromRoute('evisa.download_invoice', array('vrid' => $misReport->vr_id));
            $rows[] = [
                'customer_name' => $misReport->title,
                'bill_date' => date('d-M-Y', strtotime($misReport->txn_date)),  
                'bill_no' => ($misReport->txn_type == 'D') ? 'VS /'.$misReport->id : '',
                'app_ref' => $misReport->app_ref,
                'name' => $misReport->name,
                'passport' => (!empty($misReport->vr_id)) ? Link::fromTextAndUrl($misReport->passport_no, $invoicePDF) :$misReport->passport_no,
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
                '#url' => Url::fromRoute('evisa.download.mis'),
                '#attributes' => [
                    'target' => '_blank',
                    'class' => 'btn btn-primary'
                ]
            ];
        }
        $form['#theme'] = 'mis_report';
        $form['#attributes']['class'][] = 'form-horizontal';
        return $form;
    }
    /**
     * Validate MIS Report Form
     * @param array $form
     * @param FormStateInterface $form_state
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        if((!empty($form_state->getValue('from_date'))) && (!empty($form_state->getValue('to_date')))) {
            if(strtotime($form_state->getValue('from_date')) > strtotime($form_state->getValue('to_date'))) {
                $form_state->setErrorByName('to_date', $this->t('Please select proper date range'));
            }
            $fromDate = new \DateTime($form_state->getValue('from_date'));
            $toDate = new \DateTime($form_state->getValue('to_date'));
            $interval = $fromDate->diff($toDate);
            if($interval->days > 31) {
                $form_state->setErrorByName('to_date', $this->t('Please select one month period only'));
            }
        }
        parent::validateForm($form, $form_state);
    }
    /**
     * Insert / update Country Purpose of Travel Association into database 
     * @param array $form
     * @param FormStateInterface $form_state
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $customer_id = trim($form_state->getValue('customer_id'));
        $fromDate = $form_state->getValue('from_date');
        $toDate =$form_state->getValue('to_date');
        $query = [];
        if (!empty($customer_id)) {
            $query['customer_id'] = $customer_id;
        }
        if(!empty($fromDate) && empty($toDate)) {
            $toDate = new \DateTime($fromDate);
            $toDate->modify('+30 days');
            $toDate = $toDate->format('Y-m-d');
        }
        if(empty($fromDate) && !empty($toDate)) {
            $fromDate = new \DateTime($toDate);
            $fromDate->modify('-30 days');
            $fromDate = $fromDate->format('Y-m-d');
        }
        if (!empty($fromDate) && !empty($toDate)) {
            $query['fd'] = $fromDate;
            $query['td'] = $toDate;
        }
        $form_state->setRedirect(
                'evisa.mis', [], ['query' => $query]
        );
        //Commented due to pagination & filter to work 
        //$form_state->setRebuild(TRUE);
    }

}
