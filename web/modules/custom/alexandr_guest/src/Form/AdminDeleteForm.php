<?php

namespace Drupal\alexandr_guest\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;


/**
 * Defines a confirmation form to confirm deletion of something by id.
 */
class AdminDeleteForm extends ConfirmFormBase {

  /**
   * ID of the item to delete.
   *
   * @var int
   */
  protected $ctid = 0;

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $cid = NULL) {
    $this->ctid = $cid;
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $connection = \Drupal::service('database');
    $result = $connection->delete('alexandr_guest');
    $result->condition('id', $this->ctid);
    $result->execute();
    $response = new RedirectResponse('/admin/structure/guest_book');
    $response->send();
    \Drupal::messenger()->addMessage($this->t('Entry deleted successfully'), 'status', TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() : string {
    return "admin_confirm_delete_form";
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return Url::fromRoute('alexandr_guest.admin_form');
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Do you want to delete this record?');

  }

}
