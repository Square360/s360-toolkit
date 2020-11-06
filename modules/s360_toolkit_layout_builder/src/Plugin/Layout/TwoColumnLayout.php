<?php

namespace Drupal\s360_toolkit_layout_builder\Plugin\Layout;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Layout\LayoutDefault;
use Drupal\Core\Plugin\PluginFormInterface;

/**
 * Configurable one column layout plugin class.
 *
 * @internal
 *   Plugin classes are internal.
 */
class TwoColumnLayout extends LayoutDefault implements PluginFormInterface {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    $configuration = parent::defaultConfiguration();

    return $configuration + [
      'column_width' => $this->getDefaultWidth(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['column_width'] = [
      '#type' => 'select',
      '#title' => $this->t('Column width'),
      '#default_value' => $this->configuration['column_width'],
      '#options' => $this->getWidthOptions(),
      '#description' => $this->t('Choose the column width for this layout.'),
    ];

    return parent::buildConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    $this->configuration['column_width'] = $form_state->getValue('column_width');
  }

  /**
   * {@inheritdoc}
   */
  public function build(array $regions) {
    $build = parent::build($regions);

    $build['#attributes']['class'][] = 'layout--' . $this->configuration['column_width'];

    return $build;
  }

  /**
   * Gets the column width options for the configuration form.
   *
   * @return string[]
   *   The column width options array where the keys are strings that will be
   *   added to the CSS classes and the values are the human readable labels.
   */
  protected function getWidthOptions() {
    return [
      '50-50' => '50%/50%',

      '60-40' => '60%/40%',
      '40-60' => '40%/60%',

      '70-30' => '70%/30%',
      '30-70' => '30%/70%',

      '75-25' => '75%/25%',
      '25-75' => '25%/75%',
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultWidth() {
    return '50-50';
  }

}
