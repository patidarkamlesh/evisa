<?php

/**
 * @file
 * Contains \Drupal\demo\Form\Multistep\MultistepThreeForm.
 */

namespace Drupal\demo\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

class MultistepThreeForm extends MultistepFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'multistep_form_three';
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
            '#markup' => ($urgent_visa == 1) ? ($urgentPrice + $visaPrice) : $visaPrice,
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
        $form['passanger']['name'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Passanger Name'),
            '#required' => TRUE
        ];
        $form['passanger']['father_name'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Father Name'),
            '#required' => TRUE
        ];
        $form['passanger']['mother_name'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Mother Name'),
            '#required' => TRUE
        ];
        
        $form['passanger']['contact'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Contact No'),
            '#required' => TRUE
        ];
        $form['passanger']['dob'] = [
            '#type' => 'date',
            '#title' => $this->t('Date of Birth'),
            '#required' => TRUE
        ];
        $form['passanger']['place_birth'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Place of Birth'),
            '#required' => TRUE
        ];
        $form['passanger']['country_birth'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Country of Birth'),
            '#required' => TRUE
        ];
        $gender = [1=>'Male', 2=>'Female']; 
        $form['passanger']['gender'] = [
            '#type' => 'radios',
            '#title' => $this->t('Gender'),
            '#options' => $gender,
            '#required' => TRUE,
        ];
        $mar_status = [1=>'Single', 2=>'Married', 3=>'Widowed', 4=>'Divorced']; 
        $form['passanger']['mar_status'] = [
            '#type' => 'select',
            '#title' => $this->t('Marital Status'),
            '#options' => $mar_status,
            '#required' => TRUE
        ];
        $form['passanger']['religion'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Religion'),
            '#required' => TRUE
        ];
        $form['passanger']['spouse'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Spouse'),
        ];
        $form['passanger']['profession'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Profession'),
            '#required' => TRUE
        ];

        $form['passport'] = [
            '#type' => 'fieldset',
            '#collapsible' => TRUE,
            '#collapsed' => TRUE,
            '#title' => $this->t('Passport Detail')
        ];
        $form['passport']['passport_no'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Passport No'),
            '#required' => TRUE
        ];
        $form['passport']['passport_issued'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Issued At'),
            '#required' => TRUE
        ];
        $form['passport']['passport_issued_date'] = [
            '#type' => 'date',
            '#title' => $this->t('Date of Issue'),
            '#required' => TRUE
        ];
        $form['passport']['passport_expired_date'] = [
            '#type' => 'date',
            '#title' => $this->t('Date of Expiry'),
            '#required' => TRUE
        ];
        $form['passport']['photo'] = [
            '#type' => 'managed_file',
            '#title' => $this->t('Colour Passport Size photograph'),
            '#upload_location' => 'public://visadoc/' . $customerId . '/photo',
            '#upload_validators' => [
                'file_validate_extensions' => array('pdf'),
                //'file_validate_size' => array(),
            ],
            //'#size' => 13,
            '#multiple' => FALSE,
            '#required' => TRUE
        ];
        $form['passport']['passport_first'] = [
            '#type' => 'managed_file',
            '#title' => $this->t('Passport First page Coloured Scan Copy'),
            '#upload_location' => 'public://visadoc/' . $customerId . '/passport_first',
            //'#upload_validators' => array('file_validate_extensions' => array('pdf doc docx')),
            //'#size' => 13,
            '#multiple' => FALSE,
            '#required' => TRUE
        ];
        $form['passport']['passport_last'] = [
            '#type' => 'managed_file',
            '#title' => $this->t('Passport Last page Coloured Scan Copy'),
            '#upload_location' => 'public://visadoc/' . $customerId . '/passport_last',
            //'#upload_validators' => array('file_validate_extensions' => array('pdf doc docx')),
            //'#size' => 13,
            '#multiple' => FALSE,
            '#required' => TRUE
        ];
        $form['passport']['support_doc_1'] = [
            '#type' => 'managed_file',
            '#title' => $this->t('Supporting Document'),
            '#description' => $this->t('Father Visa/PPT Copy, Mother Visa/PPT Copy, Husband Visa/PPT Copy, Marriage Certificate, Observation Page, NOC'),
            '#upload_location' => 'public://visadoc/' . $customerId . '/support_doc_1',
            //'#upload_validators' => array('file_validate_extensions' => array('pdf doc docx')),
            //'#size' => 13,
            '#multiple' => FALSE,
        ];
        $form['passport']['support_doc_2'] = [
            '#type' => 'managed_file',
            '#title' => $this->t('Supporting Document'),
            '#description' => $this->t('Father Visa/PPT Copy, Mother Visa/PPT Copy, Husband Visa/PPT Copy, Marriage Certificate, Observation Page, NOC'),
            '#upload_location' => 'public://visadoc/' . $customerId . '/support_doc_2',
            //'#upload_validators' => array('file_validate_extensions' => array('pdf doc docx')),
            //'#size' => 13,
            '#multiple' => FALSE,
        ];
        $form['passport']['ticket'] = [
            '#type' => 'managed_file',
            '#title' => $this->t('Confirm ticket copy for 96 Hrs. Visa'),
            '#upload_location' => 'public://visadoc/' . $customerId . '/ticket',
            //'#upload_validators' => array('file_validate_extensions' => array('pdf doc docx')),
            //'#size' => 13,
            '#multiple' => FALSE,
        ];
        $form['flight'] = [
            '#type' => 'fieldset',
            '#collapsible' => TRUE,
            '#collapsed' => TRUE,
            '#title' => $this->t('Flight Detail')
        ];
        $form['flight']['arrival_from'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Arrival From'),
        ];
        $form['flight']['arrival_date'] = [
            '#type' => 'date',
            '#title' => $this->t('Arrival Date'),
        ];
        $form['flight']['departure_to'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Departure To'),
        ];
        $form['flight']['departure_date'] = [
            '#type' => 'date',
            '#title' => $this->t('Departure Date'),
        ];
        $form['address'] = [
            '#type' => 'fieldset',
            '#collapsible' => TRUE,
            '#collapsed' => TRUE,
            '#title' => $this->t('Address Detail')
        ];
        $form['address']['address_line_1'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Address Line 1'),
        ];
        $form['address']['address_line_2'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Address Line 2'),
        ];
        $form['address']['city'] = [
            '#type' => 'textfield',
            '#title' => $this->t('City'),
        ];
        $form['address']['state'] = [
            '#type' => 'textfield',
            '#title' => $this->t('State'),
        ];
        $form['address']['country_add'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Country'),
        ];
        $form['address']['zip'] = [
            '#type' => 'textfield',
            '#title' => $this->t('ZIP'),
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
        $form['#theme'] = 'multistep_form_three'; 
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $this->store->set('final_price', $form_state->getValue('final_price'));
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
        $this->store->set('zip', $form_state->getValue('zip'));

        $customerCumAccount = getCumAmount($this->store->get('customer_id'));
        $visaPrice = $form_state->getValue('final_price');
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
