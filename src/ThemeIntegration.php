<?php

namespace Drupal\frontend_router;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Theme\ThemeManagerInterface;

/**
 * Class to handle theme integration to render content.
 */
class ThemeIntegration {

  /**
   * A config factory for retrieving required config objects.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The theme manager.
   *
   * @var \Drupal\Core\Theme\ThemeManagerInterface
   */
  protected $themeManager;

  /**
   * Constructs a new ThemeIntegration instance.
   *
   * @param \Drupal\Core\Theme\ThemeManagerInterface $theme_manager
   *   The theme manager.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   A config factory for retrieving required config objects.
   */
  public function __construct(ThemeManagerInterface $theme_manager, ConfigFactoryInterface $config_factory) {
    $this->themeManager = $theme_manager;
    $this->configFactory = $config_factory;
  }

  /**
   * Gets fragment region names.
   *
   * Fragment regions are defined as the ones that will vary on a page-by-page
   * basis and will be updated by the router.
   *
   * @param string $theme_name
   *   The theme name to get fragment regions for or leave empty for the active
   *   theme.
   *
   * @return string[]
   *   The list of fragment region names.
   */
  public function getFragmentRegions($theme_name = '') {
    if (!$theme_name) {
      $theme_name = $this->themeManager
        ->getActiveTheme()
        ->getName();
    }

    $regions = $this->configFactory
      ->get("$theme_name.settings")
      ->get('frontend_router.regions');

    if (is_array($regions)) {
      return array_keys(array_filter($regions));
    }

    return ['content'];
  }

  /**
   * Removes any noscript HTML tags.
   *
   * @param string $html_markup
   *   The HTML markup to remove noscript tags from.
   *
   * @return string
   *   The filtered HTML markup.
   */
  public function filterNoscriptTags($html_markup) {
    // No <noscript> tags found, return early.
    if (stripos($html_markup, '<noscript') === FALSE) {
      return $html_markup;
    }

    $dom = new \DOMDocument();
    // Load DOMDocument without any extra html, body or doctype HTML tags.
    @$dom->loadHTML($html_markup, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    foreach ($dom->getElementsByTagName('noscript') as $node) {
      $node->parentNode->removeChild($node);
    }
    return $dom->saveHTML();
  }

}
