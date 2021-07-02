<?php

namespace Drupal\s360_toolkit_header_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\link\Plugin\Field\FieldWidget\LinkWidget;
use Drupal\s360_toolkit_header_field\Plugin\Field\FieldType\HeaderFieldItem;

/**
 * Plugin implementation of the 'header_field' widget.
 *
 * @FieldWidget(
 *   id = "header_field",
 *   label = @Translation("Header Field"),
 *   field_types = {
 *     "header_field"
 *   }
 * )
 */
class HeaderFieldWidget extends LinkWidget {

  // Define all the default sizes.
  const DEFAULT_SIZES = [
    'h1' => 'Heading 1',
    'h2' => 'Heading 2',
    'h3' => 'Heading 3',
    'h4' => 'Heading 4',
    'h5' => 'Heading 5',
    'h6' => 'Heading 6',
  ];

  // Define all default styles.
  const DEFAULT_STYLES = [];

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $item = $items[$delta];

    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $element['#attributes'] = [
      'class' => ['header-field'],
    ];
    $element['#type'] = 'details';
    $element['#open'] = TRUE;

    // Rename the title field's title.
    $element['title']['#title'] = 'Heading text';

    // Make the title required based on the field.
    $element['title']['#required'] = $element['#required'];

    // Start heading group.
    $element['text_group'] = [
      '#type' => 'fieldset',
      '#title' => t('Text'),
      '#attributes' => [
        'class' => ['header-field__text-group'],
      ],
    ];

    $semantic_tag_options = array_intersect_key(HeaderFieldItem::AVAILABLE_TAGS, array_flip($this->getFieldSetting('allowed_tags')));

    $element['text_group']['heading_tag'] = [
      '#title' => t('Semantic Tag'),
      '#type' => 'select',
      '#options' => $semantic_tag_options,
      '#default_value' => $item->options['heading_tag'] ?? 'h2',
    ];

    // Move the 'title' element into the text_group.
    $element['text_group']['title'] = $element['title'];

    $element['text_group']['subtitle'] = [
      '#title' => t('Subtitle text'),
      '#type' => 'textfield',
      '#default_value' => $item->subtitle ?? '',
    ];

    // Start options group.
    $element['options_group'] = [
      '#type' => 'fieldset',
      '#title' => t('Options'),
      '#attributes' => [
        'class' => ['header-field__options-group'],
      ],
    ];

    // Start display group.
    $element['options_group']['display_group'] = [
      '#type' => 'details',
      '#title' => t('Display options'),
      '#open' => ($item->options['center'] || $item->options['uppercase'] || $item->options['class']) ?? FALSE,
    ];

    $element['options_group']['display_group']['visually_hidden'] = [
      '#title' => t('Visually Hidden'),
      '#type' => 'checkbox',
      '#default_value' => $item->options['visually_hidden'] ?? FALSE,
      '#description' => t('When checked, the text will be hidden from display, but not screen readers.'),
    ];

    $element['options_group']['display_group']['center'] = [
      '#title' => t('Center'),
      '#type' => 'checkbox',
      '#default_value' => $item->options['center'] ?? FALSE,
      '#description' => t('When checked, the text will be centered.'),
    ];

    $element['options_group']['display_group']['uppercase'] = [
      '#title' => 'Uppercase',
      '#type' => 'checkbox',
      '#default_value' => $item->options['uppercase'] ?? FALSE,
      '#description' => t('When checked, the text will be uppercased.'),
    ];

    $element['options_group']['display_group']['size'] = [
      '#title' => t('Size'),
      '#type' => 'select',
      '#options' => $this->getSizeOptions(),
      '#empty_option' => t('- Select -'),
      '#default_value' => $item->options['size'] ?? '',
    ];

    $element['options_group']['display_group']['styles'] = [
      '#title' => t('Styles'),
      '#type' => 'checkboxes',
      '#options' => $this->getStyleOptions(),
      '#default_value' => $item->options['styles'] ?? [],
    ];

