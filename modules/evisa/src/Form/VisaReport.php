<?php

namespace Drupal\evisa\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use \Drupal\Core\Link;
//use Symfony\Component\HttpFoundation\BinaryFileResponse;

class VisaReport extends FormBase {
    /**
     * Visa Report Form ID
     * @return string
     */
    public function getFormId() {
        return 'visa_report';
    }
    /**
     * Visa Report Form for View data
     * @param array $form
     * @param FormStateInterface $form_state
     * @return array $form
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $custdata = \Drupal::request()->get('customer_id');
        $passport_no = \Drupal::request()->get('passport_no');
        $app_ref = \Drupal::request()->get('app_ref');
        $app_from = \Drupal::request()->get('app_from');
        $app_to = \Drupal::request()->get('app_to');
        $blockStatus = FALSE;
        $roles = \Drupal::currentUser()->getRoles();
        if(in_array('agent', $roles)) {
        // Add Visa Link
        $form['add_visa'] = [
            //'#markup' => '<p><a class="use-ajax" data-dialog-type="modal" href="/demo/multistep-one">Post Visa</a></p>',
            '#prefix' => '<div class="addvisa">',
            '#suffix' => '</div>', 
            '#markup' => "<a class='use-ajax btn btn-primary' data-dialog-type='modal' href='".$GLOBALS['base_url']."/demo/multistep-one'>Post Visa</a>",
        ];            
        } else {
        $form['filter'] = [
            '#type' => 'fieldset',
            '#collapsible' => TRUE,
            '#collapsed' => TRUE,
            '#title' => t('Search option')
        ];
        /*$form['filter']['customer_name'] = [
          '#type' => 'search',
          '#title' => t('Customer Name'),
          '#size' => 15,
          '#default_value' => (!empty($custdata)) ? $custdata : '', 
        ];*/
        
