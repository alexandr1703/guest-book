<?php

namespace Drupal\alexandr_guest\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\file\Entity\File;
use Drupal\file\FileInterface;
use Drupal\Core\Url;

/**
 * Class GuestController.
 */
class GuestController extends  ControllerBase {
  /**
   * Form build interface.
   *
   * @var Drupal\Core\Form\FormBase
   */
  protected $formBuilder;

  /**
   * Return instance.
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->formBuilder = $container->get('form_builder');
    return $instance;
  }

  public function createForm(){
    $mainform = \Drupal::formBuilder()->getForm('Drupal\alexandr_guest\Form\GuestForm');
    return $mainform;
  }

  /**
   * Get all records from database.
   *
   * @return array
   *   A simple array.
   */
  public function load() {
    $query = Database::getConnection()->select('alexandr_guest', 'a');
    $query->fields('a', ['image', 'avatar', 'name', 'created', 'email', 'phone', 'id', 'comment']);
    $result = $query->execute()->fetchAll();
    return $result;
  }



  /**
   * Render table of contents.
   *
   * @return array
   */
  public function report() {
    $form = $this->createForm();
    $info = json_decode(json_encode($this->load()), TRUE);
    $info = array_reverse($info);
    $rows = [];
    foreach ($info as &$value) {
      $fid = $value['image'];
      $file = File::load($fid);
      $avatarfid = $value['avatar'];
      $avatarfile = File::load($avatarfid);
      if ($file instanceof FileInterface) {
        $value['image'] = [
          '#type' => 'image',
          '#theme' => 'image_style',
          '#style_name' => 'large',
          '#alt' => 'comment_image',
          '#attributes' => [
            'class' => ['image-overlay'],
          ],
          '#uri' => $file->getFileUri(),
        ];
        $renderer = \Drupal::service('renderer');
        $value['image'] = $renderer->render($value['image']);
      }
      if ($avatarfile instanceof FileInterface) {
        $value['avatar'] = [
          '#type' => 'image',
          '#theme' => 'image_style',
          '#style_name' => 'large',
          '#alt' => 'avatar_image',
          '#attributes' => [
            'class' => ['image-overlay'],
          ],
          '#uri' => $avatarfile->getFileUri(),
        ];
        $renderer = \Drupal::service('renderer');
        $value['avatar'] = $renderer->render($value['avatar']);
      }
      $time = time();
      $value['created'] = date('d-M-Y  H:i:s', $time);
      array_push($rows, $value);
      }
    return [
      '#theme' => 'guest_book',
      '#form' => $form,
      '#items' => $rows,
    ];
  }
}
