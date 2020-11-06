<?php

namespace Drupal\s360_toolkit_heading_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Component\Utility\Html;

/**
 * Plugin implementation of the 'sqr_title_field' formatter.
 *
 * @FieldFormatter(
 *   id = "heading_field_formatter",
 *   label = @Translation("Heading Field"),
 * )
 */
class HeadingFieldFormatter extends FormatterBase {

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
    $entity_type = $this->fieldDefinition->getTargetEntityTypeId();

    $options = $item->options;

    $header = [
      '#type' => 'html_tag',
      '#tag' => $options['tag'],
      '#attributes' => [
        'class' => [
          $entity_type . '__heading',
        ],
      ],
    ];

    // Hide the header
    if ($options['visible'] === 1) {
      $header['#attributes'] = [
        'class' => [
          "visually-hidden",
        ],
      ];
    }

    $header['#value'] = $this->setHeaderValue($item);

    return $header;
  }

  /**
   *
   */
  protected function setHeaderValue($item) {
    $title = Html::escape($item->title);

    return $title;
  }

}
