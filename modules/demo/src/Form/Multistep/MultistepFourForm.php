<?php

/**
 * @file
 * Contains \Drupal\demo\Form\Multistep\MultistepThreeForm.
 */

namespace Drupal\demo\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

class MultistepFourForm extends MultistepFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'multistep_form_four';
  }

  /**
     * {@inheritdoc}.
     */
    public function buildForm(array $form, FormStateInterface $form_state) {

        $form = parent::buildForm($form, $form_state);
        $country_id = $this->store->get('country');
        $purpose_travel_id = $this->store->get('purpose_travel');
        $visa_type_id = $this->store->get('visa_type');
        $nationality_id = $this->store->get('nationality');
        $urgent_visa = $this->store->get('urgent_visa');
        $visaPrice = $this->store->get('visa_price');
        $urgentPrice = $this->store->get('urgent_price');
        $customerId = $this->store->get('customer_id');
        $destination_name = $this->store->get('destination_name');
        $purpose_name = $this->store->get('purpose_name');
        $type_visa_name = $this->store->get('type_visa_name');
        $nation_name = $this->store->get('nation_name');
        $final_price = $this->store->get('final_price');
        $title = $this->store->get('title');
        $name = $this->store->get('name');
        $passport_no = $this->store->get('passport_no');
        $father_name = $this->store->get('father_name');
        $mother_name = $this->store->get('mother_name');
        $photo = $this->store->get('photo');
        $passport_first = $this->store->get('passport_first');
        $passport_last = $this->store->get('passport_last');
        $support_doc_1 = $this->store->get('support_doc_1');
        $support_doc_2 = $this->store->get('support_doc_2');
        $ticket = $this->store->get('ticket');
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
        $city = $this->store->get('city');
        $state = $this->store->get('state');
        $country_add = $this->store->get('country_add');
        $zip = $this->store->get('zip');        
        
        if(empty($country_id) || empty($purpose_travel_id) || empty($visa_type_id) || empty($nationality_id) || empty($visaPrice)) {
            drupal_set_message('Not set form Value 1', 'error');
            return new RedirectResponse('/demo/multistep-one');
        }
        $form['country_name'] = [
            '#type' => 'item',
            '#title' => $this->t('Destination'),
            '#markup' => $destination_name,
        ];
        $form['purpose_travel'] = [
            '#type' => 'item',
            '#title' => $this->t('Purpose of Travel'),
            '#markup' => $purpose_name,
        ];
        $form['visa_type'] = [
            '#type' => 'item',
            '#title' => $this->t('Type of Visa'),
            '#markup' => $type_visa_name,
        ];
        $form['nationality'] = [
            '#type' => 'item',
            '#title' => $this->t('Nationality'),
            '#markup' => $nation_name,
        ];
        $form['urgent_visa'] = [
            '#type' => 'item',
            '#title' => $this->t('Urgent Visa'),
            '#markup' => ($urgent_visa == 1) ? 'Yes' : 'No',
        ];
        $form['visa_price'] = [
            '#type' => 'item',
            '#title' => $this->t('Visa Price'),
            '#markup' => $visaPrice,
        ];
        $form['urgent_price'] = [
            '#type' => 'item',
            '#title' => $this->t('Urgent Visa'),
            '#markup' => ($urgent_visa == 1) ? $urgentPrice : 0.00,
        ];
        $form['total_price'] = [
            '#type' => 'item',
            '#title' => $this->t('Total Visa Price'),
            '#markup' => ($urgent_visa == 1) ? number_format(($urgentPrice + $visaPrice), 2) : number_format($visaPrice, 2),
        ];
        $form['final_price'] = [
            '#type' => 'hidden',
            '#value' => ($urgent_visa == 1) ? ($urgentPrice + $visaPrice) : $visaPrice,
        ];
        // Form 2 Data
        $form['passanger'] = [
            '#type' => 'fieldset',
            '#collapsible' => TRUE,
            '#collapsed' => TRUE,
            '#title' => $this->t('Passanger Detail')
        ];
        $titleset = [1=>'Mr.', 2=>'Mrs.', 3=>'Master', 4=>'Miss'];
        $form['passanger']['name'] = [
            '#type' => 'item',
            '#title' => $this->t('Passanger Name'),
            '#markup' => $titleset[$title].' '.$name,
        ];
        $form['passanger']['father_name'] = [
            '#type' => 'item',
            '#title' => $this->t('Father Name'),
            '#markup' => $father_name,
        ];
        $form['passanger']['mother_name'] = [
            '#type' => 'item',
            '#title' => $this->t('Mother Name'),
            '#markup' => $mother_name,
        ];
        
        $form['passanger']['contact'] = [
            '#type' => 'item',
            '#title' => $this->t('Contact No'),
            '#markup' => $contact,
        ];
        $form['passanger']['dob'] = [
            '#type' => 'item',
            '#title' => $this->t('Date of Birth'),
            '#markup' => $dob,
        ];
        $form['passanger']['place_birth'] = [
            '#type' => 'item',
            '#title' => $this->t('Place of Birth'),
            '#markup' => $place_birth,
        ];
        $form['passanger']['country_birth'] = [
            '#type' => 'item',
            '#title' => $this->t('Country of Birth'),
            '#markup' => $country_birth,
        ];
        $genderList = [1=>'Male', 2=>'Female']; 
        $form['passanger']['gender'] = [
            '#type' => 'item',
            '#title' => $this->t('Gender'),
            '#markup' => $genderList[$gender],
        ];
        $marStatusList = [1=>'Single', 2=>'Married']; 
        $form['passanger']['mar_status'] = [
            '#type' => 'item',
            '#title' => $this->t('Marital Status'),
            '#markup' => $marStatusList[$mar_status],
        ];
        $form['passanger']['religion'] = [
            '#type' => 'item',
            '#title' => $this->t('Religion'),
            '#markup' => $religion,
        ];
        $form['passanger']['spouse'] = [
            '#type' => 'item',
            '#title' => $this->t('Spouse Name'),
            '#markup' => $spouse,
        ];
        $form['passanger']['profession'] = [
            '#type' => 'item',
            '#title' => $this->t('Profession'),
            '#markup' => $profession,
        ];
        $form['passport'] = [
            '#type' => 'fieldset',
            '#collapsible' => TRUE,
            '#collapsed' => TRUE,
            '#title' => $this->t('Passport Detail')
        ];
        $form['passport']['passport_no'] = [
            '#type' => 'item',
            '#title' => $this->t('Passport No'),
            '#markup' => $passport_no,
        ];
        $form['passport']['passport_issued'] = [
            '#type' => 'item',
            '#title' => $this->t('Issued At'),
            '#markup' => $passport_issued,
        ];
        $form['passport']['passport_issued_date'] = [
            '#type' => 'item',
            '#title' => $this->t('Date of Issue'),
            '#markup' => $passport_issued_date,
        ];
        $form['passport']['passport_expired_date'] = [
            '#type' => 'item',
            '#title' => $this->t('Date of Expiry'),
            '#markup' => $passport_expired_date,
        ];
        
        $form['flight'] = [
            '#type' => 'fieldset',
            '#collapsible' => TRUE,
            '#collapsed' => TRUE,
            '#title' => $this->t('Flight Detail')
        ];
        $form['flight']['arrival_from'] = [
            '#type' => 'item',
            '#title' => $this->t('Arrival From'),
            '#markup' => $arrival_from,
        ];
        $form['flight']['arrival_date'] = [
            '#type' => 'item',
            '#title' => $this->t('Arrival Date'),
            '#markup' => $arrival_date,
        ];
        $form['flight']['departure_to'] = [
            '#type' => 'item',
            '#title' => $this->t('Departure To'),
            '#markup' => $departure_to,
        ];
        $form['flight']['departure_date'] = [
            '#type' => 'item',
            '#title' => $this->t('Departure Date'),
            '#markup' => $departure_date,
        ];
        $form['address'] = [
            '#type' => 'fieldset',
            '#collapsible' => TRUE,
            '#collapsed' => TRUE,
            '#title' => $this->t('Address Detail')
        ];
        $form['address']['address_line_1'] = [
            '#type' => 'item',
            '#title' => $this->t('Address Line 1'),
            '#markup' => $address_line_1,
        ];
        $form['address']['address_line_2'] = [
            '#type' => 'item',
            '#title' => $this->t('Address Line 2'),
            '#markup' => $address_line_2,
        ];
        $form['address']['city'] = [
            '#type' => 'item',
            '#title' => $this->t('City'),
            '#markup' => $city,
        ];
        $form['address']['state'] = [
            '#type' => 'item',
            '#title' => $this->t('State'),
            '#markup' => $state,
        ];
        $form['address']['country_add'] = [
            '#type' => 'item',
            '#title' => $this->t('Country'),
            '#markup' => $country_add,
        ];
        $form['address']['zip'] = [
            '#type' => 'item',
            '#title' => $this->t('ZIP'),
            '#markup' => $zip,
        ];
        
        $form['actions']['previous'] = [
            '#type' => 'link',
            '#title' => $this->t('Modify Search'),
            '#attributes' => array(
                'class' => array('btn btn-primary'),
            ),
            '#weight' => 0,
            '#url' => Url::fromRoute('demo.multistep_one'),
        ];
        $form['#theme'] = 'multistep_form_four'; 
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
      /*  $this->store->set('final_price', $form_state->getValue('final_price'));
        $this->store->set('title', $form_state->getValue('title'));
        $this->store->set('name', $form_state->getValue('name'));
        $this->store->set('passport_no', $form_state->getValue('passport_no'));
        $this->store->set('father_name', $form_state->getValue('father_name'));
        $this->store->set('mother_name', $form_state->getValue('mother_name'));
        $this->store->set('photo', $form_state->getValue('photo'));
        $this->store->set('passport_first', $form_state->getValue('passport_first'));
        $this->store->set('passport_last', $form_state->getValue('passport_last'));
        $this->store->set('support_doc_1', $form_state->getValue('support_doc_1'));
        $this->store->set('support_doc_2', $form_state->getValue('support_doc_2'));
        $this->store->set('ticket', $form_state->getValue('ticket'));
        $this->store->set('contact', $form_state->getValue('contact'));
        $this->store->set('dob', $form_state->getValue('dob'));
        $this->store->set('place_birth', $form_state->getValue('place_birth'));
        $this->store->set('country_birth', $form_state->getValue('country_birth'));
        $this->store->set('gender', $form_state->getValue('gender'));
        $this->store->set('mar_status', $form_state->getValue('mar_status'));
        $this->store->set('religion', $form_state->getValue('religion'));
        $this->store->set('spouse', $form_state->getValue('spouse'));
        $this->store->set('profession', $form_state->getValue('profession'));
        $this->store->set('passport_issued', $form_state->getValue('passport_issued'));
        $this->store->set('passport_issued_date', $form_state->getValue('passport_issued_date'));
        $this->store->set('passport_expired_date', $form_state->getValue('passport_expired_date'));
        $this->store->set('arrival_from', $form_state->getValue('arrival_from'));
        $this->store->set('arrival_date', $form_state->getValue('arrival_date'));
        $this->store->set('departure_to', $form_state->getValue('departure_to'));
        $this->store->set('departure_date', $form_state->getValue('departure_date'));
        $this->store->set('address_line_1', $form_state->getValue('address_line_1'));
        $this->store->set('address_line_2', $form_state->getValue('address_line_2'));
        $this->store->set('city', $form_state->getValue('city'));
        $this->store->set('state', $form_state->getValue('state'));
        $this->store->set('country_add', $form_state->getValue('country_add'));
        $this->store->set('zip', $form_state->getValue('zip')); */

        $customerCumAccount = getCumAmount($this->store->get('customer_id'));
        $visaPrice = $this->store->get('final_price');
        if ($visaPrice <= $customerCumAccount) {
            // Save the data
            parent::saveData();
            $form_state->setRedirect('demo.multistep_one');
        } else {
            drupal_set_message(t('Insufficiant balance to post visa. To recharge your account, please contact Finance.'), 'error');
            $form_state->setRedirect('demo.multistep_one');
        }

        $form_state->setRedirect('demo.multistep_one');
    }

}
