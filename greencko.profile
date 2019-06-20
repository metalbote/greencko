<?php

/**
 * @file
 * Enables modules and site configuration for a Greencko site installation.
 */

use Drupal\Core\Form\FormStateInterface;
use Drupal\greencko\Form\GreenckoAssemblerForm;
use Drupal\varbase\Config\ConfigBit;

/**
 * Implements hook_form_FORM_ID_alter() for install_configure_form().
 *
 * Allows the profile to alter the site configuration form.
 */
function greencko_form_install_configure_form_alter(&$form, FormStateInterface $form_state) {
  $varbase_path = drupal_get_path('profile', 'varbase');
  include_once $varbase_path . '/varbase.profile';
  varbase_form_install_configure_form_alter($form, $form_state);

  // Add a placeholder as example that one can choose an arbitrary site name.
  $form['site_information']['site_name']['#attributes']['placeholder'] = t('My Greencko powered Website');
  $form['site_information']['site_name']['#default_value'] = t('My Greencko powered Website');

  // Default site email noreply@your-domain.com.
  $form['site_information']['site_mail']['#default_value'] = 'noreply@your-domain.com';
  $form['site_information']['site_mail']['#attributes']['style'] = 'width: 25em;';

  $form['admin_account']['account']['name']['#attributes']['disabled'] = FALSE;
  $form['admin_account']['account']['name']['#default_value'] = 'webmaster';
  $form['admin_account']['account']['mail']['#default_value'] = 'webmaster@your-domain.com';

  $form['admin_account']['account']['mail']['#description'] = '';

  // Date/time settings.
  $form['regional_settings']['site_default_country']['#default_value'] = 'DE';
  $form['regional_settings']['date_default_timezone']['#default_value'] = 'Europe/Berlin';
}

/**
 * Implements hook_install_tasks().
 */
function greencko_install_tasks(&$install_state) {
  $varbase_path = drupal_get_path('profile', 'varbase');
  include_once $varbase_path . '/varbase.profile';
  $varbase_install_tasks = varbase_install_tasks($install_state);

  $install_components = [
    'varbase_multilingual_configuration_form' => $varbase_install_tasks['varbase_multilingual_configuration_form'],
    'varbase_configure_multilingual' => $varbase_install_tasks['varbase_configure_multilingual'],
    'varbase_extra_components' => $varbase_install_tasks['varbase_extra_components'],
    'varbase_assemble_extra_components' => $varbase_install_tasks['varbase_assemble_extra_components'],

    // Custom Components.
    'greencko_extra_components' => [
      'display_name' => t('greencko components'),
      'display' => TRUE,
      'type' => 'form',
      'function' => GreenckoAssemblerForm::class,
    ],
    'greencko_assemble_extra_components' => [
      'display_name' => t('Assemble Greencko components'),
      'display' => TRUE,
      'type' => 'batch',
    ],
    'varbase_development_tools' => $varbase_install_tasks['varbase_development_tools'],
    'varbase_assemble_development_tools' => $varbase_install_tasks['varbase_assemble_development_tools'],
  ];
  $install_components['varbase_extra_components']['display_name'] = t('Varbase components');
  $install_components['varbase_assemble_extra_components']['display_name'] = t('Assemble Varbase components');

  return $install_components;
}

/**
 * Batch job to assemble greencko extra components.
 *
 * @param array $install_state
 *   The current install state.
 *
 * @return array
 *   The batch job definition.
 */
function greencko_assemble_extra_components(array &$install_state) {

  // Default Greencko components, which must be installed.
  $default_components = ConfigBit::getList('configbit/default.components.greencko.bit.yml',
    'install_default_components', TRUE, 'dependencies', 'profile', 'greencko');

  $batch = [];

  // Install default components first.
  foreach ($default_components as $default_component) {
    $batch['operations'][] = [
      'varbase_assemble_extra_component_then_install',
      (array) $default_component,
    ];
  }

  // Install selected extra features.
  $selected_extra_features = [];
  $selected_extra_features_configs = [];

  if (isset($install_state['varbase']['extra_features_values'])) {
    $selected_extra_features = $install_state['varbase']['extra_features_values'];
  }

  if (isset($install_state['varbase']['extra_features_configs'])) {
    $selected_extra_features_configs = $install_state['varbase']['extra_features_configs'];
  }

  // Get the list of extra features config bits.
  $extraFeatures = ConfigBit::getList('configbit/extra.components.greencko.bit.yml', 'show_extra_components', TRUE, 'dependencies', 'profile', 'greencko');

  // If we do have selected extra features.
  if (count($selected_extra_features) && count($extraFeatures)) {
    // Have batch processes for each selected extra features.
    foreach ($selected_extra_features as $extra_feature_key => $extra_feature_checked) {
      if ($extra_feature_checked) {

        // If the extra feature was a module and not enabled, then enable it.
        if (!Drupal::moduleHandler()->moduleExists($extra_feature_key)) {
          // Add the checked extra feature to the batch process to be enabled.
          $batch['operations'][] = [
            'varbase_assemble_extra_component_then_install',
            (array) $extra_feature_key,
          ];
        }

        if (count($selected_extra_features_configs) &&
          isset($extraFeatures[$extra_feature_key]['config_form']) &&
          $extraFeatures[$extra_feature_key]['config_form'] == TRUE &&
          isset($extraFeatures[$extra_feature_key]['formbit'])) {

          $formbit_file_name = drupal_get_path('profile', 'greencko') . '/' . $extraFeatures[$extra_feature_key]['formbit'];

          if (file_exists($formbit_file_name)) {

            // Added the selected extra feature configs to the batch process
            // with the same function name in the formbit.
            $batch['operations'][] = [
              'varbase_save_editable_config_values',
              (array) [
                $extra_feature_key,
                $formbit_file_name,
                $selected_extra_features_configs,
              ],
            ];
          }
        }
      }
    }

    // Hide warnings and status messages.
    $batch['operations'][] = [
      'varbase_hide_warning_and_status_messages',
      (array) TRUE,
    ];

    // Fix entity updates to clear up any mismatched entity.
    $batch['operations'][] = ['varbase_fix_entity_update', (array) TRUE];
  }

  return $batch;
}
