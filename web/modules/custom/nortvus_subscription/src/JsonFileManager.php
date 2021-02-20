<?php

namespace Drupal\nortvus_subscription;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileSystemInterface;

/**
 * Class JsonFileManager.
 */
class JsonFileManager implements FileManagerInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The file system.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * Default file scheme.
   *
   * @var string
   */
  protected $defaultFileScheme;

  /**
   * Constructs a new \Drupal\nortvus_subscription\JsonFileManager object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   The file system.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, FileSystemInterface $file_system) {
    $this->entityTypeManager = $entity_type_manager;
    $this->fileSystem = $file_system;
    $this->defaultFileScheme = \Drupal::config('system.file')->get('default_scheme');
  }

  /**
   * {@inheritdoc}
   */
  public function getFileContent($file_path) {
    $file = file_get_contents($file_path);

    return $file;
  }

  /**
   * {@inheritdoc}
   */
  public function generateDirectory($directory_name, $scheme = NULL): string {
    if (empty($scheme)) {
      $scheme = $this->defaultFileScheme;
    }
    $directory = $scheme . '://' . $directory_name;

    return $directory;
  }

  /**
   * {@inheritdoc}
   */
  public function fileSaveData($directory, $file_name, $data): void {
    $this->fileSystem->prepareDirectory($directory, FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS);
    $file_location = $directory . '/' . $file_name;

    if (is_array($data)) {
      // Prepare json output.
      $data = Json::encode($data);
    }

    file_save_data($data, $file_location, FileSystemInterface::EXISTS_REPLACE);
  }

}
