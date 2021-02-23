<?php

namespace Drupal\s360_toolkit_header_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FieldItemInterface;

/**
 * Plugin implementation of the 'header_field' formatter.
 *
 * @FieldFormatter(
 *   id = "header_field_generic",
 *   label = @Translation("Generic Markup <div />"),
 *   field_types = {
 *     "header_field"
 *   }
 * )
 */
class GenericFormatter extends Base {

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
    $options = $item->options;

    // Create the <div /> tag.
    $header = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => [
        'class' => [
          'header-group',
          ($options['center'] ? 'header-group--centered' : ''),
          ($options['uppercase'] ? 'header-group--uppercase' : ''),
          ($options['class'] ? $this->formatClass($options['class']) : ''),
        ],
      ],
    ];

    foreach ($this->getHeaderTags($item) as $header_tag) {
      $header[] = $header_tag;
    }

    return $header;
  }

}
