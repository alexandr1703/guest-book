<?php

namespace Drupal\alexandr_guest\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class AlexandrController.
 */
class GuestController extends  ControllerBase {
  /**
   * Form build interface.
   *
   * @var Drupal\Core\Form\FormBase
   */
  protected $formBuilder;

  public function createForm(){
    $mainform = \Drupal::formBuilder()->getForm('Drupal\alexandr_guest\Form\GuestForm');
    return $mainform;
  }
}
