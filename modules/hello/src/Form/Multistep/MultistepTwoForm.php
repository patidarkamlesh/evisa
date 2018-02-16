<?php

/**
 * @file
 * Contains \Drupal\hello\Form\Multistep\MultistepTwoForm.
 */

namespace Drupal\hello\Form\Multistep;

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
    if(empty($this->store->get('name'))) {
        $response = new RedirectResponse(\Drupal::url('welcome'));
        $response->send();
        return;
    }
    $form['age'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Your age'),
      '#default_value' => $this->store->get('age') ? $this->store->get('age') : '',
    );

    $form['location'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Your location'),
      '#default_value' => $this->store->get('location') ? $this->store->get('location') : '',
    );

    $form['actions']['previous'] = array(
      '#type' => 'link',
      '#title' => $this->t('Previous'),
      '#attributes' => array(
        'class' => array('button'),
      ),
      '#weight' => 0,
      '#url' => Url::fromRoute('hello.multistep_one'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->store->set('age', $form_state->getValue('age'));
    $this->store->set('location', $form_state->getValue('location'));

    // Save the data
    parent::saveData();
    $form_state->setRedirect('hello.multistep_one');
  }
}
