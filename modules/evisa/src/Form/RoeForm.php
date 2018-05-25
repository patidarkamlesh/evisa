<?php

namespace Drupal\evisa\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class RoeForm extends Formbase {

    /**
     * ROE form ID
     */
    public function getFormId() {
        return 'roe_form';
    }

    /**
     * Rate of Exchange Form for Add / Edit
     * @param array $form
     * @param FormStateInterface $form_state
     * @return array $form
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $countries = getAllCountry(TRUE);
        $editId = \Drupal::request()->query->get('rid');
        if (isset($editId) && is_numeric($editId)) {
            $record = getRoeFromId((int) $editId);
            $hiddenId = $record['rid'];
        } else {
            $hiddenId = 0;
        }
        $form['editid'] = [
            '#type' => 'hidden',
            '#value' => $hiddenId
        ];
        $form['cid'] = [
            '#type' => 'select',
            '#title' => $this->t('Country'),
            '#description' => $this->t('Select Country'),
            '#options' => $countries,
            '#required' => TRUE,
            '#default_value' => (isset($record['cid'])) ? $record['rid'] : ''
        ];
        $form['roe'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Rate of Exchange'),
            '#description' => $this->t('Enter Rate of Exchange'),
            '#required' => TRUE,
            '#default_value' => (isset($record['roe'])) ? $record['roe'] : '',
            '#element_validate' => [
                [
                    $this, 'validateRoe'
                ]
            ]
        ];
        $form['actions'] = [
            '#type' => 'actions',
        ];

        // Add a submit button that handles the submission of the form.
        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Submit'),
            '#description' => $this->t('Submit'),
        ];
        return $form;
    }

    /**
     * To validate Rate of Exchange textfield
     */
    public function validateRoe($element, &$form_state, $form) {
        if (!is_numeric($element['#value'])) {
            $form_state->setErrorByName('roe', 'Rate of exchange should be numeric value');
        }
    }

    /**
     * Validate Rate of Exchange
     * @param array $form
     * @param FormStateInterface $form_state
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        if (!empty($form_state->getValue('cid'))) {
            $rid = \Drupal::database()->select('rate_exchange', 're')
                            ->fields('re', ['rid'])
                            ->condition('cid', $form_state->getValue('cid'))
                            ->execute()->fetchField();
            if (!empty($rid)) {
                $form_state->setErrorByName('cid', $this->t('Rate of Exchange for this country is already available.'));
            }
        }
    }

    /**
     * Insert / update country into database 
     * @param array $form
     * @param FormStateInterface $form_state
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $cid = $form_state->getValue('cid');
        $roe = $form_state->getValue('roe');
        $roeId = $form_state->getValue('editid');
        //Update if Country ID set else Insert
        if (isset($roeId) && !empty($roeId)) {
            $result = \Drupal::database()->update('rate_exchange')
                    ->fields([
                        'roe' => $roe,
                        'updated_user_id' => \Drupal::currentUser()->id(),
                        'updated' => date('Y-m-d H:i:s'),
                    ])
                    ->condition('rid', $roeId)
                    ->execute();
            $message = $this->t('Rate of Exchange has been updated');
        } else {
            $query = \Drupal::database()->insert('rate_exchange')
                    ->fields([
                        'cid' => $cid,
                        'roe' => $roe,
                        'created_user_id' => \Drupal::currentUser()->id(),
                        'created' => date('Y-m-d H:i:s'),
                    ])
                    ->execute();
            $message = 'Rate of Exchange has been added';
        }
        drupal_set_message($message);
        //Redirect to Rate of Exchange Page
        $form_state->setRedirect('evisa.roe');
    }

}
