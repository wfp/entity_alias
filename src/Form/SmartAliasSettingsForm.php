<?php

/**
 * @file
 * Contains \Drupal\smart_alias\Form\SmartAliasSettingsForm.
 */

namespace Drupal\smart_alias\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Class SmartAliasSettingsForm.
 *
 * @package Drupal\smart_alias\Form
 */
class SmartAliasSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'smart_alias.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'smart_alias_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('smart_alias.settings');
    $configured_entity_types = $config->get('entity_types');
    $content_types = \Drupal::service('entity.manager')->getStorage('node_type')->loadMultiple();

    foreach ($content_types as $type => $content_type) {
      $default_value = 0;
      if (!empty($configured_entity_types[$type])) {
        $default_value = $configured_entity_types[$type]['enabled'];
      }

      $form[$type] = [
        '#type' => 'checkbox',
        '#title' => $content_type->label(),
        '#default_value' => $default_value,
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $values = [];
    $config = $this->config('smart_alias.settings');
    $content_types = \Drupal::service('entity.manager')->getStorage('node_type')->loadMultiple();

    foreach ($content_types as $type => $content_type) {
      $is_enabled = $form_state->getValue($type);
      if (!$is_enabled) {
        $nids = \Drupal::entityQuery('node')
          ->condition('type', $type)->execute();
        if ($nids) {
          foreach ($nids as $nid) {
            \Drupal::service('pathauto.alias_storage_helper')->deleteAll('node/' . $nid);
          }
        }
      }

      $values[$type] = [
        'entity_type' => $type,
        'enabled' => $is_enabled,
      ];
    }

    $config->set('entity_types', $values)->save();
  }

}
