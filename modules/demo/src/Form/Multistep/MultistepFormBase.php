<?php

/**
 * @file
 * Contains \Drupal\demo\Form\Multistep\MultistepFormBase.
 */

namespace Drupal\demo\Form\Multistep;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\SessionManagerInterface;
use Drupal\user\PrivateTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class MultistepFormBase extends FormBase {

  /**
   * @var \Drupal\user\PrivateTempStoreFactory
   */
  protected $tempStoreFactory;

  /**
   * @var \Drupal\Core\Session\SessionManagerInterface
   */
  private $sessionManager;

  /**
   * @var \Drupal\Core\Session\AccountInterface
   */
  private $currentUser;

  /**
   * @var \Drupal\user\PrivateTempStore
   */
  protected $store;

  /**
   * Constructs a \Drupal\demo\Form\Multistep\MultistepFormBase.
   *
   * @param \Drupal\user\PrivateTempStoreFactory $temp_store_factory
   * @param \Drupal\Core\Session\SessionManagerInterface $session_manager
   * @param \Drupal\Core\Session\AccountInterface $current_user
   */
  public function __construct(PrivateTempStoreFactory $temp_store_factory, SessionManagerInterface $session_manager, AccountInterface $current_user) {
    $this->tempStoreFactory = $temp_store_factory;
    $this->sessionManager = $session_manager;
    $this->currentUser = $current_user;

    $this->store = $this->tempStoreFactory->get('multistep_data');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('user.private_tempstore'),
      $container->get('session_manager'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = array();
    $form_state->disableCache();
    $customerId = getCustomerId();
    if(!empty($customerId)) {
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#button_type' => 'primary',
      '#weight' => 10,
    ];
    } else {
        $form['no_customer'] = [
            '#markup' => 'Customer is not associated with your access. Please contact system administrator.',
        ];
    }
    return $form;
  }

  /**
     * Saves the data from the multistep form.
     */
    protected function saveData() {
        // Logic for saving data.
        $country_id = $this->store->get('country');
        $purpose_travel_id = $this->store->get('purpose_travel');
        $visa_type_id = $this->store->get('visa_type');
        $nationality_id = $this->store->get('nationality');
        $urgent_visa = $this->store->get('urgent_visa');
        $visaPrice = $this->store->get('final_price');
        $customerId = $this->store->get('customer_id');
        $name = $this->store->get('name');
        $passport_no = $this->store->get('passport_no');
        $father_name = $this->store->get('father_name');
        $mother_name = $this->store->get('mother_name');
        $photo = $this->store->get('photo');
        $passport_first = $this->store->get('passport_first');
        $passport_last = $this->store->get('passport_last');
        $support_doc_1 = (!empty($this->store->get('support_doc_1')) ? $this->store->get('support_doc_1') : array(0));
        $support_doc_2 = (!empty($this->store->get('support_doc_2')) ? $this->store->get('support_doc_2') : array(0));
        $ticket = (!empty($this->store->get('ticket')) ? $this->store->get('ticket') : array(0));
        $contact = $this->store->get('contact');
        $dob = $this->store->get('dob');
        $place_birth = $this->store->get('place_birth');
        $country_birth = $this->store->get('country_birth');
        $gender = $this->store->get('gender');
        $mar_status = $this->store->get('mar_status');
        $religion = $this->store->get('religion');
        $spouse = $this->store->get('spouse');
        $profession = $this->store->get('profession');
        $passport_issued = $this->store->get('passport_issued');
        $passport_issued_date = $this->store->get('passport_issued_date');
        $passport_expired_date = $this->store->get('passport_expired_date');
        $arrival_from = $this->store->get('arrival_from');
        $arrival_date = $this->store->get('arrival_date');
        $departure_to = $this->store->get('departure_to');
        $departure_date = $this->store->get('departure_date');
        $address_line_1 = $this->store->get('address_line_1');
        $address_line_2 = $this->store->get('address_line_2');
        $city  = $this->store->get('city');
        $state = $this->store->get('state');
        $country_add = $this->store->get('country_add');
        $zip = $this->store->get('zip');
        $roe = $this->store->get('roe');
        $visa_fee = $this->store->get('visa_fee');
        $urgent_fee = $this->store->get('urgent_fee');
        
        $customerCumAccount = getCumAmount($customerId);
        $applicationRef = 'EVL'.date('YmdHis').$customerId;
        $visa_id = 0;
        if ($visaPrice <= $customerCumAccount) {
            // Begin Transation
            $transaction = \Drupal::database()->startTransaction();
            try {
                //Insert data into visa table 
                $visa_id = \Drupal::database()->insert('visa')
                        ->fields([
                            'customer_id' => $customerId,
                            'agent_id' => \Drupal::currentUser()->id(),
                            'dest_id' => $country_id,
                            'purpose_travel_id' => $purpose_travel_id,
                            'visa_type_id' => $visa_type_id,
                            'national_id' => $nationality_id,
                            'urgent_visa' => $urgent_visa,
                            'visa_price' => $visaPrice,
                            'pas_name' => $name,
                            'pas_passport_no' => $passport_no,
                            'pas_father' => $father_name,
                            'pas_mother' => $mother_name,
                            'pas_photo_id' => $photo[0],
                            'pas_passport_first_id' => $passport_first[0],
                            'pas_passport_last_id' => $passport_last[0],
                            'pas_sup_doc_1' => $support_doc_1[0],
                            'pas_sup_doc_2' => $support_doc_2[0],
                            'pas_ticket' => $ticket[0],
                            'status_id' => 1,
                            'created_date' => date('Y-m-d H:i:s'),
                            'app_ref' => $applicationRef
                        ])
                        ->execute();
                // Upload photo & passport pages
                $photofile = \Drupal\file\Entity\File::load($photo[0]);
                $photofile->setPermanent();
                $photofile->save();
                $passportfirstfile = \Drupal\file\Entity\File::load($passport_first[0]);
                $passportfirstfile->setPermanent();
                $passportfirstfile->save();
                $passportlastfile = \Drupal\file\Entity\File::load($passport_last[0]);
                $passportlastfile->setPermanent();
                $passportlastfile->save();
                if (!empty($support_doc_1[0])) {
                    $supdoc1file = \Drupal\file\Entity\File::load($support_doc_1[0]);
                    $supdoc1file->setPermanent();
                    $supdoc1file->save();
                }
                if (!empty($support_doc_2[0])) {
                    $supdoc2file = \Drupal\file\Entity\File::load($support_doc_2[0]);
                    $supdoc2file->setPermanent();
                    $supdoc2file->save();
                }
                if (!empty($ticket[0])) {
                    $ticketfile = \Drupal\file\Entity\File::load($ticket[0]);
                    $ticketfile->setPermanent();
                    $ticketfile->save();
                }
                //Debit amount from customer account
                $newCumAmount = $customerCumAccount - $visaPrice;
                $accountId = \Drupal::database()->insert('account_txn')
                        ->fields([
                            'customer_id' => $customerId,
                            'txn_type' => 'D',
                            'debit' => $visaPrice,
                            'txn_date' => date('Y-m-d H:i:s'),
                            'uid' => \Drupal::currentUser()->id(),
                            'txn_reason' => 'Visa Posted',
                            'cum_amount' => $newCumAmount,
                            'visa_id' => $visa_id
                        ])
                        ->execute();
                //Set message 
                drupal_set_message($this->t('Visa has been uploaded successfully.'));
            } catch (Exception $e) {
                $transaction->rollBack();
                watchdog_exception('visa', $e);
                drupal_set_message($this->t('There is some issue to post visa. Please contact site administrator.'));
            }
        } else {
            drupal_set_message(t('Insufficiant balance to post visa. To recharge your account, please contact Finance.'), 'error');
        }
        if ($visa_id) {
            $customerName = getCustomerName($customerId);
            $destination_name = $this->store->get('destination_name');
            $purpose_name = $this->store->get('purpose_name');
            $nation_name = $this->store->get('nation_name');
            $type_visa_name = $this->store->get('type_visa_name');
            //Insert data into Visa Report
            $visa_report_id = \Drupal::database()->insert('visa_report')
                    ->fields([
                        'visa_id' => $visa_id,
                        'customer_name' => $customerName,
                        'customer_id' => $customerId,
                        'destination_name' => $destination_name,
                        'purpose_name' => $purpose_name,
                        'visa_type_name' => $type_visa_name,
                        'nationality' => $nation_name,
                        'visa_price' => $visaPrice,
                        'urgent' => $urgent_visa,
                        'visa_fee' => $visa_fee,
                        'urgent_fee' => $urgent_fee,
                        'roe' => $roe,
                        'agent_id' => \Drupal::currentUser()->id(),
                        'name' => $name,
                        'passport_no' => $passport_no,
                        'father_name' => $father_name,
                        'mother_name' => $mother_name,
                        'created' => date('Y-m-d H:i:s'),
                        'status_id' => 1,
                        'app_ref' => $applicationRef,
                        'contact' => $contact,
                        'dob' => $dob,
                        'place_birth' => $place_birth,
                        'country_birth' => $country_birth,
                        'gender' => $gender,
                        'mar_status' => $mar_status,
                        'religion' => $religion,
                        'spouse' => $spouse,
                        'profession' => $profession,
                        'passport_issued' => $passport_issued,
                        'passport_issued_date' => $passport_issued_date,
                        'passport_expired_date' => $passport_expired_date,
                        'arrival_from' => $arrival_from,
                        'arrival_date' => $arrival_date,
                        'departure_to' => $departure_to,
                        'departure_date' => $departure_date,
                        'address_line_1' => $address_line_1,
                        'address_line_2' => $address_line_2,
                        'city' => $city,
                        'state' => $state,
                        'country' => $country_add,
                        'zip' => $zip
                    ])
                    ->execute();
            //Notify Operation team for New Visa
        }
    }

    /**
   * Helper method that removes all the keys from the store collection used for
   * the multistep form.
   */
  protected function deleteStore() {
    $keys = ['country', 'purpose_travel', 'visa_type', 'nationality', 'visa_price', 'customer_id', 'urgent_visa', 'name', 'passport_no', 'father_name', 'mother_name', 'photo', 'passport_first', 'passport_last', 'support_doc_1', 'support_doc_2', 'ticket', 'destination_name', 'purpose_name', 'nation_name', 'type_visa_name', 'final_price', 'urgent_price',
             'contact','dob','place_birth','country_birth','gender','mar_status','religion','spouse','profession','passport_issued',
             'passport_issued_date','passport_expired_date','arrival_from','arrival_date','departure_to','departure_date','address_line_1','address_line_2',
             'city','state','country_add','zip','urgent_fee', 'visa_fee', 'roe'  
            ];
    foreach ($keys as $key) {
      $this->store->delete($key);
    }
  }
}
