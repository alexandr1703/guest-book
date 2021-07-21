<?php

namespace Drupal\alexandr_guest\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;


/**
 * Defines a confirmation form to confirm deletion of something by id.
 */
class DeleteForm extends ConfirmFormBase {

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
//    $current_path = \Drupal::service('path.current')->getPath();
    $response = new RedirectResponse('/alexandr/guest');
    $response->send();
    \Drupal::messenger()->addMessage($this->t('Entry deleted successfully'), 'status', TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() : string {
    return "confirm_delete_form";
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return Url::fromRoute('alexandr_guest.form');
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Do you want to delete %id?', ['%id' => $this->ctid]);

  }

}
