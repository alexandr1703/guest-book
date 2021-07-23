<?php

namespace Drupal\alexandr_guest\Form;

use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\file\Entity\File;


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

  /**
   * Build form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['messages-name'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => [
        'class' => ['form-message'],
      ],
    ];

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your name'),
      '#required' => TRUE,
      '#maxlength' => 32,
      '#minlength' => 2,
      '#description' => t('More than 1 symbol.'),
      '#ajax' => [
        'callback' => '::setMessageName',
        'event' => 'change',
      ],
    ];

    $form['avatar'] = [
      '#type' => 'managed_file',
      '#title' => t('Your Ava'),
      '#description' => t('Only png, jpg and jpeg.Max size 2Mb.'),
      '#upload_validators' => [
        'file_validate_extensions' => ['png jpg jpeg'],
        'file_validate_size' => [2097152],
      ],
      '#theme' => 'image_widget',
      '#preview_image_style' => 'medium',
      '#upload_location' => 'public://guest_image',
    ];
    $form['messages-email'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => [
        'class' => ['form-message-email'],
      ],
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Your email'),
      '#required' => TRUE,
      '#description' => t('example@gmail.com'),
      '#attributes' => [
        'placeholder' => t('example@gmail.com'),
      ],
      '#ajax' => [
        'callback' => '::setMessageEmail',
        'event' => 'change',
      ],
    ];
    $form['messages-phone'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => [
        'class' => ['form-message-phone'],
      ],
    ];
    $form['phone_number'] = [
      '#type' => 'tel',
      '#title' => $this->t('Your phone'),
      '#required' => TRUE,
      '#maxlength' => 10,
      '#description' => t('Only number.'),
      '#attributes' => [
        'placeholder' => t('6666666666'),
      ],
      '#ajax' => [
        'callback' => '::setMessagePhone',
        'event' => 'change',
      ],
    ];
    $form['messages-comment'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => [
        'class' => ['form-message-comment'],
      ],
    ];
    $form['comment'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Your comment'),
      '#required' => TRUE,
      '#ajax' => [
      'callback' => '::setMessageComment',
      'event' => 'change',
    ],
    ];

    $form['image'] = [
      '#type' => 'managed_file',
      '#title' => t('Your Image'),
      '#description' => t('Only png, jpg and jpeg.Max size 5Mb.'),
      '#upload_validators' => [
        'file_validate_extensions' => ['png jpg jpeg'],
        'file_validate_size' => [5242880],
      ],
      '#theme' => 'image_widget',
      '#preview_image_style' => 'medium',
      '#upload_location' => 'public://guest_image',
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add comment'),
      '#ajax' => [
        'callback' => '::ajaxSubmitCallback',
        'event' => 'click',
        'wrapper' => 'guest_formm',
        'progress' => [
          'type' => 'throbber',
          'message' => t('Verifying...'),
        ],
      ],
    ];
    return $form;
  }

  /**
   * AJAX validation for name.
   */
  public function setMessageName(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $name = $form_state->getValue('name');
    if (strlen($name) <= 1) {
      $response->addCommand(
        new HtmlCommand(
          '.form-message',
          '<div class="my-message-error">' . $this->t('Enter correct name.')
        )
      );
    }
    else {
      $response->addCommand(
        new HtmlCommand(
          '.form-message',
          '<div class="my-message">' . $this->t('Name: Ok!')
        )
      );
    }
    return $response;
  }

  /**
   * AJAX validation for phone.
   */
  public function setMessagePhone(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $phone = $form_state->getValue('phone_number');
    if ((!preg_match('/^[0-9]{10}$/', $phone))) {
      $response->addCommand(
        new HtmlCommand(
          '.form-message-phone',
          '<div class="my-message-error">' . $this->t('Enter correct phone number in format: 1234567890 ')
        )
      );
    }
    else {
      $response->addCommand(
        new HtmlCommand(
          '.form-message-phone',
          '<div class="my-message">' . $this->t('Phone: Ok!')
        )
      );
    }
    return $response;
  }

  /**
   * AJAX validation for email.
   */
  public function setMessageEmail(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $email = $form_state->getValue('email');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[A-Za-z-_]+[@]+[a-z]{2,12}+[.]+[a-z]{2,7}+$/', $email)) {
      $response->addCommand(
        new HtmlCommand(
          '.form-message-email',
          '<div class="my-message-error">' . $this->t('Enter correct email in format: example@gmail.com.')
        )
      );
    }
    else {
      $response->addCommand(
        new HtmlCommand(
          '.form-message-email',
          '<div class="my-message">' . $this->t('Email: Ok!')
        )
      );
    }
    return $response;
  }

  /**
   * AJAX validation for comment.
   */
  public function setMessageComment(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $comment = $form_state->getValue('comment');
    if (strlen($comment) <= 1) {
      $response->addCommand(
        new HtmlCommand(
          '.form-message-comment',
          '<div class="my-message-error">' . $this->t('Enter longer comment, be more creative)')
        )
      );
    }
    else {
      $response->addCommand(
        new HtmlCommand(
          '.form-message-comment',
          '<div class="my-message">' . $this->t('Good comment!')
        )
      );
    }
    return $response;
  }

  /**
   * Validation form.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    $name = $form_state->getValue('name');
    $email = $form_state->getValue('email');
    $phone = $form_state->getValue('phone_number');
    $comment = $form_state->getValue('comment');
    $image = $form_state->getValue('image');
    $avatar = $form_state->getValue('avatar');
    $errorArray = [0, 0, 0];
    if (strlen($name) > 1) {
      $errorArray[0] = 1;
    }
    if (strlen($name) <= 1){
      $errorArray[0] = 0;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[A-Za-z-_]+[@]+[a-z]{2,12}+[.]+[a-z]{2,7}+$/', $email)) {
      $errorArray[1] = 0;
    }
    else {
      $errorArray[1] = 1;
    }
    if  (!preg_match('/^[0-9]{10}/', $phone)) {
      $errorArray[2] = 0;
    }
    else {
      $errorArray[2] = 1;
    }
    if (strlen($comment) > 1){
      $errorArray[3] = 1;
    }
    if ($errorArray[0] == 1 && $errorArray[1] == 1 && $errorArray[2] == 1 && $errorArray[3] == 1) {
      return TRUE;
    }
  }

  /**
   * Ajax callback validate.
   */
  public function ajaxSubmitCallback(array &$form, FormStateInterface $form_state) {
    $ajax_response = new AjaxResponse();
    $message = [
      '#theme' => 'status_messages',
      '#message_list' => $this->messenger()->all(),
      '#status_headings' => [
        'status' => t('Status message'),
        'error' => t('Error message'),
        'warning' => t('Warning message'),
      ],
    ];
    \Drupal::messenger()->addMessage($this->t('Thanks for your comment'));
    $messages = \Drupal::service('renderer')->render($message);
    $ajax_response->addCommand(new HtmlCommand('#form-system-messages',  $messages));
    $this->messenger()->deleteAll();
    $out = "";
    if ($this->validateForm($form, $form_state) == TRUE) {
      $url = Url::fromRoute('alexandr_guest.form', []);
      if ($url->isRouted()) {
        $out = $url->toString();
      }
      \Drupal::messenger()->addMessage($this->t('Thanks for your comment'));
      $ajax_response->addCommand(new RedirectCommand($out));
    }
    return $ajax_response;
  }

  /**
   * Submit form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $time = time();
    if ($this->validateForm($form, $form_state) == TRUE) {
      $connection = \Drupal::service('database');
      if (isset($form_state->getValue('image')[0])) {
        $file = File::load($form_state->getValue('image')[0]);
        $file->setPermanent();
        $file->save();
      }
      else {
        $form_state->getValue('image')[0] = 0;
      }
      if (isset($form_state->getValue('avatar')[0])) {
        $ava = File::load($form_state->getValue('avatar')[0]);
        $ava->setPermanent();
        $ava->save();
      }
      else {
        $form_state->getValue('avatar')[0] = 0;
      }
      $connection->insert('alexandr_guest')
        ->fields([
          'uid' => $this->currentUser()->id(),
          'name' => $form_state->getValue('name'),
          'avatar' => $form_state->getValue('avatar')[0],
          'created' => date($time),
          'comment' => $form_state->getValue('comment'),
          'phone' => $form_state->getValue('phone_number'),
          'email' => $form_state->getValue('email'),
          'image' => $form_state->getValue('image')[0],
        ])
        ->execute();
    }

  }
}
