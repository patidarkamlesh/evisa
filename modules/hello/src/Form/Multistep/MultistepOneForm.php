<?php

/**
 * @file
 * Contains \Drupal\hello\Form\Multistep\MultistepOneForm.
 */

namespace Drupal\hello\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\UpdateBuildIdCommand;
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
    $form_state->disableCache();
    echo $this->store->get('favorite');
    echo "<br />";
    echo $this->store->get('model');
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your name'),
      '#default_value' => $this->store->get('name') ? $this->store->get('name') : '',
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Your email address'),
      '#default_value' => $this->store->get('email') ? $this->store->get('email') : '',
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
                'wrapper' => 'dit-fields-test',
            ),
            '#suffix' => '<div id="kamlesh"></div>'
        ];


    
    $form['model']= [
      '#type' => 'select',
      '#title' => $this->t('Select a Model'),
      '#empty_option' => $this->t('-select-'),
      //'#validated' => TRUE,
      '#attributes' => ['id' => 'edit-fields-test']  
       
    ];
    
    
    $form['actions']['submit']['#value'] = $this->t('Next');

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->store->set('email', $form_state->getValue('email'));
    $this->store->set('name', $form_state->getValue('name'));
    $form_state->setRedirect('hello.multistep_two');
  }

    public function validateForm(array &$form, FormStateInterface $form_state) {

    }

function ajax_select_callback($form, $form_state) {
  $values = array("<option value = 1>Kamlesh</option>");
  $values = array('12335');
  $response = new AjaxResponse();
  //$response->addCommand(new UpdateBuildIdCommand($form['#build_id_old'], $form['#build_id']));
  $rendered_form = \Drupal::service('renderer')->renderRoot($values);
  //$response->addCommand(new InvokeCommand('#edit-fields-test','html' ,$values));
  $response->addCommand(new InvokeCommand('#edit-email','val' ,$values));
  return $response;
}

  
}
