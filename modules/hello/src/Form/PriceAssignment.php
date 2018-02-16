<?php

namespace Drupal\hello\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class PriceAssignment extends FormBase {
    
    public function getFormId() {
        return 'price_assignment';
    }
    
    public function buildForm(array $form, FormStateInterface $form_state) {
        $form['my_entity_autocomplete'] = array(
            '#type' => 'entity_autocomplete',
            '#target_type' => 'node',
            '#selection_settings' => [
              'target_bundles' => ['customer']
            ]
          );
    }
    
    public function submitForm(array &$form, FormStateInterface $form_state) {
        
    }
}
