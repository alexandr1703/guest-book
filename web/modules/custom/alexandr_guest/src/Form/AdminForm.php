<?php

namespace Drupal\alexandr_guest\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\file\FileInterface;

class AdminForm extends FormBase{

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'admin_form';
  }

  /**
   * Build form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $query = \Drupal::database();
    $result = $query->select('alexandr_guest', 'a')
      ->fields('a',[ 'image' , 'avatar', 'name', 'created', 'email', 'phone', 'id', 'comment'])
      ->execute()->fetchAll(\PDO::FETCH_OBJ);
    $info = json_decode(json_encode($result), TRUE);
    $info = array_reverse($info);
    $rows =[];
    $headers = [
      t('Name'),
      t('Avatar'),
      t('Comment'),
      t('Image'),
      t('Email'),
      t('Phone'),
      t('Submitted'),
      t('  '),
      t('  '),
    ];
    foreach($info as &$row){
      $id = [
        '#type' => 'hidden',
        '#value' => $row['id'],
      ];
      $row[0] = $row['name'];
      $avatarfid = $row['avatar'];
      $avatarfile = File::load($avatarfid);
      if ($avatarfile instanceof FileInterface) {
        $avatar = [
          '#type' => 'image',
          '#theme' => 'image_style',
          '#style_name' => 'thumbnail',
          '#uri' => $avatarfile->getFileUri(),
          '#width' => 100,
        ];
        $renderer = \Drupal::service('renderer');
        $avatar = $renderer->render($avatar);
        $row[1] = $avatar;
      }
      else {
        $row[1] = "{no avatar}";
      }
      $row[2] = $row['comment'];
      $fid = $row['image'];
      $file = File::load($fid);
      if ($file instanceof FileInterface) {
        $image = [
          '#type' => 'image',
          '#theme' => 'image_style',
          '#style_name' => 'thumbnail',
          '#uri' => $file->getFileUri(),
          '#width' => 100,
        ];
        $renderer = \Drupal::service('renderer');
        $image = $renderer->render($image);
        $row[3] = $image;
      }
      else {
        $row[3] = "{no image}";
      }
      $row[4] = $row['email'];
      $a = '+380 ';
      $row['phone'] = $a.$row['phone'];
      $row[5] = $row['phone'];
      $time = time();
      $row['created'] = date('d-M-Y  H:i:s', $time);
      $row[6] = $row['created'];
      $id = $row['id'];
      $row[7] = t("<a class='button-control' href='/guest/editt/$id'>Edit</a>");
      $row[8] = t("<a data-dialog-type='modal' class='button-control use-ajax' href='/guest/deletee/$id'>Delete</a>");
      $row[9] = $id;
      array_push($rows, $row);
    }
    $form['table'] = [
      '#type' => 'tableselect',
      '#header' => $headers,
      '#options' => $rows,
      '#empty' => t('Not cats yet('),
    ];
    $form['delete'] = [
      '#type' => 'submit',
      '#value' => t('Delete'),
      '#attributes' => [
        'onclick' => 'if(!confirm("Really Delete?")){return false;}',
      ],
    ];
    return $form;
  }
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $row = $form['table']['#value'];
    $connection = \Drupal::service('database');
    foreach ($row as $key => $val) {
      $result = $connection->delete('alexandr_guest');
      $result->condition('id', $form['table']['#options'][$key][9]);
      $result->execute();
    }
  }
}
