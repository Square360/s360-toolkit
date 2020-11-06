<?php

namespace Drupal\s360_toolkit_heading_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'heading_field_widget' widget.
 *
 * @FieldWidget(
 *   id = "heading_field_widget",
 *   label = @Translation("Heading Field"),
 *   field_types = {
 *     "heading_field_type"
 *   }
 * )
 */
class HeadingFieldWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(
    FieldItemListInterface $items,
    $delta,
    array $element,
    array &$form,
    FormStateInterface $form_state
  ) {
    $item = $items[$delta];

    $element['#attached']['library'][] = 's360_toolkit_heading_field/s360-toolkit-heading-field';

    $element += [
      '#type' => 'fieldset',
      '#attributes' => [
        'class' => [
          's360-toolkit-heading-field',
        ],
      ],
    ];

    $element['tag'] = [
      '#type' => 'select',
      '#options' => [
        'h2' => 'h2',
        'h3' => 'h3',
        'h4' => 'h4',
        'h5' => 'h5',
        'h6' => 'h6',
      ],
      '#default_value' => isset($item->options['tag']) ? $item->options['tag'] : 'h2',
    ];

    $element['title'] = [
      '#type' => 'textfield',
      '#default_value' => isset($item->title) ? $item->title : NULL,
      '#maxlength' => 255,
      '#access' => $this->getFieldSetting('title') != DRUPAL_DISABLED,
      '#required' => $this->getFieldSetting('title') === DRUPAL_REQUIRED && $element['#required'],
    ];

    $element['advanced_options'] = [
      '#type' => 'details',
      '#title' => $this->t('Show advanced options'),
      '#attributes' => [
        'class' => [
          's360-toolkit-heading-field__options',
        ],
      ],
    ];

    $element['advanced_options']['visible'] = [
      '#title' => t('Hide'),
      '#type' => 'checkbox',
      '#default_value' => isset($item->options['visible']) ? $item->options['visible'] : 0,
      '#description' => t('When checked, hide the title while still making it available to screen readers.'),
    ];

    $element['#element_validate'][] = [
      get_called_class(),
      'processFields',
    ];

    return $element;
  }

  /**
   * Copy custom values into the option field.
   */
  public static function processFields(&$element, FormStateInterface $form_state, $form) {
    $values = $form_state->getValue($element['#parents']);

    $values['options'] = [
      'tag' => $values['tag'],
      'visible' => $values['advanced_options']['visible'],
    ];

    $values['title'] = $values['title'];

    $form_state->setValueForElement($element, $values);
  }

}
