<?php

namespace Drupal\hello\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\File;

class UploadTest extends FormBase {

    public function getFormId() {
        return 'upload_test_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state) {
       $currentuserid = \Drupal::currentUser()->id(); 
       $maxup = 3;
       
       for($i = 1; $i <= $maxup; $i++) {
            $form['uploadfile_'.$i] = [
                '#type' => 'managed_file',
                 '#title' => $this->t('Managed <em>@type</em>', ['@type' => 'file & butter']),
                 '#upload_location' => 'public://test/'.$currentuserid,
                 '#progress_message' => $this->t('Please wait...'),
                 //'#extended' => (bool) $extended,
                 //'#upload_validators' => array('file_validate_extensions' => array('pdf doc docx')),

                 //'#size' => 13,
                 '#multiple' => FALSE,
            ];  
           
       }

        // Group submit handlers in an actions element with a key of "actions" so
        // that it gets styled correctly, and so that other modules may add actions
        // to the form.
        $form['actions'] = [
            '#type' => 'actions',
        ];

        // Add a submit button that handles the submission of the form.
        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Submit'),
            '#description' => $this->t('Submit, #type = submit'),
        ];
       
       return $form; 
    }

    public function validateForm(array &$form, FormStateInterface $form_state) {
        
    }

    public function submitForm(array &$form, FormStateInterface $form_state) {
        $allfiles =  $form_state->getValues();
        print_r($allfiles); exit;
        $uploadfilenew = $form_state->getValue('uploadfile');
        $file1 = \Drupal\file\Entity\File::load($uploadfilenew[0]);
        $file1->setPermanent();
        $file1->save();
        $message = $this->t('File has been uploaded successfully');
        drupal_set_message($message);
    }

}
