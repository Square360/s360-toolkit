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

  // Define all the heading classes.
  const HEADING_CLASSES = [
    'heading-2' => 'Heading 2',
    'heading-3' => 'Heading 3',
    'heading-4' => 'Heading 4',
    'heading-5' => 'Heading 5',
    'heading-6' => 'Heading 6',
  ];

  // Define any additional classes that can be used.
  const ADDITIONAL_CLASSES = [
    'visually-hidden' => 'Visually Hidden',
  ];

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

    // It's weird to see "<nolink>" in the uri field, so remove it and let the
    // processFields function handle the empty value.
    if ($element['uri']['#default_value'] === '<nolink>' || $element['uri']['#default_value'] === NULL) {
      $element['uri']['#default_value'] = '';
    }

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
    $element['options_group']['display_group']['class'] = [
      '#title' => t('Style'),
      '#type' => 'select',
      '#options' => $this->getClassOptions(),
      '#empty_option' => t('- Select -'),
      '#default_value' => $item->options['class'] ?? '',
    ];

    // Start link group.
    $element['options_group']['link_group'] = [
      '#type' => 'details',
      '#title' => t('Link options'),
      '#open' => ($item->options['new_window'] || $element['uri']['#default_value'] !== '') ?? FALSE,
    ];

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
      'center' => $element['options_group']['display_group']['center']['#value'],
      'uppercase' => $element['options_group']['display_group']['uppercase']['#value'],
      'class' => $element['options_group']['display_group']['class']['#value'],
      'new_window' => $element['options_group']['link_group']['new_window']['#value'],
    ];

    $form_state->setValueForElement($element, $values);
  }

  /**
   * Creates an array of available classes.
   *
   * @return array
   *   An associative array of available classes.
   */
  private function getClassOptions() {
    $classes = [];

    // Add the heading classes.
    $classes = HeaderFieldWidget::HEADING_CLASSES;

    $allowed_classes = explode("\n", $this->getFieldSetting('allowed_classes'));
    $allowed_classes = array_map('trim', $allowed_classes);
    $allowed_classes = array_filter($allowed_classes, 'strlen');

    foreach ($allowed_classes as $allowed_class) {
      $matches = [];

      if (preg_match('/(.*)\|(.*)/', $allowed_class, $matches)) {
        $key = trim($matches[1]);
        $value = trim($matches[2]);
      }

      $classes[$key] = $value;
    }

    // Merge in the additional classes.
    $classes = array_merge($classes, HeaderFieldWidget::ADDITIONAL_CLASSES);

    return $classes;
  }

}
