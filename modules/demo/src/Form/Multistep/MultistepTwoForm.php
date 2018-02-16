<?php

/**
 * @file
 * Contains \Drupal\demo\Form\Multistep\MultistepTwoForm.
 */

namespace Drupal\demo\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

class MultistepTwoForm extends MultistepFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'multistep_form_two';
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
        $customerId = $this->store->get('customer_id');
        if(empty($country_id) || empty($purpose_travel_id) || empty($visa_type_id) || empty($nationality_id) || empty($visaPrice)) {
            drupal_set_message('Not set form Value 1', 'error');
            return new RedirectResponse('/drupal8.4/demo/multistep-one');
        }
        $country_name = getCountryFromId($country_id);
        $nationality = getNationalityFromId($nationality_id);
        $purpose_travel = getPurposeFromId($purpose_travel_id);
        $visa_type = getVisaTypeFromId($visa_type_id);
        $this->store->set('destination_name', $country_name['country_name']);
        $this->store->set('purpose_name', $purpose_travel['purpose_travel']);
        $this->store->set('nation_name', $nationality['nationality_name']);
        $this->store->set('type_visa_name', $visa_type['visa_type']);
        $form['country_name'] = [
            '#type' => 'item',
            '#title' => $this->t('Destination'),
            '#markup' => $country_name['country_name'],
        ];
        $form['purpose_travel'] = [
            '#type' => 'item',
            '#title' => $this->t('Purpose of Travel'),
            '#markup' => $purpose_travel['purpose_travel'],
        ];
        $form['visa_type'] = [
            '#type' => 'item',
            '#title' => $this->t('Type of Visa'),
            '#markup' => $visa_type['visa_type'],
        ];
        $form['nationality'] = [
            '#type' => 'item',
            '#title' => $this->t('Nationality'),
            '#markup' => $nationality['nationality_name'],
        ];
        $form['visa_price'] = [
            '#type' => 'item',
            '#title' => $this->t('Visa Price'),
            '#markup' => $visaPrice,
        ];
        $form['urgent_visa'] = [
            '#type' => 'item',
            '#title' => $this->t('Urgent Visa'),
            '#markup' => ($urgent_visa == 1) ? 'Yes' : 'No',
        ];
        // Form 2 Data
        $form['passanger'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Passanger Detail')
        ];
        $form['passanger']['name'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Passanger Name'),
            '#required' => TRUE
        ];
        $form['passanger']['passport_no'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Passport No'),
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
        $form['passanger']['photo'] = [
            '#type' => 'managed_file',
            '#title' => $this->t('Colour Passport Size photograph'),
            '#upload_location' => 'public://visadoc/' . $customerId . '/photo',
            '#upload_validators' => array('file_validate_extensions' => array('pdf')),
            //'#size' => 13,
            '#multiple' => FALSE,
            '#required' => TRUE
        ];
        $form['passanger']['passport_first'] = [
            '#type' => 'managed_file',
            '#title' => $this->t('Passport First page Coloured Scan Copy'),
            '#upload_location' => 'public://visadoc/' . $customerId . '/passport_first',
            //'#upload_validators' => array('file_validate_extensions' => array('pdf doc docx')),
            //'#size' => 13,
            '#multiple' => FALSE,
            '#required' => TRUE
        ];
        $form['passanger']['passport_last'] = [
            '#type' => 'managed_file',
            '#title' => $this->t('Passport Last page Coloured Scan Copy'),
            '#upload_location' => 'public://visadoc/' . $customerId . '/passport_last',
            //'#upload_validators' => array('file_validate_extensions' => array('pdf doc docx')),
            //'#size' => 13,
            '#multiple' => FALSE,
            '#required' => TRUE
        ];
        $form['passanger']['support_doc_1'] = [
            '#type' => 'managed_file',
            '#title' => $this->t('Supporting Document'),
            '#description' => $this->t('Father Visa/PPT Copy, Mother Visa/PPT Copy, Husband Visa/PPT Copy, Marriage Certificate, Observation Page, NOC'),
            '#upload_location' => 'public://visadoc/' . $customerId . '/support_doc_1',
            //'#upload_validators' => array('file_validate_extensions' => array('pdf doc docx')),
            //'#size' => 13,
            '#multiple' => FALSE,
        ];
        $form['passanger']['support_doc_2'] = [
            '#type' => 'managed_file',
            '#title' => $this->t('Supporting Document'),
            '#description' => $this->t('Father Visa/PPT Copy, Mother Visa/PPT Copy, Husband Visa/PPT Copy, Marriage Certificate, Observation Page, NOC'),
            '#upload_location' => 'public://visadoc/' . $customerId . '/support_doc_1',
            //'#upload_validators' => array('file_validate_extensions' => array('pdf doc docx')),
            //'#size' => 13,
            '#multiple' => FALSE,
        ];
        $form['passanger']['ticket'] = [
            '#type' => 'managed_file',
            '#title' => $this->t('Confirm ticket copy for 96 Hrs. Visa'),
            '#upload_location' => 'public://visadoc/' . $customerId . '/ticket',
            //'#upload_validators' => array('file_validate_extensions' => array('pdf doc docx')),
            //'#size' => 13,
            '#multiple' => FALSE,
        ]; 
        $form['actions']['previous'] = [
            '#type' => 'link',
            '#title' => $this->t('Modify Search'),
            '#attributes' => array(
                'class' => array('button'),
            ),
            '#weight' => 0,
            '#url' => Url::fromRoute('demo.multistep_one'),
        ];

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
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

        $customerCumAccount = getCumAmount($this->store->get('customer_id'));
        $visaPrice = $this->store->get('visa_price');
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
