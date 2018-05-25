<?php

namespace Drupal\evisa\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use \Drupal\Core\Link;

class PriceAssignView extends FormBase {
    /**
     * Country Purpose Visa Form ID
     * @return string
     */
    public function getFormId() {
        return 'price_assignment_view';
    }
    /**
     * Price Assignment Form for View data
     * @param array $form
     * @param FormStateInterface $form_state
     * @return array $form
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $custdata = \Drupal::request()->get('customer_name');
        // Add Price Assignment Link
        $form['add_priceassign'] = [
            //'#markup' => '<p><a class="use-ajax" data-dialog-type="modal" href="/drupal8.4/evisa/priceassignment/form">Add Price Assignment</a></p>',
            '#markup' => "<a class='btn btn-primary' href='".$GLOBALS['base_url']."/evisa/priceassignment/form'>Add Price Assignment</a>",
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
            '#url' => Url::fromRoute('evisa.priceassignment'),
        ];
        
        //Get Country Purpose of Travel Visa type data 
        //$priceAssignments = getPriceAssignment();
        $num_per_page = \Drupal::config('evisa.adminsettings')->get('limit');
        $query = \Drupal::database()->select('price_assignment', 'pa');
        $query->join('country', 'c', 'c.cid = pa.country_id');
        $query->join('purpose_of_travel', 'p', 'p.pid = pa.purpose_id');
        $query->join('visa_types', 'v', 'v.vtid = pa.visa_type_id');
        $query->join('node_field_data', 'nf', 'nf.nid = pa.customer_id');
        $query->fields('c', ['country_name']);
        $query->fields('p', ['purpose_travel']);
        $query->fields('v', ['visa_type']);
        $query->fields('pa', ['id', 'price', 'urgent_price']);
        $query->addField('nf','title', 'customer_name');
        //if (!empty($form_state->getValue('customer_name'))) {
        if (!empty($custdata)) {
          $query->condition('nf.title', '%' . db_like($custdata) . '%', 'LIKE');
        }
        $total = $query->countQuery()->execute()->fetchField();
        $page = pager_default_initialize($total, $num_per_page);

        $offset = $num_per_page * $page;
        $query->range($offset, $num_per_page);
        $query->orderBy('pa.id', 'DESC');
        $priceAssignments = $query->execute()->fetchAll();
        //$count_query = $query->countQuery()->execute()->fetchField();
        //create table header
        $header_table = [
            'customer_name' => t('Customer Name'),
            'country_name' => t('Country'),
            'purpose' => t('Purpose of Travel'),
            'visa_type' => t('Visa type'),
            'price' => t('Price'),
            'urgent_price' => t('Urgent Price'),
            'opt' => t('Operation'),
        ];
        $rows = [];
        foreach ($priceAssignments as $priceAssignment) {
            $edit = Url::fromUserInput('/evisa/priceassignment/edit/' . $priceAssignment->id);
            $rows[] = [
                'customer_name' => $priceAssignment->customer_name,
                'country_name' => $priceAssignment->country_name,
                'purpose' => $priceAssignment->purpose_travel,
                'visa_type' => $priceAssignment->visa_type,
                'price' => $priceAssignment->price,
                'urgent_price' => $priceAssignment->urgent_price,
                'opt' => Link::fromTextAndUrl('Edit', $edit)
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
        $customer_name = trim($form_state->getValue('customer_name'));
        $query = [];
        if (!empty($customer_name)) {
            $query = ['customer_name' => $customer_name];
        }
        $form_state->setRedirect(
                'evisa.priceassignment', [], ['query' => $query]
        );
        //Commented due to pagination & filter to work 
        //$form_state->setRebuild(TRUE);
    }

}
