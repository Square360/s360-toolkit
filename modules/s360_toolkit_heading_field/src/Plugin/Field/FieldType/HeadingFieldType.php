<?php

namespace Drupal\s360_toolkit_heading_field\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\MapDataDefinition;
use Drupal\Core\Field\FieldItemBase;

/**
 * Plugin implementation of the 'custom_heading' field type.
 *
 * @FieldType(
 *   id = "heading_field_type",
 *   label = @Translation("Heading Field"),
 *   description = @Translation("Heading Field"),
 *   default_widget = "heading_field_widget",
 *   default_formatter = "heading_field_formatter",
 * )
 */
class HeadingFieldType extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings() {
    return [
      'title' => DRUPAL_OPTIONAL,
    ];
  }

  /**
   * Field type properties definition.
   *
   * Inside this method we define all the fields (properties) that our
   * custom field type will have.
   *
   * Here is a list of allowed property types: https://goo.gl/sIBBgO
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $storage) {
    $properties['options'] = MapDataDefinition::create()
      ->setLabel(t('Options'));

    $properties['title'] = DataDefinition::create('string')
      ->setLabel(t('Link text'));

    return $properties;
  }

  /**
   * Field type schema definition.
   *
   * Inside this method we defines the database schema used to store data for
   * our field type.
   *
   * Here there is a list of allowed column types: https://goo.gl/YY3G7s
   */
  public static function schema(FieldStorageDefinitionInterface $storage) {
    $columns = [];

    $columns['options'] = [
      'description' => 'Serialized array of options for the heading.',
      'type' => 'blob',
      'size' => 'big',
      'serialize' => TRUE,
    ];

    $columns['title'] = [
      'description' => 'The title of the heading',
      'type' => 'varchar',
      'length' => 255,
    ];

    return [
      'columns' => $columns,
      'indexes' => [],
    ];
  }

}
