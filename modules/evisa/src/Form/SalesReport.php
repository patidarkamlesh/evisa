<?php

namespace Drupal\evisa\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use \Drupal\Core\Link;
//use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SalesReport extends FormBase {
    /**
     * Sales Report Form ID
     * @return string
     */
    public function getFormId() {
        return 'sales_report';
    }
    /**
     * Sales Report Form for View data
     * @param array $form
     * @param FormStateInterface $form_state
     * @return array $form
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $custdata = \Drupal::request()->get('customer_id');
        $fromDate = \Drupal::request()->get('fd');
        $toDate = \Drupal::request()->get('td');
        $roles = \Drupal::currentUser()->getRoles();
        if(in_array('sales_user', $roles)) {
            $salesCustomer = getSalesCustomer(TRUE);
        } else if(in_array('admin', $roles) || in_array('administrator', $roles)) {
            $salesCustomer = getAllCustomer(TRUE);
        } else {
            $salesCustomer = array();
        }
        $form['filter'] = [
            '#type' => 'fieldset',
            '#collapsible' => TRUE,
            '#collapsed' => TRUE,
            '#title' => t('Search option')
        ];
        $form['filter']['customer_id'] = [
            '#title' => $this->t('Customer Name'),
            '#type' => 'select',
            '#options' => $salesCustomer,
            '#empty_option' => $this->t('Select'),
            '#default_value' =>(!empty($custdata)) ? $custdata: ''
        ];
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
            '#url' => Url::fromRoute('evisa.sales.report'),
        ];
        
        $num_per_page = 10;
        //Get Sales Report Data
        $query = \Drupal::database()->select('visa', 'v');
        $query->join('node_field_data', 'nf', 'nf.nid = v.customer_id');
        $query->addField('nf','title', 'customer_name');
        $query->addExpression('count(v.id)', 'total_visa');
        $query->addExpression('sum(v.visa_price)', 'total_visa_price');
        $query->addExpression('max(v.created_date)', 'last_transaction');
        if (!empty($custdata)) {
            $query->condition('v.customer_id', $custdata);
        }
        if (!empty($fromDate)) {
            $query->condition('v.created_date', $fromDate, '>=');
        }
        if (!empty($toDate)) {
            $query->condition('v.created_date', $toDate, '<=');
        }
        if(in_array('sales_user', $roles)){
          $query->condition('v.customer_id', getSalesCustomer(), 'IN');
        }
        $query->groupBy('v.customer_id');
        $query->groupBy('nf.title');
        $total = $query->countQuery()->execute()->fetchField();
        $page = pager_default_initialize($total, $num_per_page);

        $offset = $num_per_page * $page;
        $query->range($offset, $num_per_page);
        
        $salesReports = $query->execute()->fetchAll();
        //print_r($salesReports); exit;
        //create table header
        $header_table = [
            'customer_name' => t('Key Agent Name'),
            'last_txn_date' => t('Last Transaction Date'),
            'visa_count' => t('Visa Count'),
            'total_business' => t('Total Business'),
        ];
        $rows = [];
        foreach ($salesReports as $salesReport) {
            $rows[] = [
                'customer_name' => $salesReport->customer_name,
                'last_txn_date' => date('d-m-Y', strtotime($salesReport->last_transaction)),
                'visa_count' => $salesReport->total_visa,
                'total_business' => $salesReport->total_visa_price,
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
                '#url' => Url::fromRoute('evisa.download.sales'),
                '#attributes' => [
                    'target' => '_blank',
                    'class' => 'btn btn-primary'
                ]
            ];
        }
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
                'evisa.sales.report', [], ['query' => $query]
        );
        //Commented due to pagination & filter to work 
        //$form_state->setRebuild(TRUE);
    }

}
