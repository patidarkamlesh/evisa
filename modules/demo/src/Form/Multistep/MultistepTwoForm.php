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
        $urgentPrice = $this->store->get('urgent_price');
        $customerId = $this->store->get('customer_id');
        if(empty($country_id) || empty($purpose_travel_id) || empty($visa_type_id) || empty($nationality_id) || empty($visaPrice)) {
            drupal_set_message('Not set form Value 1', 'error');
            return new RedirectResponse('/demo/multistep-one');
        }
        $country_name = getCountryFromId($country_id);
        $nationality = getNationalityFromId($nationality_id);
        $purpose_travel = getPurposeFromId($purpose_travel_id);
        $visa_type = getVisaTypeFromId($visa_type_id);
        $document = getDocument($country_id, $purpose_travel_id, $visa_type_id);
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
        $form['urgent_visa'] = [
            '#type' => 'item',
            '#title' => $this->t('Urgent Visa'),
            '#markup' => ($urgent_visa == 1) ? 'Yes' : 'No',
        ];
        $form['visa_price'] = [
            '#type' => 'item',
            '#markup' => number_format($visaPrice, 2),
        ];
        $form['urgent_price'] = [
            '#type' => 'item',
            '#markup' => ($urgent_visa == 1) ? number_format($urgentPrice,2) : 0.00,
        ];
        $form['total_price'] = [
            '#type' => 'item',
            '#markup' => ($urgent_visa == 1) ? number_format(($urgentPrice + $visaPrice), 2) : number_format($visaPrice,2),
        ];
        $form['document'] = [
           '#type' => 'item',
           '#markup' => $document, 
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
        $form['#theme'] = 'multistep_form_two'; 
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
            $form_state->setRedirect('demo.multistep_three');
    }

}
