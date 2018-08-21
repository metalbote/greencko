<?php

/**
 * @file
 * Drupal site-specific configuration file.
 */

// Site Folder i.e. 'default'
$site_dir = 'default'; // CHANGE ON MULTISITE

// General Settings
$settings['allow_authorize_operations'] = FALSE;
$settings['file_chmod_directory'] = 0775;
$settings['file_chmod_file'] = 0664;
$settings['session_write_interval'] = 180;
$settings['maintenance_theme'] = 'greencko_ui';

if (file_exists(DRUPAL_ROOT . '/sites/' . $site_dir . '/settings.development.php')) {
  include DRUPAL_ROOT . '/sites/' . $site_dir . '/settings.development.php';
}
elseif (file_exists(DRUPAL_ROOT . '/sites/' . $site_dir . '/settings.staging.php')) {
  include DRUPAL_ROOT . '/sites/' . $site_dir . '/settings.staging.php';
}
else {
  include DRUPAL_ROOT . '/sites/' . $site_dir . '/settings.production.php';
}

