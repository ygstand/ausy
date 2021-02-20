<?php

namespace Drupal\nortvus_subscription\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SubscriptionForm.
 */
class SubscriptionForm extends FormBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Category manager service.
   *
   * @var \Drupal\nortvus_subscription\TaxonomyManagerInterface
   */
  protected $categoryManager;

  /**
   * Subscription service.
   *
   * @var \Drupal\nortvus_subscription\SubscriptionInterface
   */
  protected $subscription;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->entityTypeManager = $container->get('entity_type.manager');
    $instance->categoryManager = $container->get('nortvus_subscription.category_manager');
    $instance->subscription = $container->get('nortvus_subscription.subscription');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'subscription_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Getting taxonomy terms of the category vocabulary.
    $categories_terms = $this->categoryManager->getAllTerms('category');
    // Contains options to be used in the category form element.
    $category_options = [];
    /** @var \Drupal\taxonomy\Entity\Term $categories_term */
    foreach ($categories_terms as $categories_term) {
      $category_options[$categories_term->id()] = $categories_term->getName();
    }

    // @todo Clarify if multiple select is required and if so, then modify this.
    $form['category'] = [
      '#type' => 'select',
      '#title' => t('Category'),
      '#default_value' => $this->t('Select category'),
      '#options' => $category_options,
    ];
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#maxlength' => 255,
      '#required' => TRUE,
    ];
    $form['mail'] = [
      '#type' => 'email',
      '#title' => $this->t('E-mail'),
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
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    // Prepares values to create new subscription.
    $data = [
      'name' => $values['name'],
      'mail' => $values['mail'],
      'category' => $values['category'],
    ];

    // Creates new subscription.
    $subscriptions = $this->subscription->createSubscription($data);
    $this->subscription->save($subscriptions);
  }

}
