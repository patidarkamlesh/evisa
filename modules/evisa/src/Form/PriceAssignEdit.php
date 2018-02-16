<?php

namespace Drupal\evisa\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class PriceAssignEdit extends FormBase {
    /**
     * Country Purpose Visa Form ID
     * @return string
     */
    public function getFormId() {
        return 'price_assignment_edit';
    }
    /**
     * Price Assignment Form for Add / Edit
     * @param array $form
     * @param FormStateInterface $form_state
     * @return array $form
     */
    public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
        $priceAssigned = getPriceAssigned($id);
        if (!empty($priceAssigned['id'])) {
            $form['price_assign_id'] = [
                '#type' => 'hidden',
                '#value' => $priceAssigned['id'],
            ];
            $form['customer_name'] = [
                '#type' => 'item',
                '#title' => $this->t('Customer Name'),
                '#markup' => $priceAssigned['customer_name'],
            ];
            $form['customer_id'] = [
                '#type' => 'hidden',
                '#value' => $priceAssigned['customer_name'],
            ];
            $form['country'] = [
                '#type' => 'item',
                '#title' => $this->t('Country'),
                '#markup' => $priceAssigned['country_name'],
            ];
            $form['country_id'] = [
                '#type' => 'hidden',
                '#value' => $priceAssigned['country_name'],
            ];
            $form['purpose_travel'] = [
                '#type' => 'item',
                '#title' => $this->t('Purpose of travel'),
                '#markup' => $priceAssigned['purpose_travel'],
            ];
            $form['purpose_id'] = [
                '#type' => 'hidden',
                '#value' => $priceAssigned['purpose_travel'],
            ];
            $form['visa_type'] = [
                '#type' => 'item',
                '#title' => $this->t('Type of Visa'),
                '#markup' => $priceAssigned['visa_type'],
            ];
            $form['visa_type_id'] = [
                '#type' => 'hidden',
                '#value' => $priceAssigned['visa_type'],
            ];
            $form['old_price'] = [
                '#type' => 'hidden',
                '#value' => $priceAssigned['price'],
            ];
            $form['price'] = [
                '#type' => 'number',
                '#title' => 'Price',
                '#description' => 'Enter Price',
                '#min' => 0,
                '#required' => TRUE,
                '#default_value' => $priceAssigned['price'],
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
                '#markup' => $this->t('System didn\'t found any price assignment. Please contact system adminstrator.'),
            ];
        }

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
        $customer_name = $form_state->getValue('customer_id');
        $country_name = $form_state->getValue('country');
        $purpose_type = $form_state->getValue('purpose_id');
        $visa_type = $form_state->getValue('visa_type_id');
        $price = $form_state->getValue('price');
        $old_price = $form_state->getValue('old_price');
        $price_assign_id = $form_state->getValue('price_assign_id');
        //Update Price Information
        $updateQuery = \Drupal::database()->update('price_assignment')
                       ->fields([
                           'price' => $price,
                           'updated_user_id' => \Drupal::currentUser()->id(),
                           'updated' => date('Y-m-d H:i:s'),
                       ])
                       ->condition('id', $price_assign_id)
                       ->execute();
        //Send Email for update
        $mailManager = \Drupal::service('plugin.manager.mail');
 $module = 'evisa';
 $key = 'price_assign_update';
 $to = 'mail.kamleshpatidar@gmail.com';
 $params['customer_name'] = $customer_name;
 $params['country_name'] = $country_name;
 $params['purpose_type'] = $purpose_type;
 $params['visa_type'] = $visa_type;
 $params['old_price'] = $old_price;
 $params['price'] = $price;
 
 $langcode = '';
 $send = true;
 $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
 if ($result['result'] !== true) {
   drupal_set_message(t('There was a problem sending your message and it was not sent.'), 'error');
 }
 else {
   drupal_set_message(t('Your message has been sent.'));
 }

        
        //$message = $this->t('Price Assignment updated for Customer');
       // drupal_set_message($message);
        //Redirect to Type of visa Page
        $form_state->setRedirect('evisa.priceassignment');
    }

}
