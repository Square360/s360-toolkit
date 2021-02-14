<?php

namespace Drupal\s360_toolkit_header_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Component\Utility\Html;
use Drupal\link\Plugin\Field\FieldFormatter\LinkFormatter;

/**
 * Plugin implementation of the 'header_field_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "header_field_formatter",
 *   label = @Translation("Header Field"),
 * )
 */
class HeaderFieldFormatter extends LinkFormatter {

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
    $heading_text = Html::escape($item->title);
    $subtitle_text = Html::escape($item->subtitle);
    $url = $this->buildUrl($item);
    $options = $url->getOptions();

    // Create the <header /> tag.
    $header = [
      '#type' => 'html_tag',
      '#tag' => 'header',
      '#attributes' => [
        'class' => [
          'header-group',
          ($options['center'] ? 'header-group--centered' : ''),
          ($options['uppercase'] ? 'header-group--uppercase' : ''),
          ($options['style_class'] ? 'header-group--' . $options['style_class'] : ''),
        ],
      ],
    ];

    // Create the <h[2-6] /> tag using the $options['tag'] value.
    $heading = [
      '#type' => 'html_tag',
      '#tag' => $options['tag'],
      '#attributes' => [
        'class' => [
          'header-group__heading',
        ],
      ],
    ];

    // When there's a uri, create a link and put it inside the heading.
    if (!empty($item->get('uri'))) {
      $link = [
        '#type' => 'link',
        '#title' => $heading_text,
        '#url' => $url,
      ];

      $heading[] = $link;
    }

    // Otherwise just put heading_text inside the heading.
    else {
      $heading['#value'] = $heading_text;
    }

    // Put the heading inside the header.
    $header[] = $heading;

    // When there's subtitle text, create a div and put it inside the heading.
    if (!empty($subtitle_text)) {
      $subtitle = [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#attributes' => [
          'class' => [
            'header-group__subtitle',
          ],
        ],
      ];

      $subtitle['#value'] = $subtitle_text;
      $header[] = $subtitle;
    }

    return $header;
  }

}
