<?php

/**
 * @file
 * Contains \Drupal\entity_alias\Form\EntityAliasSettingsForm.
 */

namespace Drupal\entity_alias\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Class EntityAliasSettingsForm.
 *
 * @package Drupal\entity_alias\Form
 */
class EntityAliasSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'entity_alias.settings',
    ];
  }


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'entity_alias_settings_form';
  }


  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $content_types = \Drupal::service('entity.manager')
      ->getStorage('node_type')
      ->loadMultiple();
    $config = $this->config('entity_alias.settings');
    $configured_entity_types = $config->get('entity_types');

    $form[]['#prefix'] = t('<p>Smart alias uses @pathauto</p><p>Content types checked here will be smart aliased.</p>', [
      '@pathauto' => \Drupal::l('pathauto', Url::fromRoute('pathauto.settings.form')),
    ]);

    foreach ($content_types as $type_id => $content_type) {
      $default_value = 0;
      if (!empty($configured_entity_types[$type_id])) {
        $default_value = $configured_entity_types[$type_id]['enabled'];
      }

      $form[$type_id] = array(
        '#type' => 'checkbox',
        '#title' => $content_type->label(),
        '#default_value' => $default_value,
      );
    }

    return parent::buildForm($form, $form_state);
  }


  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $content_types = \Drupal::service('entity.manager')
      ->getStorage('node_type')
      ->loadMultiple();

    $config = \Drupal::service('config.factory')
      ->getEditable('entity_alias.settings');

    $submitted_values = [];
    foreach ($content_types as $type => $content_type) {
      $is_enabled = $form_state->getValue($type);
      if (!$is_enabled) {
        $nids = \Drupal::entityQuery('node')
          ->condition('type', $type)->execute();
        if ($nids) {
          foreach($nids as $nid) {
            \Drupal::service('pathauto.alias_storage_helper')->deleteAll('node/' . $nid);
          }
        }
      }

      $submitted_values[$type] = [
        'entity_type' => $type,
        'enabled' => $is_enabled,
      ];
    }

    $config->set('entity_types', $submitted_values)->save();

  }

}
