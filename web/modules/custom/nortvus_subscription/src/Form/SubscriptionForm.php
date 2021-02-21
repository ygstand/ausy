<?php

namespace Drupal\nortvus_subscription\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
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
  public function buildForm(array $form, FormStateInterface $form_state, $subscription_id = NULL) {
    // Getting taxonomy terms of the category vocabulary.
    $categories_terms = $this->categoryManager->getAllTerms('category');
    // Contains options to be used in the category form element.
    $category_options = [];
    /** @var \Drupal\taxonomy\Entity\Term $categories_term */
    foreach ($categories_terms as $categories_term) {
      $category_options[$categories_term->id()] = $categories_term->getName();
    }

    $form['subscription_id'] = [
      '#type' => 'hidden',
      '#value' => $subscription_id,
    ];
    // Contains a subscription data.
    $subscription = [];
    // Getting the subscription in case the $subscription_id variable
    // is not empty.
    if ($subscription_id) {
      $subscription = $this->subscription->getSubscription($subscription_id);
      // Prevents showing the subscription edit form in case the subscription
      // does not exists.
      if (empty($subscription)) {
        return [];
      }
      // @todo Clarify if multiple select is required and if so, then modify this.
      $form['category'] = [
        '#type' => 'hidden',
        '#value' => $subscription['category'],
      ];
    }
    else {
      // @todo Clarify if multiple select is required and if so, then modify this.
      $form['category'] = [
        '#type' => 'select',
        '#default_value' => isset($subscription['category']) ? $subscription['category'] : '',
        '#options' => $category_options,
        '#empty_option' => $this->t('Select Category'),
        '#empty_value' => '',
        '#required' => TRUE,
      ];
    }

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#maxlength' => 255,
      '#default_value' => isset($subscription['name']) ? $subscription['name'] : '',
      '#required' => TRUE,
    ];
    $form['mail'] = [
      '#type' => 'email',
      '#title' => $this->t('E-mail'),
      '#default_value' => isset($subscription['mail']) ? $subscription['mail'] : '',
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
      'created' => time(),
      'edited' => time(),
    ];

    // Checking if subscription id is passed and if so, updates the existing
    // subscription instead of creating a new one.
    if ($values['subscription_id']) {
      // Getting the subscription.
      $subscription = $this->subscription->getSubscription($values['subscription_id']);
      // Prepares values to update the subscription.
      $data = [
        'name' => $values['name'],
        'mail' => $values['mail'],
        'category' => $subscription['category'],
        'created' => $subscription['created'],
        'edited' => time(),
      ];

      // Updates the existing subscription.
      $subscriptions = $this->subscription->editSubscription($values['subscription_id'], $data);
      // Url of the page the user is to be redirected to. It's assumed that
      // if the subscription edit form is open in modal form on the subscriptions
      // page and once the user has submitted the form they should be
      // redirected back to the subscriptions page.
      $url = Url::fromRoute(
        'nortvus_subscription.subscription_controller_subscriptions_page',
        ['subscription_id' => $values['subscription_id']]
      );
      $form_state->setRedirectUrl($url);

    }
    else {
      // Creates new subscription.
      $subscriptions = $this->subscription->createSubscription($data);
    }
    // Saves changes.
    $this->subscription->save($subscriptions);
  }

}
