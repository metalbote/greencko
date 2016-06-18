<?php
/**
 * @file
 * Enables modules and site configuration for a greenery site installation.
 */

use Drupal\contact\Entity\ContactForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_FORM_ID_alter() for install_configure_form().
 *
 * Allows the profile to alter the site configuration form.
 */
function greenery_form_install_configure_form_alter(&$form, FormStateInterface $form_state) {
  $form['#submit'][] = 'greenery_form_install_configure_submit';
  $form['site_information']['site_name']['#default_value'] = 'My webiteasy Website';
  $form['admin_account']['account']['name']['#default_value'] = 'greenadmin';
  $form['regional_settings']['site_default_country']['#default_value'] = 'DE';
  $form['regional_settings']['date_default_timezone']['#default_value'] = 'Europe/Berlin';
}

/**
 * Submission handler to sync the contact.form.feedback recipient.
 */
function greenery_form_install_configure_submit($form, FormStateInterface $form_state) {
  $site_mail = $form_state->getValue('site_mail');
  ContactForm::load('feedback')->setRecipients([$site_mail])->trustData()->save();
}