<?php

namespace Drupal\s360_toolkit_header_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\Component\Utility\Html;
use Drupal\link\Plugin\Field\FieldFormatter\LinkFormatter;

/**
 * Base class for Header field formatters.
 */
abstract class HeaderFieldFormatter extends LinkFormatter {

  /**
   * Returns an array of classes for the header.
   *
   * @param array $options
   *   The selected options for the header.
   *
   * @return array
   *   The classes.
   */
  protected function getHeaderClasses(array $options) {
    $classes[] = 'sthf-header';

    if ($options['center']) {
      $classes[] = 'sthf-header--centered';
    }

    if ($options['uppercase']) {
      $classes[] = 'sthf-header--uppercase';
    }

    if ($options['size']) {
      $classes[] = 'sthf-header--size-' . $options['size'];
    }

    if ($options['styles']) {
      // Add a class for all styles.
      foreach ($options['styles'] as $style) {
        $classes[] = 'sthf-header--style-' . $style;
      }
    }

    // "visually-hidden" is a utility class so it doesn't need a BEM class name.
    if ($options['visually_hidden']) {
      $classes[] = 'visually-hidden';
    }

    return $classes;
  }

  /**
   * Creates an array of header tags.
   *
   * @param Drupal\Core\Field\FieldItemInterface $item
   *   The header field item.
   *
   * @return array
   *   The array containing heading tag <h[2-6] /> and optinally the subtitle.
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
        'id' => [
          'sthf-header-' . hash('adler32', $heading_text),
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
