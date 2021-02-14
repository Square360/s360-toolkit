<?php

namespace Drupal\s360_toolkit_header_field\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Form\FormStateInterface;
use Drupal\link\LinkItemInterface;
use Drupal\link\Plugin\Field\FieldType\LinkItem;

/**
 * Plugin implementation of the 'header_field_type' field type.
 *
 * @FieldType(
 *   id = "header_field_type",
 *   label = @Translation("Header Field"),
 *   description = @Translation("Header Field"),
 *   default_widget = "header_field_widget",
 *   default_formatter = "header_field_formatter",
 * )
 */
class HeaderFieldItem extends LinkItem {

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    return [
      'allowed_classes' => '',
    ] + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings() {
    return [
      'title' => DRUPAL_REQUIRED,
      'link_type' => LinkItemInterface::LINK_GENERIC,
      'heading_as_link' => FALSE,
    ] + parent::defaultFieldSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = parent::schema($field_definition);

    $schema['columns']['subtitle'] = [
      'description' => 'Optional subtitle for the header.',
      'type' => 'varchar',
      'length' => 255,
    ];

    return $schema;
  }

  /**
   * Field type properties definition.
   *
   * Inside this method we define all the fields (properties) that our
   * custom field type will have.
   *
   * Here is a list of allowed property types: https://goo.gl/sIBBgO
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = parent::propertyDefinitions($field_definition);

    $properties['uri'] = DataDefinition::create('uri')
      ->setLabel(t('URI'))
      ->setRequired(FALSE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    $element = parent::storageSettingsForm($form, $form_state, $has_data);

    $element['allowed_classes'] = [
      '#type' => 'textarea',
      '#rows' => 10,
      '#default_value' => $this->getSetting('allowed_classes'),
      '#title' => t('Allowed classes'),
      '#description' => t('Possible styles that can be applied to the header field. Enter one value per line, in the format class-name|label.'),
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::fieldSettingsForm($form, $form_state);

    $element['heading_as_link'] = [
      '#type' => 'checkbox',
      '#title' => t('Render heading text as link'),
      '#default_value' => $this->getSetting('heading_as_link'),
    ];

    return $element;
  }

}