        $form['filter']['customer_id'] = [
            '#title' => $this->t('Customer Name'),
            '#type' => 'entity_autocomplete',
            '#target_type' => 'node',
            '#selection_settings' => [
              'target_bundles' => ['customer'],
            ],
            '#default_value' =>(!empty($custdata)) ? \Drupal::entityTypeManager()->getStorage('node')->load($custdata): ''
            
        ];        
        $form['filter']['passport_no'] = [
            '#title' => $this->t('Passport No'),
            '#type' => 'textfield',
            '#default_value' => (!empty($passport_no)) ? $passport_no : '',
        ];        
        $form['filter']['app_ref'] = [
            '#title' => $this->t('Application Reference'),
            '#type' => 'textfield',
            '#default_value' => (!empty($app_ref)) ? $app_ref : '',
        ];        
        $form['filter']['app_from'] = [
            '#title' => $this->t('Application From'),
            '#type' => 'date',
            '#default_value' => (!empty($app_from)) ? $app_from : '',
        ];        
        $form['filter']['app_to'] = [
            '#title' => $this->t('Application To'),
            '#type' => 'date',
            '#default_value' => (!empty($app_to)) ? $app_to : '',
        ];        
        $form['filter']['submit'] = [
            '#type' => 'submit',
            '#value' => t('Search'),
        ];
        $form['filter']['reset'] = [
            '#type' => 'link',
            '#title' => $this->t('Reset'),
            '#attributes' => [
                'class' => ['btn btn-primary']
            ],
            '#url' => Url::fromRoute('evisa.visa'),
        ];
        }
        $num_per_page = \Drupal::config('evisa.adminsettings')->get('limit');
        $query = \Drupal::database()->select('visa_report', 'vr');
        $query->join('visa', 'v', 'v.id = vr.visa_id');
        $query->fields('vr', ['id','visa_id','customer_name','customer_id','destination_name','purpose_name','visa_type_name','nationality','visa_price','urgent','name','passport_no', 'father_name', 'mother_name', 'created', 'status_id', 'approved_visa']);
        if (!empty($custdata)) {
          //$query->condition('vr.customer_name', '%' . db_like($custdata) . '%', 'LIKE');
            $query->condition('vr.customer_id', $custdata);
        }
        if (!empty($passport_no)) {
            $query->condition('vr.passport_no', '%' . db_like($passport_no) . '%', 'LIKE');
        }        
        if (!empty($app_ref)) {
            $query->condition('vr.app_ref', '%' . db_like($app_ref) . '%', 'LIKE');
        }        
        if (!empty($app_from)) {
            $app_from = date('Y-m-d', strtotime($app_from));
            $query->condition('vr.created', $app_from , '>=');
        }        
        if (!empty($app_to)) {
            $app_to = date('Y-m-d', strtotime($app_to));
            $query->condition('vr.created', $app_to , '<=');
        }        
        if(in_array('agent', $roles)){
          $query->condition('v.customer_id', getCustomerId());
          $blockStatus = getBlockedStatus(getCustomerId());
        }
        $total = $query->countQuery()->execute()->fetchField();
        $page = pager_default_initialize($total, $num_per_page);

        $offset = $num_per_page * $page;
        $query->range($offset, $num_per_page);
        $query->orderBy('vr.id', 'DESC');        
        $visaReports = $query->execute()->fetchAll();
        //create table header
        $header_table = [
            'edit' => t('Edit'),
            'customer_name' => t('Customer Name'),
            'country_name' => t('Destination'),
            'purpose' => t('Purpose of Travel'),
            'visa_type' => t('Visa type'),
            'national' => t('Nationality'),
            'passport' => t('Passport'),
            'price' => t('Visa Price'),
            'status' => t('Status'),
            'download' => t('Approved Visa'),
            'opt' => t('View'),
        ];
        if(!(\Drupal::currentUser()->hasPermission('edit visa'))) {
            unset($header_table['edit']);
        }
        $rows = [];
        $status = [1 => 'Open', 2=>'In Progress', 3=>'Approved', 4=> 'Rejected', 5=> 'Cancelled'];
        
        if(in_array('operation_user', $roles)){
            $viewLink = '/evisa/visa/opview/';
        } else {
            $viewLink = '/evisa/visa/view/';
        }
        foreach ($visaReports as $visaReport) {
            $view = Url::fromUserInput($viewLink . $visaReport->id);
            $edit = Url::fromUserInput('/evisa/visa/edit/' . $visaReport->id);
            $visaStatus = $status[$visaReport->status_id];
            $download = Url::fromUserInput('/evisa/visa/download/' . $visaReport->approved_visa, ['attributes' => ['target' => '_blank', 'class' => 'button']]);
            if((\Drupal::currentUser()->hasPermission('edit visa'))) {
                $rows[] = [
                    'edit' => ($visaReport->status_id == 1 || $visaReport->status_id == 2) ? Link::fromTextAndUrl('Edit', $edit): '--',
                    'customer_name' => $visaReport->customer_name,
                    'country_name' => $visaReport->destination_name,
                    'purpose' => $visaReport->purpose_name,
                    'visa_type' => $visaReport->visa_type_name,
                    'national' => $visaReport->nationality,
                    'passport' => $visaReport->passport_no,
                    'price' => $visaReport->visa_price,
                    'status' => $visaStatus,
                    'download' => ($blockStatus || $visaReport->status_id != 3) ? 'NA' : Link::fromTextAndUrl('Download', $download),
                    'opt' => Link::fromTextAndUrl('View', $view),
                ];
            } else {
                $rows[] = [
                    'customer_name' => $visaReport->customer_name,
                    'country_name' => $visaReport->destination_name,
                    'purpose' => $visaReport->purpose_name,
                    'visa_type' => $visaReport->visa_type_name,
                    'national' => $visaReport->nationality,
                    'passport' => $visaReport->passport_no,
                    'price' => $visaReport->visa_price,
                    'status' => $visaStatus,
                    'download' => ($blockStatus || $visaReport->status_id != 3) ? 'NA' : Link::fromTextAndUrl('Download', $download),
                    'opt' => Link::fromTextAndUrl('View', $view),
                ];
            }
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
        $passport_no = trim($form_state->getValue('passport_no'));
        $app_ref = trim($form_state->getValue('app_ref'));
        $app_from = trim($form_state->getValue('app_from'));
        $app_to = trim($form_state->getValue('app_to'));
        $query = [];
        if (!empty($customer_id)) {
            $query['customer_id'] =  $customer_id;
        }
        if (!empty($passport_no)) {
            $query['passport_no'] =  $passport_no;
        }
        if (!empty($app_ref)) {
            $query['app_ref'] =  $app_ref;
        }
        if (!empty($app_from)) {
            $query['app_from'] =  $app_from;
        }
        if (!empty($app_to)) {
            $query['app_to'] =  $app_to;
        }
        $form_state->setRedirect(
                'evisa.visa', [], ['query' => $query]
        );
        //Commented due to pagination & filter to work 
        //$form_state->setRebuild(TRUE);
    }

}
