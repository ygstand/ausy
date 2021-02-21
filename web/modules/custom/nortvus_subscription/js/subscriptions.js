/**
 * @file
 * Subscriptions page behaviors.
 */
(function ($, window, Drupal) {

  'use strict';

  /**
   * Provides functionality to sort the subscriptions table data.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   */
  Drupal.behaviors.sortTable = {
    attach: function (context, settings) {
      $('#subscriptions-table', context).each(function (index, element) {
        $(element).DataTable();
      });
    }
  };

})(jQuery, window, Drupal);
