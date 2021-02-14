<?php

namespace Drupal\s360_toolkit_header_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\link\Plugin\Field\FieldWidget\LinkWidget;

/**
 * Plugin implementation of the 'header_field_widget' widget.
 *
 * @FieldWidget(
 *   id = "header_field_widget",
 *   label = @Translation("Header Field"),
 *   field_types = {
 *     "header_field_type"
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

    $element['title']['#title'] = 'Heading text';
    $element['title']['#weight'] = -1;

    if (!$this->getFieldSetting('heading_as_link')) {
      unset($element['uri']);
    }

    $element['center'] = [
      '#title' => 'Center',
      '#type' => 'checkbox',
      '#default_value' => isset($item->options['center']) ? $item->options['center'] : FALSE,
    ];

    $element['uppercase'] = [
      '#title' => 'Uppercase',
      '#type' => 'checkbox',
      '#default_value' => isset($item->options['uppercase']) ? $item->options['uppercase'] : FALSE,
    ];

    $element['tag'] = [
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

    $element['style_class'] = [
      '#title' => 'Class',
      '#type' => 'select',
      '#options' => $this->getStyleClasses(),
      '#empty_option' => t('- Select -'),
      '#default_value' => $item->options['style_class'] ?? '',
    ];

    $element['subtitle'] = [
      '#title' => 'Subtitle',
      '#type' => 'textfield',
      '#default_value' => $item->subtitle ?? '',
    ];

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
    if ($element['uri']['#value'] === '' || !isset($element['uri'])) {
      $values['uri'] = '<nolink>';
    }

    $values['subtitle'] = $element['subtitle']['#value'];

    $form_state->setValueForElement($element, $values);
  }

  /**
   * Copy custom values into the option field.
   */
  public static function processOptions(&$element, FormStateInterface $form_state, $form) {
    $values = $form_state->getValue($element['#parents']);

    $values['options'] = [
      'tag' => $values['tag'],
      'center' => $values['center'],
      'uppercase' => $values['uppercase'],
      'style_class' => $values['style_class'],
    ];

    $form_state->setValueForElement($element, $values);
  }

  /**
   * Creates an array of available style classes.
   *
   * Combines the "allowed_classes" and basic h2-h6 classes.
   *
   * @return array
   *   An associative array of available classes.
   */
  private function getStyleClasses() {
    $values = [
      'h2' => 'Heading 2',
      'h3' => 'Heading 3',
      'h4' => 'Heading 4',
      'h5' => 'Heading 5',
      'h6' => 'Heading 6',
    ];

    $list = explode("\n", $this->getFieldSetting('allowed_classes'));
    $list = array_map('trim', $list);
    $list = array_filter($list, 'strlen');

    foreach ($list as $text) {
      $matches = [];

      if (preg_match('/(.*)\|(.*)/', $text, $matches)) {
        $key = trim($matches[1]);
        $value = trim($matches[2]);
      }

      $values[$key] = $value;
    }

    return $values;
  }

}
