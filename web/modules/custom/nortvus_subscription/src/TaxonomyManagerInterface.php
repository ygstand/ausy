<?php

namespace Drupal\nortvus_subscription;

/**
 * Interface TaxonomyManagerInterface.
 */
interface TaxonomyManagerInterface {

  /**
   * Returns a list of taxonomy terms by vocabulary id.
   *
   * @param int $vid
   *   Vocabulary id.
   *
   * @return array
   *   All taxonomy terms of particular vocabulary id.
   */
  public function getAllTerms($vid): array;

}
