<?php

/**
 * @file
 * Contains \Drupal\demo\Form\Multistep\MultistepOneForm.
 */

namespace Drupal\demo\Form\Multistep;
use Drupal\Core\Form\FormStateInterface;

class MultistepOneForm extends MultistepFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'multistep_form_one';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);
    $this->deleteStore();
    $customerId = getCustomerId();
    if(!empty($customerId)) {
     $dropDown = TRUE;
        //Get all Countries
        $countries =  getAllCountry($dropDown);
        // Get all Nationalities
        $nationalities =  getAllNationality($dropDown);
        $nationalselected = ($form_state->getValue('nationality')) ? $form_state->getValue('nationality') : 0;
        $selected = ($form_state->getValue('country')) ? $form_state->getValue('country') : 0;
        $defaultPurpose = ($form_state->getValue('purpose_travel')) ? $form_state->getValue('purpose_travel') : 0;
        $defaultVisa = ($form_state->getValue('visa_type')) ? $form_state->getValue('visa_type') : 0;
        $form['country'] = [
            '#type' => 'select',
            '#title' => $this->t('Country'),
            '#description' => $this->t('Select Country'),
            '#options' => $countries,
            '#default_value' => $selected,
            '#required' => TRUE,
            '#empty_option' => $this->t('Select'),
            '#ajax' => [
                'callback' => array($this, 'ajax_view_purpose_travel'),
                'wrapper' => 'view-purpose-travel'
            ],
        ];
        $form['purpose_travel'] = [
            '#type' => 'select',
            '#title' => $this->t('Purpose of Travel'),
            '#description' => $this->t('Select Purpose of Travel'),
            '#prefix' => '<div id="view-purpose-travel">',
            '#suffix' => '</div>',
            '#options' => getPurposeBasedCountry($selected,$dropDown),
            '#required' => TRUE,
            '#validated' => TRUE,
            '#default_value' => $defaultPurpose,
            '#empty_option' => $this->t('Select'),
            '#ajax' => [
                'event' => 'change',
                'callback' => array($this, 'ajax_view_visa_type'),
                'wrapper' => 'view-visa-type'
            ],
        ];
        $form['visa_type'] = [
            '#type' => 'select',
            '#title' => $this->t('Visa Type'),
            '#description' => $this->t('Select Type of Visa'),
            '#prefix' => '<div id="view-visa-type">',
            '#suffix' => '</div>',            
            '#options' => getVisaBasedCountryPurpose($selected,$defaultPurpose,$dropDown),
            '#default_value' => $defaultVisa,
            '#required' => TRUE,
            '#validated' => TRUE,
            '#empty_option' => $this->t('Select'),
        ];
        $form['nationality'] = [
            '#type' => 'select',
            '#title' => $this->t('Nationality'),
            '#description' => $this->t('Select Nationality'),
            '#options' => $nationalities,
            '#default_value' => $nationalselected,
            '#required' => TRUE,
            '#empty_option' => $this->t('Select'),
        ];
        $form['urgent_visa'] = [
            '#type' => 'checkbox',
            '#title' => 'Urgent Visa',
        ];

    $form['actions']['submit']['#value'] = $this->t('Next');
    } 
    return $form;
  }
    /**
     * Ajax callback function for purpose of travel
     */
    public function ajax_view_purpose_travel(array &$form, FormStateInterface $form_state) {
        return $form['purpose_travel'];        
    }
    /**
     * Ajax callback function for Visa Type
     */
    public function ajax_view_visa_type(array &$form, FormStateInterface $form_state) {
        return $form['visa_type'];        
    }
  /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $country_id = $form_state->getValue('country');
        $purpose_travel = $form_state->getValue('purpose_travel');
        $visa_type = $form_state->getValue('visa_type');
        $nationality = $form_state->getValue('nationality');
        $urgent_visa = $form_state->getValue('urgent_visa');
        $customerId = getCustomerId();
        $visaPriceInfo = getVisaPrice($country_id, $purpose_travel, $visa_type, $customerId, $nationality, $urgent_visa);
        $roe = getRoeFromCountry($country_id);
        $visaFee = $visaPriceInfo['price'];
        $urgentVisaFee = $visaPriceInfo['urgent_price'];
        $visaPrice = $visaPriceInfo['price']*$roe;
        if ((!empty($urgent_visa)) && (empty($visaPriceInfo['urgent_price']))) {
            if ($nationality == 1) {
                $visaPriceInfo['urgent_price'] = \Drupal::config('evisa.adminsettings')->get('def_urgent_indian_price');
            } else {
                $visaPriceInfo['urgent_price'] = \Drupal::config('evisa.adminsettings')->get('def_urgent_non_indian_price');
            }   
        }
        $urgentPrice = $visaPriceInfo['urgent_price']*$roe;
        if($roe <=0) {
            drupal_set_message(t('Rate of Exchange is not define.'), 'error');
            return $form_state->setRedirect('demo.multistep_one');
        }
        if ($visaPrice <= 0) {
            drupal_set_message(t('Price not set for your account.'), 'error');
            return $form_state->setRedirect('demo.multistep_one');
        }
        $customerCumAccount = getCumAmount($customerId);
        if ($visaPrice <= $customerCumAccount) {
            $this->store->set('country', $country_id);
            $this->store->set('purpose_travel', $purpose_travel);
            $this->store->set('visa_type', $visa_type);
            $this->store->set('nationality', $nationality);
            $this->store->set('urgent_visa', $urgent_visa);
            $this->store->set('visa_price', $visaPrice);
            $this->store->set('urgent_price', $urgentPrice);
            $this->store->set('customer_id', $customerId);
            $this->store->set('roe', $roe);
            $this->store->set('visa_fee', $visaFee);
            $this->store->set('urgent_fee', $urgentVisaFee);
            $form_state->setRedirect('demo.multistep_two');
        } else {
            drupal_set_message(t('Insufficiant balance to post visa. To recharge your account, please contact Finance.'), 'error');
            return $form_state->setRedirect('demo.multistep_one');
        }
    }

}
