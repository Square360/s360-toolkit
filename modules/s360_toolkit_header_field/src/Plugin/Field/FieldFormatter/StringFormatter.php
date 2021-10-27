<?php

namespace Drupal\s360_toolkit_header_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\s360_toolkit_header_field\Plugin\Field\FieldType\HeaderFieldItem;

/**
 * Plugin implementation of the 'header_field' formatter.
 *
 * @FieldFormatter(
 *   id = "header_field_string",
 *   label = @Translation("Heading Text (plain)"),
 *   field_types = {
 *     "header_field"
 *   }
 * )
 */
class StringFormatter extends HeaderFieldFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = $this->viewValue($item);
    }

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  private function viewValue(FieldItemInterface $item) {
    if ($item->title) {
      $options = $item->options;

      $heading_tag = HeaderFieldItem::AVAILABLE_TAGS[$options['heading_tag']];

      return [
        '#type' => 'inline_template',
        '#template' => '{{ value|nl2br }}',
        '#context' => [
          'value' => ($item->title !== '')
          ? $heading_tag . ': ' . $item->title
          : '',
        ],
      ];
    }
  }

}
