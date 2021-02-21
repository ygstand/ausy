<?php

namespace Drupal\nortvus_subscription\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Link;
use Drupal\nortvus_subscription\FileManagerInterface;
use Drupal\nortvus_subscription\SubscriptionInterface;
use Drupal\nortvus_subscription\TaxonomyManagerInterface;
use Drupal\taxonomy\TermInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SubscriptionController.
 */
class SubscriptionController extends ControllerBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * Category manager service.
   *
   * @var \Drupal\nortvus_subscription\TaxonomyManagerInterface
   */
  protected $categoryManager;

  /**
   * Json file manager service.
   *
   * @var \Drupal\nortvus_subscription\FileManagerInterface
   */
  protected $jsonFileManager;

  /**
   * Subscription service.
   *
   * @var \Drupal\nortvus_subscription\SubscriptionInterface
   */
  protected $subscription;

  /**
   * Constructs a BookController object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter service.
   * @param \Drupal\nortvus_subscription\TaxonomyManagerInterface $category_manager
   *   Category manager service
   * @param \Drupal\nortvus_subscription\FileManagerInterface $json_file_manager
   *   Json file manager service
   * @param \Drupal\nortvus_subscription\SubscriptionInterface $subscription
   *   Subscription service
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, DateFormatterInterface $date_formatter, TaxonomyManagerInterface $category_manager, FileManagerInterface $json_file_manager, SubscriptionInterface $subscription) {
    $this->entityTypeManager = $entity_type_manager;
    $this->dateFormatter = $date_formatter;
    $this->categoryManager = $category_manager;
    $this->jsonFileManager = $json_file_manager;
    $this->subscription = $subscription;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('date.formatter'),
      $container->get('nortvus_subscription.category_manager'),
      $container->get('nortvus_subscription.json_file_manager'),
      $container->get('nortvus_subscription.subscription')
    );
  }

  /**
   * Subscriptions page.
   *
   * @return array
   *   Return a render array.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   *   Thrown if the entity type doesn't exist.
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   *   Thrown if the storage handler couldn't be loaded.
   */
  public function subscriptionsPage() {
    $header = [
      ['data' => $this->t('Name')],
      ['data' => $this->t('E-mail')],
      ['data' => $this->t('Category')],
      ['data' => $this->t('Created')],
      ['data' => $this->t('Edit')],
      ['data' => $this->t('Delete')],
    ];

    $rows = [];
    $subscriptions = $this->subscription->getAllSubscriptions();
    foreach ($subscriptions as $subscription_id => $subscription) {
      /** @var \Drupal\taxonomy\TermStorageInterface $taxonomy_storage */
      $taxonomy_storage = $this->entityTypeManager->getStorage('taxonomy_term');
      // Contains category name.
      $category_name = '';
      if (!empty($subscription['category'])) {
        /** @var \Drupal\taxonomy\TermInterface $category_term */
        $category_term = $taxonomy_storage->load($subscription['category']);
        if ($category_term instanceof TermInterface)
        $category_name = $category_term->label();
      }
      // Formatting the subscription created timestamp.
      $created = $this->dateFormatter->format($subscription['created']);

      // Generates edit subscription link.
      $edit_link = Link::createFromRoute(
        $this->t('Edit'),
        'nortvus_subscription.subscription_edit_form',
        ['subscription_id' => $subscription_id],
        [
          'attributes' => [
            'class' => ['use-ajax'],
            'data-dialog-type' => 'modal',
          ],
          'class' => ['use-ajax'],
        ]
      );
      // Generates delete subscription link.
      $delete_link = Link::createFromRoute(
        $this->t('Delete'),
        'nortvus_subscription.subscription_delete_form',
        ['subscription_id' => $subscription_id],
        [
          'attributes' => [
            'class' => ['use-ajax'],
            'data-dialog-type' => 'modal',
          ],
          'class' => ['use-ajax'],
        ]
      );

      $rows[] = [
        'data' => [
          $subscription['name'],
          $subscription['mail'],
          $category_name,
          $created,
          $edit_link,
          $delete_link,
        ],
      ];
    }

    return [
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t("No results"),
      '#attached' => [
        'library' => [
          'nortvus_subscription/subscriptions',
        ],
      ],
      '#attributes' => [
        'id' => 'subscriptions-table',
        'class' => ['subscriptions-table'],
      ],
    ];
  }

}
