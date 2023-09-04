<?php

/**
 * @file
 * A Custom Contact Us form
 */

namespace Drupal\custom_form\Form;

use Drupal\Core\Form\FormBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormStateInterface;

class CustomContactUsForm extends FormBase
{

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'custom_contact_us_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $form['full_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Full Name'),
      '#required' => TRUE,
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email Address'),
      '#required' => TRUE,
    ];

    $form['phone'] = [
      '#type' => 'tel',
      '#title' => $this->t('Phone Number'),
    ];

    $form['message'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Message'),
      '#maxlength' => 250,
      '#required' => TRUE,
    ];



    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {

    // Retrving the value of the forms
    $params = [
      'message' => $form_state->getValue('message'),
      'full_name' => $form_state->getValue('full_name'),
      'email' => $form_state->getValue('email'),
      'phone' => $form_state->getValue('phone'),
    ];

    // Sending mail to admin
    $to = \Drupal::config('system.site')->get('mail'); 
    \Drupal::service('plugin.manager.mail')->mail('custom_form', 'admin_mail', $to, $params);

    // Getting value of the user submitted mail
    $user_email = $form_state->getValue('email');
    
    // Sending mail to user
    $params = [];
    \Drupal::service('plugin.manager.mail')->mail('custom_form', 'user_mail', $user_email, $params);

    // Form Submission Message.
    \Drupal::messenger()->addMessage($this->t('Thank you for your submission'));
  }
}
