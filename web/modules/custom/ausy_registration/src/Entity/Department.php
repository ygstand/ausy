<?php

namespace Drupal\ausy_registration\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Department entity.
 *
 * @ConfigEntityType(
 *   id = "department",
 *   label = @Translation("Department"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\ausy_registration\DepartmentListBuilder",
 *     "form" = {
 *       "add" = "Drupal\ausy_registration\Form\DepartmentForm",
 *       "edit" = "Drupal\ausy_registration\Form\DepartmentForm",
 *       "delete" = "Drupal\ausy_registration\Form\DepartmentDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\ausy_registration\DepartmentHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "department",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "uuid",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/department/{department}",
 *     "add-form" = "/admin/config/add-department",
 *     "edit-form" = "/admin/structure/department/{department}/edit",
 *     "delete-form" = "/admin/structure/department/{department}/delete",
 *     "collection" = "/admin/structure/department"
 *   }
 * )
 */
class Department extends ConfigEntityBase implements DepartmentInterface {

  /**
   * The Department ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Department label.
   *
   * @var string
   */
  protected $label;

}
