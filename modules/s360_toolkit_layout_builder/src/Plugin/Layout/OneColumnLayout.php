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
class OneColumnLayout extends LayoutDefault implements PluginFormInterface {

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['edge_to_edge'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Edge to Edge'),
      '#default_value' => $this->configuration['edge_to_edge'],
      '#description' => $this->t('When checked this layout will span the entire width of the page.'),
    ];

    $form['inset'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Inset'),
      '#default_value' => $this->configuration['inset'],
      '#description' => $this->t('When checked this layout will be inset.'),
    ];

    return parent::buildConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    $this->configuration['edge_to_edge'] = $form_state->getValue('edge_to_edge');
    $this->configuration['inset'] = $form_state->getValue('inset');
  }

  /**
   * {@inheritdoc}
   */
  public function build(array $regions) {
    $build = parent::build($regions);

    if ($this->configuration['edge_to_edge'] == 1) {
      $build['#attributes']['class'][] = 'layout--edge-to-edge';
    }

    if ($this->configuration['inset'] == 1) {
      $build['#attributes']['class'][] = 'layout--inset';
    }

    return $build;
  }

}
