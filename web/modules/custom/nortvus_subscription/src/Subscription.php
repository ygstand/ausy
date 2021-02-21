<?php

namespace Drupal\nortvus_subscription;

use Drupal\Component\Serialization\Json;

/**
 * Class JsonFileManager.
 */
class Subscription implements SubscriptionInterface {

  /**
   * Name of the file to store subscriptions.
   */
  const SUBSCRIPTIONS_FILE_NAME = 'subscriptions.json';

  /**
   * Name of the directory to save the subscriptions file in.
   */
  const SUBSCRIPTIONS_DIRECTORY_NAME = 'subscriptions';

  /**
   * Json file manager service.
   *
   * @var \Drupal\nortvus_subscription\FileManagerInterface
   */
  protected $jsonFileManager;

  /**
   * An array of subscriptions.
   *
   * @var array
   */
  protected $subscriptions = [];

  /**
   * Constructs a new \Drupal\nortvus_subscription\Subscription object.
   *
   * @param \Drupal\nortvus_subscription\FileManagerInterface $json_file_manager
   *   Json file manager service
   */
  public function __construct(FileManagerInterface $json_file_manager) {
    $this->jsonFileManager = $json_file_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function getSubscription($id): ?array {
    $subscription = NULL;
    $subscriptions = $this->getAllSubscriptions();

    if (!empty($subscriptions) && is_array($subscriptions)) {
      $subscription = $subscriptions[$id];
    }

    return $subscription;
  }

  /**
   * {@inheritdoc}
   */
  public function getAllSubscriptions(): array {
    // Contains result to be returned.
    $result = [];

    $directory = $this->jsonFileManager->generateDirectory(self::SUBSCRIPTIONS_DIRECTORY_NAME);
    $subscriptions = $this->jsonFileManager->getFileContent($directory . '/' . self::SUBSCRIPTIONS_FILE_NAME);
    if (!empty($subscriptions)) {
      // Decoding json to get the array.
      $result = JSON::decode($subscriptions);
    }

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function editSubscription($id, $values): array {
    $subscriptions = $this->getAllSubscriptions();
    if (isset($subscriptions[$id])) {
      $subscriptions[$id] = $values;
    }

    return $subscriptions;
  }

  /**
   * {@inheritdoc}
   */
  public function createSubscription($values): array {
    $subscriptions = $this->getAllSubscriptions();
    if (!empty($subscriptions)) {
      $subscriptions[] = $values;
    }
    else {
      // The ids list should start from digit 1.
      $subscriptions = [
        1 => $values
      ];
    }

    return $subscriptions;
  }

  /**
   * {@inheritdoc}
   */
  public function delete($id): array {
    $subscriptions = $this->getAllSubscriptions();
    if (isset($subscriptions[$id])) {
      unset($subscriptions[$id]);
    }
    $this->save($subscriptions);

    return $subscriptions;
  }

  /**
   * {@inheritdoc}
   */
  public function save($data): void {
    $output = Json::encode($data);
    $directory = $this->jsonFileManager->generateDirectory(self::SUBSCRIPTIONS_DIRECTORY_NAME);

    $this->jsonFileManager->fileSaveData($directory, self::SUBSCRIPTIONS_FILE_NAME, $output);
  }

}
