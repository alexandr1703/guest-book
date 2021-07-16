<?php

namespace Drupal\alexandr_guest\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Main form.
 */
class GuestForm extends FormBase {

  /**
   * Return form.
   */
  public function getFormId() {
    return 'quest_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your name'),
      '#required' => TRUE,
      '#maxlength' => 32,
      '#minlength' => 2,
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Your email'),
      '#required' => TRUE,
    ];

    $form['phone_number'] = [
      '#type' => 'texfield',
      '#title' => $this->t('Your phone'),
      '#required' => TRUE,
    ];

    $form['comment'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Your comment'),
      '#required' => TRUE,
    ];

    $form['image'] = [
      '#type' => 'managed_file',
      '#title' => t('Your Ava'),
      '#description' => t('Only png, jpg and jpeg.Max size 2Mb.'),
      '#upload_validators' => [
        'file_validate_extensions' => ['png jpg jpeg'],
        'file_validate_size' => [2097152],
      ],
      '#theme' => 'image_widget',
      '#preview_image_style' => 'medium',
      '#upload_location' => 'public://module_image',
      '#required' => TRUE,
    ];

    $form['image'] = [
      '#type' => 'managed_file',
      '#title' => t('Your Ava'),
      '#description' => t('Only png, jpg and jpeg.Max size 5Mb.'),
      '#upload_validators' => [
        'file_validate_extensions' => ['png jpg jpeg'],
        'file_validate_size' => [5242880],
      ],
      '#theme' => 'image_widget',
      '#preview_image_style' => 'medium',
      '#upload_location' => 'public://module_image',
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add comment'),
    ];
    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state)
  {

  }
}
