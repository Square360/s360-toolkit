<?php

namespace Drupal\s360_toolkit_header_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FieldItemInterface;

/**
 * Plugin implementation of the 'header_field' formatter.
 *
 * @FieldFormatter(
 *   id = "header_field_semantic",
 *   label = @Translation("Semantic Markup (header)"),
 *   field_types = {
 *     "header_field"
 *   }
 * )
 */
class SemanticFormatter extends HeaderFieldFormatter {

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
      // Create the <header /> tag.
      $header = [
        '#type' => 'html_tag',
        '#tag' => 'header',
        '#attributes' => [
          'class' => $this->getHeaderClasses($item->options),
        ],
      ];

      foreach ($this->getHeaderTags($item) as $header_tag) {
        $header[] = $header_tag;
      }

      return $header;
    }
    else {
      return FALSE;
    }
  }

}
