<?php

namespace Drupal\nortvus_subscription;

/**
 * Interface FileManagerInterface.
 */
interface FileManagerInterface {

  /**
   * Returns file content.
   *
   * @param string $file_path
   *   Path to file.
   *
   * @return mixed
   *   Returns content of the a file using a particular format.
   */
  public function getFileContent($file_path);

  /**
   * Returns generated directory.
   *
   * @param string $directory_name
   *   Directory name.
   * @param string $scheme
   *   File source scheme.
   *
   * @return string
   *   Generated directory based on received scheme and directory name.
   */
  public function generateDirectory($directory_name, $scheme = NULL): string;

  /**
   * Saves data into file.
   *
   * @param string $directory
   *   File directory.
   * @param string $file_name
   *   File name.
   * @param mixed $data
   *   Data to save.
   */
  public function fileSaveData($directory, $file_name, $data): void;

}
