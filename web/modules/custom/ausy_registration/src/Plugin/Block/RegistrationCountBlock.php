<?php

namespace Drupal\ausy_registration\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'RegistrationCountBlock' block.
 *
 * @Block(
 *  id = "registration_count_block",
 *  admin_label = @Translation("Registration count block"),
 * )
 */
class RegistrationCountBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\ausy_registration\NodeManagerInterface definition.
   *
   * @var \Drupal\ausy_registration\NodeManagerInterface
   */
  protected $ausyRegistrationRegistrationManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->ausyRegistrationRegistrationManager = $container->get('ausy_registration.registration_manager');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Getting the latest number of existing registrations.
    $registrations_count = $this->ausyRegistrationRegistrationManager->getNodesCount('registration');

    return [
      '#type' => 'markup',
      '#markup' => $this->t('Registration count: @count', ['@count' => $registrations_count]),
      '#cache' => [
        'max-age' => -1,
        'tags' => [
          'registration-nodes-list',
        ],
      ],
    ];
  }

}
