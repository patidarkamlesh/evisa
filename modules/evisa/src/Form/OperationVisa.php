<?php

/**
 * @file
 * Contains \Drupal\demo\Form\Multistep\MultistepThreeForm.
 */

namespace Drupal\evisa\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

class OperationVisa extends FormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'operation_view_visa';
  }

  /**
     * {@inheritdoc}.
     */
    public function buildForm(array $form, FormStateInterface $form_state) {

        $visa_id = \Drupal::request()->get('vid');
        $visaDetail = visaDetail($visa_id);
        //print_r($visaDetail); exit;
        $photoUrl = getUpFileUrl($visaDetail['pas_photo_id']);
        $passFirstUrl = getUpFileUrl($visaDetail['pas_passport_first_id']);
        $passLastUrl = getUpFileUrl($visaDetail['pas_passport_last_id']);
        $supDoc1Url = getUpFileUrl($visaDetail['pas_sup_doc_1']);
        $supDoc2Url = getUpFileUrl($visaDetail['pas_sup_doc_2']);
        $ticketUrl = getUpFileUrl($visaDetail['pas_ticket']);
        $referUrl = \Drupal::request()->server->get('HTTP_REFERER');

        $form['app_ref'] = [
            '#type' => 'item',
            '#markup' => $visaDetail['app_ref'],
        ];
        $form['customer_name'] = [
            '#type' => 'item',
            '#markup' => $visaDetail['customer_name'],
        ];
        $form['#express'] = strpos($referUrl, 'express') ? 1 : 0;
        
        $form['country_name'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Destination'),
            '#default_value' => $visaDetail['destination_name'],
            '#attributes' => array('readonly' => 'readonly'),
        ];
        $form['purpose_travel'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Purpose of Travel'),
            '#default_value' => $visaDetail['purpose_name'],
            '#attributes' => array('readonly' => 'readonly'),
        ];
        $form['visa_type'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Type of Visa'),
            '#default_value' => $visaDetail['visa_type_name'],
            '#attributes' => array('readonly' => 'readonly'),
        ];
        $form['nationality'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Nationality'),
            '#default_value' => $visaDetail['nationality'],
            '#attributes' => array('readonly' => 'readonly'),
        ];
        $form['urgent_visa'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Urgent Visa'),
            '#default_value' => ($visaDetail['urgent'] == 1) ? 'Yes' : 'No',
            '#attributes' => array('readonly' => 'readonly'),
        ];
        $form['total_price'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Total Visa Price'),
            '#default_value' => $visaDetail['visa_price'],
            '#attributes' => array('readonly' => 'readonly'),
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
            '#type' => 'textfield',
            '#title' => $this->t('Passanger Name'),
            '#default_value' => $titleset[$visaDetail['title']].' '.$visaDetail['name'],
            '#attributes' => array('readonly' => 'readonly'),
        ];
        $form['passanger']['father_name'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Father Name'),
            '#default_value' => $visaDetail['father_name'],
            '#attributes' => array('readonly' => 'readonly'),
        ];
        $form['passanger']['mother_name'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Mother Name'),
            '#default_value' => $visaDetail['mother_name'],
            '#attributes' => array('readonly' => 'readonly'),
        ];
        $form['passanger']['contact'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Contact No'),
            '#default_value' => $visaDetail['contact'],
            '#attributes' => array('readonly' => 'readonly'),
        ];
        $form['passanger']['dob'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Date of Birth'),
            '#default_value' => $visaDetail['dob'],
            '#attributes' => array('readonly' => 'readonly'),
        ];
        $form['passanger']['place_birth'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Place of Birth'),
            '#default_value' => $visaDetail['place_birth'],
            '#attributes' => array('readonly' => 'readonly'),
        ];
        $form['passanger']['country_birth'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Country of Birth'),
            '#default_value' => $visaDetail['country_birth'],
            '#attributes' => array('readonly' => 'readonly'),
        ];
        $genderList = [1=>'Male', 2=>'Female']; 
        $form['passanger']['gender'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Gender'),
            '#default_value' => $genderList[$visaDetail['gender']],
            '#attributes' => array('readonly' => 'readonly'),
        ];
        $marStatusList = [1=>'Single', 2=>'Married']; 
        $form['passanger']['mar_status'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Marital Status'),
            '#default_value' => $marStatusList[$visaDetail['mar_status']],
            '#attributes' => array('readonly' => 'readonly'),
        ];
        $form['passanger']['religion'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Religion'),
            '#default_value' => $visaDetail['religion'],
            '#attributes' => array('readonly' => 'readonly'),
        ];
        $form['passanger']['spouse'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Spouse Name'),
            '#default_value' => $visaDetail['spouse'],
            '#attributes' => array('readonly' => 'readonly'),
        ];
        $form['passanger']['profession'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Profession'),
            '#default_value' => $visaDetail['profession'],
            '#attributes' => array('readonly' => 'readonly'),
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
            '#default_value' => $visaDetail['passport_no'],
            '#attributes' => array('readonly' => 'readonly'),
        ];
        $form['passport']['passport_issued'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Issued At'),
            '#default_value' => $visaDetail['passport_issued'],
            '#attributes' => array('readonly' => 'readonly'),
        ];
        $form['passport']['passport_issued_date'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Date of Issue'),
            '#default_value' => $visaDetail['passport_issued_date'],
            '#attributes' => array('readonly' => 'readonly'),
        ];
        $form['passport']['passport_expired_date'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Date of Expiry'),
            '#default_value' => $visaDetail['passport_expired_date'],
            '#attributes' => array('readonly' => 'readonly'),
        ];
        $form['passport']['photo'] = [
            '#type' => 'item',
            '#markup' => $photoUrl->getUri(),
        ];
        $form['passport']['passport_first'] = [
            '#type' => 'item',
            '#markup' => $passFirstUrl->getUri(),
        ];
        $form['passport']['passport_last'] = [
            '#type' => 'item',
            '#markup' => $passLastUrl->getUri(),
        ];
        $form['passport']['support_doc_1'] = [
            '#type' => 'item',
            '#markup' => (!empty($supDoc1Url)) ? $supDoc1Url->getUri() : '',
        ];
        $form['passport']['support_doc_2'] = [
            '#type' => 'item',
            '#markup' => (!empty($supDoc2Url)) ? $supDoc2Url->getUri() : '',
        ];
        $form['passport']['ticket'] = [
            '#type' => 'item',
            '#markup' => (!empty($ticketUrl)) ? $ticketUrl->getUri() : '',
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
            '#default_value' => $visaDetail['arrival_from'],
            '#attributes' => array('readonly' => 'readonly'),
        ];
        $form['flight']['arrival_date'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Arrival Date'),
            '#default_value' => $visaDetail['arrival_date'],
            '#attributes' => array('readonly' => 'readonly'),
        ];
        $form['flight']['departure_to'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Departure To'),
            '#default_value' => $visaDetail['departure_to'],
            '#attributes' => array('readonly' => 'readonly'),
        ];
        $form['flight']['departure_date'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Departure Date'),
            '#default_value' => $visaDetail['departure_date'],
            '#attributes' => array('readonly' => 'readonly'),
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
            '#default_value' => $visaDetail['address_line_1'],
            '#attributes' => array('readonly' => 'readonly'),
        ];
        $form['address']['address_line_2'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Address Line 2'),
            '#default_value' => $visaDetail['address_line_2'],
            '#attributes' => array('readonly' => 'readonly'),
        ];
        $form['address']['city'] = [
            '#type' => 'textfield',
            '#title' => $this->t('City'),
            '#default_value' => $visaDetail['city'],
            '#attributes' => array('readonly' => 'readonly'),
        ];
        $form['address']['state'] = [
            '#type' => 'textfield',
            '#title' => $this->t('State'),
            '#default_value' => $visaDetail['state'],
            '#attributes' => array('readonly' => 'readonly'),
        ];
        $form['address']['country_add'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Country'),
            '#default_value' => $visaDetail['country'],
            '#attributes' => array('readonly' => 'readonly'),
        ];
        $form['address']['zip'] = [
            '#type' => 'textfield',
            '#title' => $this->t('ZIP'),
            '#default_value' => $visaDetail['zip'],
            '#attributes' => array('readonly' => 'readonly'),
        ];
        
        $form['#theme'] = 'view_visa_operation'; 
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        
    }

}
