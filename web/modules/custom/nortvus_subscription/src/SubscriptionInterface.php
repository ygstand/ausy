<?php

namespace Drupal\nortvus_subscription;

/**
 * Interface SubscriptionInterface.
 */
interface SubscriptionInterface {

  /**
   * Gets the subscription by its id.
   *
   * @param string $id
   *   Subscription id.
   *
   * @return array|null
   *   An array to contain the subscription data.
   */
  public function getSubscription($id): ?array;

  /**
   * Returns an array of subscriptions.
   *
   * @return array
   *   Returns an array of subscriptions.
   */
  public function getAllSubscriptions(): array;

  /**
   * Edits subscription.
   *
   * @param string $id
   *   Subscription id.
   * @param array $values
   *   An array of data to save.
   *
   * @return array
   *   Returns an updated array of subscriptions.
   */
  public function editSubscription($id, $values): array;

  /**
   * Creates new subscription.
   *
   * @param array $values
   *   An array of data to save.
   *
   * @return array
   *   Returns an updated array of subscriptions.
   */
  public function createSubscription($values): array;

  /**
   * Deletes subscription.
   *
   * @param string $id
   *   Subscription id.
   *
   * @return array
   *   Returns an updated array of subscriptions.
   */
  public function delete($id): array;

  /**
   * Saves the subscriptions into json file.
   *
   * @param array $data
   *   Data to save into file.
   */
  public function save($data): void;

}
