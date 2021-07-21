<?php

namespace Drupal\alexandr_guest\Form;

use Drupal\Core\Ajax\InsertCommand;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\Textfield;
use Drupal\Core\Render\Element\Tel;
use Drupal\Core\Url;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\file\Entity\File;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * Main form.
 */
class EditForm extends FormBase {

  /**
   * Id recordd.
   *
   * @var ctid
   * */
  protected $ctid = 0;

  /**
   * Return form.
   */
  public function getFormId() {
    return 'edit_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state ,$cid = NULL) {
    $query = \Drupal::database();
    $data = $query->select('alexandr_guest', 'a')
            ->fields('a',[ 'image' , 'avatar', 'name', 'created', 'email', 'phone', 'id', 'comment'])
            ->condition('a.id' , $cid, '=')
            ->execute()->fetchAll(\PDO::FETCH_OBJ);
    $form['system_messages'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#weight' => -100,
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
      '#default_value' => $data[0]->name,
      '#ajax' => [
        'callback' => '::setMessageName',
        'event' => 'change',
      ],
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Your email'),
      '#required' => TRUE,
      '#default_value' => $data[0]->email,
      '#attributes' => [
        'placeholder' => t('example@gmail.com'),
      ],
      '#ajax' => [
        'callback' => '::setMessageEmail',
        'event' => 'change',
      ],
    ];

    $form['phone_number'] = [
      '#type' => 'tel',
      '#title' => $this->t('Your phone'),
      '#required' => TRUE,
      '#default_value' => $data[0]->phone,
      '#attributes' => [
        'placeholder' => t('666-666-6666'),
      ],
      '#ajax' => [
        'callback' => '::setMessagePhone',
        'event' => 'change',
      ],
    ];

    $form['comment'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Your comment'),
      '#required' => TRUE,
      '#default_value' => $data[0]->comment,
      '#ajax' => [
        'callback' => '::setMessageComment',
        'event' => 'change',
      ],
    ];

    $form['avatar'] = [
      '#type' => 'managed_file',
      '#title' => t('Your Ava'),
      '#description' => t('Only png, jpg and jpeg.Max size 2Mb.'),
      '#default_value' => array($data[0]->avatar),
      '#upload_validators' => [
        'file_validate_extensions' => ['png jpg jpeg'],
        'file_validate_size' => [2097152],
      ],
      '#theme' => 'image_widget',
      '#preview_image_style' => 'medium',
      '#upload_location' => 'public://module_image',
    ];

    $form['image'] = [
      '#type' => 'managed_file',
      '#title' => t('Your Image'),
      '#description' => t('Only png, jpg and jpeg.Max size 5Mb.'),
      '#default_value' => array($data[0]->image),
      '#upload_validators' => [
        'file_validate_extensions' => ['png jpg jpeg'],
        'file_validate_size' => [5242880],
      ],
      '#theme' => 'image_widget',
      '#preview_image_style' => 'medium',
      '#upload_location' => 'public://module_image',
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
    $this->ctid = $cid;
    return $form;
  }

  public function setMessageName(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $name = $form_state->getValue('name');
    if (strlen($name) <= 1) {
      $response->addCommand(
        new HtmlCommand(
          '.form-message',
          '<div class="my-message">' . $this->t('Enter correct name.')
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

  public function setMessagePhone(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $phone = $form_state->getValue('phone_number');
    if (((!preg_match('/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/', $phone)) && (!preg_match('/^[0-9]{10}$/', $phone)))) {
      $response->addCommand(
        new HtmlCommand(
          '.form-message',
          '<div class="my-message">' . $this->t('Enter correct phone number in format: 666-666-6666 or 1234567890 ')
        )
      );
    }
    else {
      $response->addCommand(
        new HtmlCommand(
          '.form-message',
          '<div class="my-message">' . $this->t('Phone: Ok!')
        )
      );
    }
    return $response;
  }
  public function setMessageEmail(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $email = $form_state->getValue('email');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[A-Za-z-_]+[@]+[a-z]{2,12}+[.]+[a-z]{2,7}+$/', $email)) {
      $response->addCommand(
        new HtmlCommand(
          '.form-message',
          '<div class="my-message">' . $this->t('Enter correct email in format: example@gmail.com.')
        )
      );
    }
    else {
      $response->addCommand(
        new HtmlCommand(
          '.form-message',
          '<div class="my-message">' . $this->t('Email: Ok!')
        )
      );
    }
    return $response;
  }

  public function setMessageComment(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $comment = $form_state->getValue('comment');
    if (strlen($comment) <= 1) {
      $response->addCommand(
        new HtmlCommand(
          '.form-message',
          '<div class="my-message">' . $this->t('Enter longer comment, be more creative)')
        )
      );
    }
    else {
      $response->addCommand(
        new HtmlCommand(
          '.form-message',
          '<div class="my-message">' . $this->t('Good comment!')
        )
      );
    }
    return $response;
  }

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
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[A-Za-z-_]+[@]+[a-z]{2,12}+[.]+[a-z]{2,7}+$/', $email)) {
      $errorArray[1] = 0;
    }
    else {
      $errorArray[1] = 1;
    }
    if (strlen($phone) > 0 && ((!preg_match('/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/', $phone)) && (!preg_match('/^[0-9]{10}/', $phone)))) {
      $errorArray[2] = 0;
    }
    else {
      $errorArray[2] = 1;
    }
    if (strlen($comment) > 1){
      $errorArray[3] = 1;
    }
    if ($errorArray[0] == 1 && $errorArray[1] == 1 && $errorArray[2] == 1 && $errorArray[3] == 1) {
//      \Drupal::messenger()->addMessage($this->t('Thanks for your comment')));
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
      $ajax_response->addCommand(new RedirectCommand($out));
    }
    return $ajax_response;
  }

  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    if ($this->validateForm($form, $form_state) == TRUE) {
      $connection = \Drupal::service('database');
      $file = File::load($form_state->getValue('image')[0]);
      $file->setPermanent();
      $file->save();
      $avatarfile = File::load($form_state->getValue('avatar')[0]);
      $avatarfile->setPermanent();
      $avatarfile->save();
      $connection->update('alexandr_guest')
        ->condition('id', $this->ctid)
        ->fields([
          'name' => $form_state->getValue('name'),
          'avatar' => $form_state->getValue('avatar')[0],
          'comment' => $form_state->getValue('comment'),
          'phone' => $form_state->getValue('phone_number'),
          'email' => $form_state->getValue('email'),
          'image' => $form_state->getValue('image')[0],
        ])
        ->execute();
      \Drupal::messenger()->addMessage($this->t('Form Edit Successfully'), 'status', TRUE);
    }

  }
}
