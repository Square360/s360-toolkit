<?php

namespace Drupal\s360_toolkit_header_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\link\Plugin\Field\FieldWidget\LinkWidget;

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

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $item = $items[$delta];

    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    // Attach the admin styles for the "header-field".
    $element['#attached']['library'][] = 's360_toolkit_header_field/s360-toolkit-header-field';
    $element['#attributes'] = [
      'class' => ['header-field'],
    ];

    // Modify the title field.
    $element['title']['#title'] = 'Heading text';
    $element['title']['#required'] = TRUE;

    // Start heading group.
    $element['heading_group'] = [
      '#type' => 'fieldset',
      '#title' => t('Heading'),
      '#attributes' => [
        'class' => ['header-field__heading-group'],
      ],
    ];
    $element['heading_group']['tag'] = [
      '#title' => 'Semantic Tag',
      '#type' => 'select',
      '#options' => [
        'h2' => 'H2',
        'h3' => 'H3',
        'h4' => 'H4',
        'h5' => 'H5',
        'h6' => 'H6',
      ],
      '#default_value' => $item->options['tag'] ?? 'h2',
    ];
    $element['heading_group']['title'] = $element['title'];

    // Subtitle is not part of heading group.
    $element['subtitle'] = [
      '#title' => 'Subtitle text',
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
      '#open' => FALSE,
    ];
    $element['options_group']['display_group']['center'] = [
      '#title' => 'Center',
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
      '#title' => 'Style',
      '#type' => 'select',
      '#options' => $this->getStyleClasses(),
      '#empty_option' => t('- Select -'),
      '#default_value' => $item->options['class'] ?? '',
    ];

    // Start link group.
    $element['options_group']['link_group'] = [
      '#type' => 'details',
      '#title' => t('Link options'),
      '#open' => FALSE,
    ];

    // It's weird to see "<nolink>" in the uri field, so remove it and let the
    // processFields function handle the empty value.
    if ($element['uri']['#default_value'] === '<nolink>') {
      $element['uri']['#default_value'] = '';
    }

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
    else {
      $values['uri'] = $element['options_group']['link_group']['uri']['#value'];
    }

    $values['subtitle'] = $element['subtitle']['#value'];

    // Since the original title was moved into a group and unset, it needs to
    // be accessed via 'header_group'.
    $values['title'] = $element['heading_group']['title']['#value'];

    $form_state->setValueForElement($element, $values);
  }

  /**
   * Copy custom values into the option field.
   */
  public static function processOptions(&$element, FormStateInterface $form_state, $form) {
    $values = $form_state->getValue($element['#parents']);

    $values['options'] = [
      'tag' => $element['heading_group']['tag']['#value'],
      'center' => $element['options_group']['display_group']['center']['#value'],
      'uppercase' => $element['options_group']['display_group']['uppercase']['#value'],
      'style' => $element['options_group']['display_group']['style']['#value'],
      'new_window' => $element['options_group']['link_group']['new_window']['#value'],
    ];

    $form_state->setValueForElement($element, $values);
  }

  /**
   * Creates an array of available style classes.
   *
   * @return array
   *   An associative array of available style classes.
   */
  private function getStyleClasses() {
    $styles = [];

    // Array of heading level classes.
    $headings = [
      'header-group--heading-2' => 'Heading 2',
      'header-group--heading-3' => 'Heading 3',
      'header-group--heading-4' => 'Heading 4',
      'header-group--heading-5' => 'Heading 5',
      'header-group--heading-6' => 'Heading 6',
    ];

    $styles = $headings;

    $classes = explode("\n", $this->getFieldSetting('allowed_classes'));
    $classes = array_map('trim', $classes);
    $classes = array_filter($classes, 'strlen');

    foreach ($classes as $class) {
      $matches = [];

      if (preg_match('/(.*)\|(.*)/', $class, $matches)) {
        $key = trim($matches[1]);
        $value = trim($matches[2]);
      }

      $styles['header-group--' . $key] = $value;
    }

    // Add a visually hidden class.
    $styles['visually-hidden'] = 'Visually Hidden';

    return $styles;
  }

}
