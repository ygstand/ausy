<?php

namespace Drupal\nortvus_subscription\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SubscriptionDeleteForm.
 */
class SubscriptionDeleteForm extends FormBase {

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
    return 'subscription_delete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $subscription_id = NULL) {
    $form['subscription_id'] = [
      '#type' => 'hidden',
      '#value' => $subscription_id,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Delete'),
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
    // Getting subscription id.
    $subscription_id = $form_state->getValue('subscription_id');
    if (!empty($subscription_id)) {
      // Deletes subscription from the JSON file.
      $this->subscription->delete($subscription_id);
    }
    // Url of the page the user is to be redirected to.
    $url = Url::fromRoute('nortvus_subscription.subscription_controller_subscriptions_page');
    $form_state->setRedirectUrl($url);
  }

}
