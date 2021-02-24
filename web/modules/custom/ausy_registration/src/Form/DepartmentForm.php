<?php

namespace Drupal\ausy_registration\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class DepartmentForm.
 */
class DepartmentForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $department = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $department->label(),
      '#description' => $this->t("Label for the Department."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $department->id(),
      '#machine_name' => [
        'exists' => '\Drupal\ausy_registration\Entity\Department::load',
      ],
      '#disabled' => !$department->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $department = $this->entity;
    $status = $department->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Department.', [
          '%label' => $department->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Department.', [
          '%label' => $department->label(),
        ]));
    }
    $form_state->setRedirectUrl($department->toUrl('collection'));
  }

}
