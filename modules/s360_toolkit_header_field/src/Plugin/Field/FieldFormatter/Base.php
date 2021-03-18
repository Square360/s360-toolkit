<?php

namespace Drupal\s360_toolkit_header_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\Component\Utility\Html;
use Drupal\link\Plugin\Field\FieldFormatter\LinkFormatter;
use Drupal\s360_toolkit_header_field\Plugin\Field\FieldWidget\HeaderFieldWidget;

/**
 * Base class for Header field formatters.
 */
abstract class Base extends LinkFormatter {

  /**
   * Returns the class label based on the $selected_class key.
   */
  protected function getClassLabel($selected_class) {
    $allowed_classes = explode("\n", $this->getFieldSetting('allowed_classes'));
    $allowed_classes = array_map('trim', $allowed_classes);
    $allowed_classes = array_filter($allowed_classes, 'strlen');

    $classes = [];

    foreach ($allowed_classes as $allowed_class) {
      $matches = [];

      if (preg_match('/(.*)\|(.*)/', $allowed_class, $matches)) {
        $key = trim($matches[1]);
        $value = trim($matches[2]);
      }

      $classes[$key] = $value;
    }

    $available_classes = array_merge($classes, HeaderFieldWidget::HEADING_CLASSES, HeaderFieldWidget::ADDITIONAL_CLASSES);

    if (array_key_exists($selected_class, $available_classes)) {
      return $available_classes[$selected_class];
    }
    else {
      return '';
    }
  }

  /**
   * Format the style class.
   */
  protected function formatClass(string $raw_classname) {
    return ($raw_classname !== 'visually-hidden')
      ? 'sthf-header--' . $raw_classname
      : $raw_classname;
  }

  /**
   * {@inheritdoc}
   */
  protected function getHeaderTags(FieldItemInterface $item) {
    $heading_text = Html::escape($item->title);
    $subtitle_text = Html::escape($item->subtitle);
    $options = $item->options;
    $header_tags = [];

    // Create the <h[2-6] /> tag using the $options['tag'] value.
    $heading = [
      '#type' => 'html_tag',
      '#tag' => $options['heading_tag'],
      '#attributes' => [
        'class' => [
          'sthf-header__heading',
        ],
      ],
    ];

    // When there's a uri, create a link and put it inside the heading.
    if ($item->get('uri')->getValue() !== 'route:<nolink>') {
      $url = $this->buildUrl($item);

      $link = [
        '#type' => 'link',
        '#title' => $heading_text,
        '#url' => $url,
        '#attributes' => [
          'class' => [
            'sthf-header__link',
          ],
          'target' => $options['new_window'] ? '_blank' : '_self',
        ],
      ];

      $heading[] = $link;
    }

    // Otherwise just put heading_text inside the heading.
    else {
      $heading['#value'] = $heading_text;
    }

    $header_tags[] = $heading;

    // When there's subtitle text, create a div and put it inside the heading.
    if (!empty($subtitle_text)) {
      $subtitle = [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#attributes' => [
          'class' => [
            'sthf-header__subtitle',
          ],
        ],
        '#value' => $subtitle_text,
      ];

      $header_tags[] = $subtitle;
    }

    return $header_tags;
  }

}