    // Start link group.
    $element['options_group']['link_group'] = [
      '#type' => 'details',
      '#title' => t('Link options'),
      '#open' => ($item->options['new_window'] || $element['uri']['#default_value'] !== '<nolink>') ?? FALSE,
    ];

    // Move the 'uri' element into the link_group.
    $element['options_group']['link_group']['uri'] = $element['uri'];
    $element['options_group']['link_group']['new_window'] = [
      '#title' => t('Open link in new window.'),
      '#type' => 'checkbox',
      '#default_value' => $item->options['new_window'] ?? FALSE,
      '#description' => t("Check this to force link to open in new window."),
    ];

    // Since we've moved the title and uri into groups, hide the original.
    unset($element['title']);
    unset($element['uri']);

    $element['#element_validate'][] = [get_called_class(), 'processFields'];
    $element['#element_validate'][] = [get_called_class(), 'processOptions'];

    return $element;
  }

  /**
   * Update field values.
   */
  public static function processFields(&$element, FormStateInterface $form_state, $form) {
    $values = $form_state->getValue($element['#parents']);

    // Change empty uri to '<nolink>'.
    if ($element['options_group']['link_group']['uri']['#value'] === '') {
      $values['uri'] = '<nolink>';
    }
    // Set the uri with the uri's value from the link_group.
    else {
      $values['uri'] = $element['options_group']['link_group']['uri']['#value'];
    }

    // Set the title with the title's value from the text_group.
    $values['title'] = $element['text_group']['title']['#value'];
    $values['subtitle'] = $element['text_group']['subtitle']['#value'];

    $form_state->setValueForElement($element, $values);
  }

  /**
   * Copy custom values into the option field.
   */
  public static function processOptions(&$element, FormStateInterface $form_state, $form) {
    $values = $form_state->getValue($element['#parents']);

    $values['options'] = [
      'heading_tag' => $element['text_group']['heading_tag']['#value'],

      'visually_hidden' => $element['options_group']['display_group']['visually_hidden']['#value'],
      'center' => $element['options_group']['display_group']['center']['#value'],
      'uppercase' => $element['options_group']['display_group']['uppercase']['#value'],
      'size' => $element['options_group']['display_group']['size']['#value'],
      'styles' => array_keys($element['options_group']['display_group']['styles']['#value']),

      'new_window' => $element['options_group']['link_group']['new_window']['#value'],
    ];

    $form_state->setValueForElement($element, $values);
  }

  /**
   * Creates an array of available sizes.
   *
   * @return array
   *   An associative array of available sizes.
   */
  private function getSizeOptions() {
    $size_options = [];

    // Add the DEFAULT_SIZES to $size_options array.
    $size_options = HeaderFieldWidget::DEFAULT_SIZES;

    // Get the custom_sizes and apply some function to normalize them.
    $custom_sizes = explode("\n", $this->getFieldSetting('custom_sizes'));
    $custom_sizes = array_map('trim', $custom_sizes);
    $custom_sizes = array_filter($custom_sizes, 'strlen');

    foreach ($custom_sizes as $custom_size) {
      $matches = [];

      if (preg_match('/(.*)\|(.*)/', $custom_size, $matches)) {
        $key = trim($matches[1]);
        $value = trim($matches[2]);
      }

      $size_options[$key] = $value;
    }

    return $size_options;
  }

  /**
   * Creates an array of available styles.
   *
   * @return array
   *   An associative array of available styles.
   */
  private function getStyleOptions() {
    $style_options = [];

    // Add the DEFAULT_STYLES to $style_options array.
    $style_options = HeaderFieldWidget::DEFAULT_STYLES;

    // Get the custom_styles and apply some function to normalize them.
    $custom_styles = explode("\n", $this->getFieldSetting('custom_styles'));
    $custom_styles = array_map('trim', $custom_styles);
    $custom_styles = array_filter($custom_styles, 'strlen');

    foreach ($custom_styles as $custom_style) {
      $matches = [];

      if (preg_match('/(.*)\|(.*)/', $custom_style, $matches)) {
        $key = trim($matches[1]);
        $value = trim($matches[2]);
      }

      $style_options[$key] = $value;
    }

    return $style_options;
  }

}
