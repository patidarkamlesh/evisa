<?php

namespace Drupal\evisa\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;

class VisaEdit extends FormBase {
    /**
     * Visa Edit ID
     * @return string
     */
    public function getFormId() {
        return 'visa_edit';
    }
    /**
     * Visa Edit Form
     * @param array $form
     * @param FormStateInterface $form_state
     * @return array $form
     */
    public function buildForm(array $form, FormStateInterface $form_state, $vid = NULL) {
        $visaData = visaDetail($vid);
        $customerId = getCustomerId($visaData['agent_id']);
        if (!empty($visaData['id'])) {
            $form['report_id'] = [
                '#type' => 'hidden',
                '#value' => $visaData['id'],
            ];
            $form['visa_id'] = [
                '#type' => 'hidden',
                '#value' => $visaData['visa_id'],
            ];
            $form['agent_id'] = [
                '#type' => 'hidden',
                '#value' => $visaData['agent_id'],
            ];            
            $form['old_status_id'] = [
                '#type' => 'hidden',
                '#value' => $visaData['status_id'],
            ];            
            $form['customer_name'] = [
                '#type' => 'item',
                '#title' => $this->t('Customer Name:'),
                '#markup' => $visaData['customer_name'],
            ];
            $form['customer_id'] = [
                '#type' => 'hidden',
                '#value' => $visaData['customer_id'],
            ];
            $form['app_ref'] = [
                '#type' => 'item',
                '#title' => $this->t('Application Reference:'),
                '#markup' => $visaData['app_ref'],
            ];
            $form['country'] = [
                '#type' => 'item',
                '#title' => $this->t('Destination'),
                '#markup' => $visaData['destination_name'],
            ];
            $form['purpose_travel'] = [
                '#type' => 'item',
                '#title' => $this->t('Purpose of travel'),
                '#markup' => $visaData['purpose_name'],
            ];
            $form['visa_type'] = [
                '#type' => 'item',
                '#title' => $this->t('Type of Visa'),
                '#markup' => $visaData['visa_type_name'],
            ];
            $form['nationality'] = [
                '#type' => 'item',
                '#title' => $this->t('Nationality'),
                '#markup' => $visaData['nationality'],
            ];            
            $form['price'] = [
                '#type' => 'item',
                '#title' => $this->t('Visa Price'),
                '#markup' => $visaData['visa_price'],
            ];
            $form['price_paid'] = [
                '#type' => 'hidden',
                '#value' => $visaData['visa_price'],
            ];            
            $form['urgent'] = [
                '#type' => 'item',
                '#title' => $this->t('Urgent'),
                '#markup' => $visaData['urgent'] == 1 ? 'Yes': 'No',
            ];
            $form['name'] = [
                '#type' => 'item',
                '#title' => $this->t('Passanger Name'),
                '#markup' => $visaData['name'],
            ];
            $form['passport'] = [
                '#type' => 'item',
                '#title' => $this->t('Passport No'),
                '#markup' => $visaData['passport_no'],
            ];
            $status = [1 => 'Open', 2=>'In Progress', 3=>'Approved', 4=> 'Rejected', 5=> 'Cancelled'];            
            $form['status_id'] = [
                '#type' => 'select',
                '#title' => 'Status',
                '#required' => TRUE,
                '#options' => $status,
                '#empty_option' => t('Select'),
                '#default_value' => $visaData['status_id'],
            ];
            $form['reference_no'] = [
                '#type' => 'textfield',
                '#title' => 'Reference No',
                '#default_value' => $visaData['reference_no'],
                '#states' => [
                    'visible' => [
                        ':input[name="status_id"]' => ['value'=> 2 ]
                    ],
                    'required' => [
                        ':input[name="status_id"]' => ['value'=> 2 ]
                    ]                
                ],
                '#attributes' => [
                    'class' => ['form-control']  
                ],
            ];
            $form['approved_visa'] = [
                '#type' => 'managed_file',
                '#title' => 'Approved Visa',
                '#description' => 'To upload approved Visa',
                '#upload_location' => 'public://visadoc/' . $customerId . '/approved/'.date('Y-m-d'),
                '#upload_validators' => array('file_validate_extensions' => array('pdf')),
                '#states' => [
                    'visible' => [
                        ':input[name="status_id"]' => ['value'=> 3 ]
                    ],
                    'required' => [
                        ':input[name="status_id"]' => ['value'=> 3 ]
                    ]                
                ],
                
            ];
            $form['reject_reason'] = [
                '#type' => 'textfield',
                '#title' => 'Reject Reason',
                '#default_value' => $visaData['reject_reason'],
                '#states' => [
                    'visible' => [
                        ':input[name="status_id"]' => ['value'=> 4 ]
                    ],
                    'required' => [
                        ':input[name="status_id"]' => ['value'=> 4 ]
                    ]                
                ],
                '#attributes' => [
                    'class' => ['form-control']  
                ],
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
        } else {
            $form['no_data_found'] = [
                '#type' => 'item',
                '#markup' => $this->t('System didn\'t found any Visa data. Please contact system adminstrator.'),
            ];
        }
        $form['#theme'] = 'visa_edit_form';
        $form['#attributes']['class'][] = 'form-horizontal';
        return $form;
    }

    /**
     * Validate Country Purpose Form
     * @param array $form
     * @param FormStateInterface $form_state
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        //parent::validateForm($form, $form_state);
        if(($form_state->getValue('status_id') == 5)) {
           if($form_state->getValue('old_status_id') != 1) {
               $form_state->setErrorByName('status_id', $this->t('Visa can be cancelled only if it is open status'));
           }
        }
        if(($form_state->getValue('old_status_id') == 3) || ($form_state->getValue('old_status_id') == 4) || ($form_state->getValue('old_status_id') == 5)) {
            $form_state->setErrorByName('status_id', $this->t('Visa once approved, rejected or cancelled can not be proceed further'));
        }
    }
    /**
     * Insert / update Country Purpose of Travel Association into database 
     * @param array $form
     * @param FormStateInterface $form_state
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $status_id = $form_state->getValue('status_id');
        $reference_no = $form_state->getValue('reference_no');
        $approved_visa = $form_state->getValue('approved_visa');
        $reject_reason = $form_state->getValue('reject_reason');
        $report_id = $form_state->getValue('report_id');
        $visa_id = $form_state->getValue('visa_id');
        $visa_price_paid = $form_state->getValue('price_paid');
        $customer_id = $form_state->getValue('customer_id');
        $agent_id = $form_state->getValue('agent_id');
        $agentAccount = User::load($agent_id);
        $agentEmail = $agentAccount->get('mail')->value;

        //Update Visa Information
        $updateVisa = \Drupal::database()->update('visa')
                ->fields([
                    'status_id' => $status_id,
                    'reference_no' => ($status_id == 2 || (!empty($reference_no))) ? $reference_no : '',
                    'approved_visa' => ($status_id == 3) ? $approved_visa[0] : 0,
                    'reject_reason' => ($status_id == 4) ? $reject_reason : '',
                    'updated_user_id' => \Drupal::currentUser()->id(),
                    'updated' => date('Y-m-d H:i:s'),
                ])
                ->condition('id', $visa_id)
                ->execute();
        //Update Visa report
        $updateVisaReport = \Drupal::database()->update('visa_report')
                ->fields([
                    'status_id' => $status_id,
                    'approved_visa' => ($status_id == 3) ? $approved_visa[0] : 0,
                ])
                ->condition('id', $report_id)
                ->execute();
        if ($status_id == 3 && (!empty($approved_visa))) {
            // Upload photo & passport pages
            $approvedVisa = \Drupal\file\Entity\File::load($approved_visa[0]);
            $approvedVisa->setPermanent();
            $approvedVisa->save();
            //Get Agent Email Address
            $agentAccount = User::load($agent_id);
            $agentEmail = $agentAccount->get('mail')->value;
            if(empty($agentEmail)) {
               $agentEmail = 'mail.kamleshpatidar@gmail.com'; 
            }
            //Send Email for Approval
            $mailManager = \Drupal::service('plugin.manager.mail');
            $module = 'evisa';
            $key = 'approved_visa';
            $to = $agentEmail;
            $params['report_id'] = $report_id;
            $langcode = '';
            $send = true;
            try {
                $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
            } catch (Exception $e) {
                watchdog_exception('visa', $e);
            }
        }
        if ($status_id == 4) {
            // Get Agent Email Address
            $agentAccount = User::load($agent_id);
            $agentEmail = $agentAccount->get('mail')->value;
            if(empty($agentEmail)) {
               $agentEmail = 'mail.kamleshpatidar@gmail.com'; 
            }            
            //Send Email for Rejection
            $mailManager = \Drupal::service('plugin.manager.mail');
            $module = 'evisa';
            $key = 'rejected_visa';
            $to = $agentEmail;
            $params['report_id'] = $report_id;
            $langcode = '';
            $send = true;
            try {
                $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
            } catch (Exception $e) {
                watchdog_exception('visa', $e);
            }
        }
        if ($status_id == 5) {
            //Update Account information to credit for cancelled Visa
        
        $remark = "Visa Cancelled";
        $cumAmount = getCumAmount($customer_id);
        $cumAmount += $visa_price_paid;
        // Insert Credit
        $result = \Drupal::database()->insert('account_txn')
                ->fields([
                    'customer_id' => $customer_id,
                    'credit' => $visa_price_paid,
                    'txn_reason' => $remark,
                    'uid' => \Drupal::currentUser()->id(),
                    'txn_date' => date('Y-m-d H:i:s'),
                    'txn_type' => 'C',
                    'cum_amount' => $cumAmount,
                    'visa_id' => $visa_id
                ])
                ->execute();
        }
        drupal_set_message(t('Visa has been updated successfully.'));
        //Redirect to Visa Page
        $form_state->setRedirect('evisa.visa');
    }

}
