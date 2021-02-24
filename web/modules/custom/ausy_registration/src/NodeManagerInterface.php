<?php

namespace Drupal\ausy_registration;

/**
 * Interface NodeManagerInterface.
 */
interface NodeManagerInterface {

  /**
   * Returns a number of existing nodes of particular content type.
   *
   * @param string $type
   *   Content type.
   *
   * @return int
   *   The number of existing nodes of particular content type.
   */
  public function getNodesCount($type): int;

}
