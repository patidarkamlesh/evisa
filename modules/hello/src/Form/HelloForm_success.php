<?php

namespace Drupal\hello\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\HtmlCommand;

class HelloForm extends FormBase {

    public function buildForm(array $form, FormStateInterface $form_state) {

        $form['email'] = [
            '#type' => 'email',
            '#title' => $this->t('Email Address'),
            //'#default_value' => 'example@example.com',
            '#description' => $this->t('Enter your Email. It must have .com.'),
            '#required' => FALSE,
            '#ajax' => [
                'callback' => array($this, 'validateEmailAjax'),
                'event' => 'change',
                'progress' => array(
                    'type' => 'throbber',
                    'message' => t('Verifying email...'),
                ),
            ],
            '#suffix' => '<span class="email-valid-message"></span>'

        ];
        $firstoption = ['Red', 'Green', 'Blue'];

        $form['favorite'] = [
            '#type' => 'select',
            '#title' => $this->t('Favorite color'),
            '#options' => $firstoption,
            '#empty_option' => $this->t('-select-'),
            '#description' => $this->t('Which color is your favorite?'),
            '#ajax' => array(
                'event' => 'change',
                //'callback' => 'ajax_select_callback',
                'callback' => array($this, 'ajax_select_callback'),
                'wrapper' => 'dropdown_second_replace',
            ),
        ];

  //if (isset($form_state->getValue('favorite'))) {
    //$model_options = ['greean1', 'blue1', 'red1'];
    $form['model_wrapper'] = [
     '#prefix' => '<div id="dropdown_second_replace">',
     '#suffix' => '</div>',        
    ];        
        
    $form['model_wrapper2'] = [
     '#prefix' => '<div id="dropdown_second_replace2">',
     '#suffix' => '</div>',        
    ];  
    
    $form['model_wrapper']['model'] = [
      '#type' => 'select',
      '#title' => t('Select a Model'),
      '#empty_option' => $this->t('-select-'),
      '#validated' => TRUE,
      //'#options' => '',
      '#ajax' => array(
                'event' => 'change',
                //'callback' => 'ajax_select_callback',
                'callback' => array($this, 'ajax_select_callback2'),
                'wrapper' => 'dropdown_second_replace2',
            ),        
    ];
  //}
    $form['model_wrapper2']['model2'] = [
      '#type' => 'select',
      '#title' => t('Select a Model2'),
      '#empty_option' => $this->t('-select-'),
      '#validated' => TRUE,
    ];    
        
        
        $form['actions'] = [
            '#type' => 'actions',
        ];
        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Submit'),
        ];

        return $form;
    }

    public function getFormId() {
        return 'hello_form';
    }

    public function validateForm(array &$form, FormStateInterface $form_state) {
        if (!$this->validateEmail($form, $form_state)) {
            $form_state->setErrorByName('email', $this->t('Your Email Address is not correct'));
        }
    }

    public function submitForm(array &$form, FormStateInterface $form_state) {
        /*
         * This would normally be replaced by code that actually does something
         * with the title.
         */
        // Find out what was submitted.
        $values = $form_state->getValues();
        foreach ($values as $key => $value) {
            $label = isset($form[$key]['#title']) ? $form[$key]['#title'] : $key;

            // Many arrays return 0 for unselected values so lets filter that out.
            if (is_array($value)) {
                $value = array_filter($value);
            }

            // Only display for controls that have titles and values.
            if ($value && $label) {
                $display_value = is_array($value) ? preg_replace('/[\n\r\s]+/', ' ', print_r($value, 1)) : $value;
                $message = $this->t('Value for %title: %value', array('%title' => $label, '%value'
                    => $display_value));
                drupal_set_message($message);
            }
        }
    }
    
    protected function validateEmail(array &$form, FormStateInterface $form_state) {
        $email = $form_state->getValue('email');
        if (substr($email, -4) !== '.com') {
            return FALSE;
        }
        return TRUE;
    }
/**
 * Ajax callback to validate the email field.
 */
public function validateEmailAjax(array &$form, FormStateInterface $form_state) {
  $valid = $this->validateEmail($form, $form_state);
  $response = new AjaxResponse();
  if ($valid) {
    $css = ['border' => '1px solid green'];
    $message = $this->t('Email ok.');
  }
  else {
    $css = ['border' => '1px solid red'];
    $message = $this->t('Email not valid.');
  }
  $response->addCommand(new CssCommand('#edit-email', $css));
  $response->addCommand(new HtmlCommand('.email-valid-message', $message));
  return $response;
}

function ajax_select_callback($form, $form_state) {
  $form['model_wrapper']['model']['#options'] = ['red', '123']; 
  //$form['model_wrapper']['model']['#title'] = '';
  return $form['model_wrapper'];
  //return $form['model_wrapper'];
}
function ajax_select_callback2($form, $form_state) {
  $form['model_wrapper2']['model2']['#options'] = ['green', '5432']; 
  //$form['model_wrapper']['model']['#title'] = '';
  return $form['model_wrapper2'];
  //return $form['model_wrapper'];
}
}
