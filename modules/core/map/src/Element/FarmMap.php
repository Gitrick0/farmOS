<?php

namespace Drupal\farm_map\Element;

use Drupal\Component\Utility\Html;
use Drupal\Core\Render\Element\RenderElement;
use Drupal\farm_map\Event\MapRenderEvent;

/**
 * Provides a farm_map render element.
 *
 * @RenderElement("farm_map")
 */
class FarmMap extends RenderElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    $default_name = 'default';
    return [
      '#pre_render' => [
        [$class, 'preRenderMap'],
      ],
      '#theme' => 'farm_map',
      '#map_name' => $default_name,
    ];
  }

  /**
   * Pre-render callback for the map render array.
   *
   * @param array $element
   *   A renderable array containing a #map_name property, which will be used
   *   as the map div ID.
   *
   * @return array
   *   A renderable array representing the map.
   */
  public static function preRenderMap(array $element) {

    // Set the id to the map name.
    $map_id = Html::getUniqueId($element['#map_name']);
    $element['#attributes']['id'] = $map_id;

    // Get the map type.
    /** @var \Drupal\farm_map\Entity\MapTypeInterface $map */
    $map = \Drupal::entityTypeManager()->getStorage('map_type')->load($element['#map_name']);

    // Add the farm-map class.
    $element['#attributes']['class'][] = 'farm-map';

    // Attach the farmOS-map and farm_map libraries.
    $element['#attached']['library'][] = 'farm_map/farmOS-map';
    $element['#attached']['library'][] = 'farm_map/farm_map';

    // Include the map options.
    $map_options = $map->getMapOptions();
    $element['#attached']['drupalSettings']['farm_map'][$map_id]['options'] = $map_options;

    // Create and dispatch a MapRenderEvent.
    $event = new MapRenderEvent($map, $element);
    \Drupal::service('event_dispatcher')->dispatch(MapRenderEvent::EVENT_NAME, $event);

    // Return the element.
    return $event->element;
  }

}
