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

    foreach ($content_types as $type_id => $content_type) {
      $submitted_values[$type_id] = [
        'entity_type' => $type_id,
        'enabled' => $form_state->getValue($type_id),
      ];
    }

    $config->set('entity_types', $submitted_values)->save();

  }

}
