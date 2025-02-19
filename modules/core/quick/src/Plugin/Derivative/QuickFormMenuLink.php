<?php

namespace Drupal\farm_quick\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\farm_quick\QuickFormManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides menu links for quick forms.
 */
class QuickFormMenuLink extends DeriverBase implements ContainerDeriverInterface {

  /**
   * The quick form manager.
   *
   * @var \Drupal\farm_quick\QuickFormManager
   */
  protected $quickFormManager;

  /**
   * FarmQuickMenuLink constructor.
   *
   * @param \Drupal\farm_quick\QuickFormManager $quick_form_manager
   *   The quick form manager.
   */
  public function __construct(QuickFormManager $quick_form_manager) {
    $this->quickFormManager = $quick_form_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('plugin.manager.quick_form')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $links = [];

    // Load quick forms.
    $quick_forms = $this->quickFormManager->getDefinitions();

    // Add a top level menu parent.
    if (!empty($quick_forms)) {
      $links['farm.quick'] = [
        'title' => 'Quick forms',
        'route_name' => 'farm.quick',
        'weight' => -100,
      ] + $base_plugin_definition;
    }

    // Add a link for each quick form.
    foreach ($quick_forms as $quick_form) {
      $route_id = 'farm.quick.' . $quick_form['id'];
      $links[$route_id] = [
        'title' => $quick_form['label'],
        'parent' => 'farm.quick',
        'route_name' => $route_id,
      ] + $base_plugin_definition;
    }

    return $links;
  }

}
