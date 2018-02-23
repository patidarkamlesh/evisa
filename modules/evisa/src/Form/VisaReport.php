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
        $custdata = \Drupal::request()->get('customer_name');
        $blockStatus = FALSE;
        // Add Visa Link
        $form['add_visa'] = [
            '#markup' => '<p><a class="use-ajax" data-dialog-type="modal" href="/drupal8.4/demo/multistep-one">Post Visa</a></p>',
        ];

        $form['filter'] = [
            '#type' => 'fieldset',
            '#collapsible' => TRUE,
            '#collapsed' => TRUE,
            '#title' => t('Search option')
        ];
        $form['filter']['customer_name'] = [
          '#type' => 'search',
          '#title' => t('Customer Name'),
          '#size' => 15,
          '#default_value' => (!empty($custdata)) ? $custdata : '', 
        ];
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
            '#url' => Url::fromRoute('evisa.visa'),
        ];
        
        $num_per_page = 1;
        $query = \Drupal::database()->select('visa_report', 'vr');
        $query->join('visa', 'v', 'v.id = vr.visa_id');
        $query->fields('vr', ['id','visa_id','customer_name','destination_name','purpose_name','visa_type_name','nationality','visa_price','urgent','name','passport_no', 'father_name', 'mother_name', 'created', 'status_id', 'approved_visa']);
        if (!empty($custdata)) {
          $query->condition('vr.customer_name', '%' . db_like($custdata) . '%', 'LIKE');
        }
        $roles = \Drupal::currentUser()->getRoles();
        if(in_array('agent', $roles)){
          $query->condition('v.customer_id', getCustomerId());
          $blockStatus = getBlockedStatus(getCustomerId());
        }
        $total = $query->countQuery()->execute()->fetchField();
        $page = pager_default_initialize($total, $num_per_page);

        $offset = $num_per_page * $page;
        $query->range($offset, $num_per_page);        
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
        $rows = [];
        $status = [1 => 'Open', 2=>'In Progress', 3=>'Approved', 4=> 'Rejected'];
        
        foreach ($visaReports as $visaReport) {
            $view = Url::fromUserInput('/evisa/visa/view/' . $visaReport->id);
            $edit = Url::fromUserInput('/evisa/visa/edit/' . $visaReport->id);
            $visaStatus = $status[$visaReport->status_id];
            $download = Url::fromUserInput('/evisa/visa/download/' . $visaReport->approved_visa, ['attributes' => ['target' => '_blank', 'class' => 'button']]);
            $rows[] = [
                'edit' => Link::fromTextAndUrl('Edit', $edit),
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
        $customer_name = trim($form_state->getValue('customer_name'));
        $query = [];
        if (!empty($customer_name)) {
            $query = ['customer_name' => $customer_name];
        }
        $form_state->setRedirect(
                'evisa.visa', [], ['query' => $query]
        );
        //Commented due to pagination & filter to work 
        //$form_state->setRebuild(TRUE);
    }

}
