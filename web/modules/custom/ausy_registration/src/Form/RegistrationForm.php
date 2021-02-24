<?php

namespace Drupal\ausy_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class RegistrationForm.
 */
class RegistrationForm extends FormBase {

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Drupal\Core\Routing\CurrentRouteMatch definition.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $currentRouteMatch;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->entityTypeManager = $container->get('entity_type.manager');
    $instance->currentRouteMatch = $container->get('current_route_match');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'registration_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $department = NULL) {
    /** @var \Drupal\ausy_registration\Entity\DepartmentInterface $department */
    if (!empty($department)) {
      $form['department'] = [
        '#type' => 'hidden',
        '#value' => $department->id(),
      ];
    }

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name of the employee'),
      '#maxlength' => 255,
      '#default_value' => '',
      '#required' => TRUE,
    ];
    $form['one_plus'] = [
      '#type' => 'radios',
      '#title' => $this->t('One plus'),
      '#maxlength' => 255,
      '#options' => [
        1 => $this->t('Yes'),
        0 => $this->t('No'),
      ],
      '#required' => TRUE,
    ];
    $form['amount_of_kids'] = [
      '#type' => 'number',
      '#title' => $this->t('Amount of kids'),
      '#min' => 0,
      '#required' => TRUE,
    ];
    $form['amount_of_vegetarians'] = [
      '#type' => 'number',
      '#title' => $this->t('Amount of vegetarians'),
      '#min' => 0,
      '#required' => TRUE,
    ];
    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('E-mail'),
      '#default_value' => '',
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
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    // @todo Use Dependency Injection.
    if ($values['email'] != '') {
      if (!\Drupal::service('email.validator')->isValid($values['email'])) {
        $form_state->setErrorByName('email', t('The email address %mail is not valid.', ['%mail' => $values['email']]));
      }
      // Checking if there's already a referenced registration to the given
      // email address.
      else {
        // @todo Create a service to provide this feature.
        $node_storage = $this->entityTypeManager->getStorage('node');
        $registrations_array = $node_storage->loadByProperties([
          'type' => 'registration',
          'field_email' => $values['email'],
        ]);
        if (!empty($registrations_array)) {
          $form_state->setErrorByName('email', t('You are not able to register twice with the given email'));
        }
      }
    }
    if (!empty($values['amount_of_vegetarians'])) {
      // Getting the total amount of people.
      // Total amount of people can not be less the 1.
      $total_amount_of_people = 1 + $values['amount_of_kids'];
      if ($values['one_plus']) {
        $total_amount_of_people += 1;
      }
      if ($values['amount_of_vegetarians'] > $total_amount_of_people) {
        $form_state->setErrorByName('amount_of_vegetarians', t('Amount of vegetarians can not be higher than the total amount of people'));
      }
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    // Prevents creating the registration in case the department
    // is not specified.
    if (!empty($values['department'])) {
      /** @var \Drupal\Core\Entity\EntityStorageInterface $node_storage */
      $node_storage = $this->entityTypeManager->getStorage('node');
      /** @var \Drupal\node\NodeInterface $node */
      $node = $node_storage->create([
        'title' => $values['name'],
        'type' => 'registration',
        'field_one_plus' => $values['one_plus'],
        'field_amount_of_kids' => $values['amount_of_kids'],
        'field_amount_of_vegetarians' => $values['amount_of_vegetarians'],
        'field_email' => $values['email'],
        'field_department' => $values['department'],
      ]);
      $node->save();
      // @todo Use DI.
      \Drupal::messenger()->addMessage($this->t('Registration is complete'));
    }
    else {
      // @todo Use DI.
      \Drupal::messenger()->addWarning($this->t('Registration is not complete as the department is not specified'));
    }
  }

}
