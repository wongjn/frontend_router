<?php

namespace Drupal\frontend_router\EventSubscriber;

use Drupal\Component\Utility\Html;
use Drupal\Core\EventSubscriber\MainContentViewSubscriber;
use Drupal\Core\Theme\ThemeManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Frontend router integration subscriber for requests.
 */
class FrontendRouterResponseSubscriber implements EventSubscriberInterface {

  /**
   * The query parameter for a frontend router request.
   *
   * @var string
   */
  const FRONTEND_ROUTER_FORMAT = 'frontend_router';

  /**
   * The theme manager.
   *
   * @var \Drupal\Core\Theme\ThemeManagerInterface
   */
  protected $themeManager;

  /**
   * Whether the current request is being routed from the frontend router.
   *
   * @var bool
   */
  protected static $routed;

  /**
   * Gets whether the current request is being routed from the frontend router.
   *
   * @return bool
   *   Returns TRUE if for the frontend router.
   */
  public static function isRouted() {
    if (!isset(self::$routed)) {
      $wrapper_format = \Drupal::requestStack()
        ->getMasterRequest()
        ->get(MainContentViewSubscriber::WRAPPER_FORMAT);

      self::$routed = $wrapper_format == self::FRONTEND_ROUTER_FORMAT;
    }

    return self::$routed;
  }

  /**
   * Constructs a new FrontendRouterResponseSubscriber.
   *
   * @param \Drupal\Core\Theme\ThemeManagerInterface $theme_manager
   *   The theme manager.
   */
  public function __construct(ThemeManagerInterface $theme_manager) {
    $this->themeManager = $theme_manager;
  }

  /**
   * Returns a string to notify the frontend router of un-routable request.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   The event to process.
   */
  public function onRequest(GetResponseEvent $event) {
    if (!self::isRouted()) {
      return;
    }

    $page_state = $event->getRequest()->get('ajax_page_state');
    if (!$page_state || $page_state['theme'] != $this->themeManager->getActiveTheme()->getName()) {
      $error_response = new Response('__INVALID__', Response::HTTP_BAD_REQUEST, ['Content-Type' => 'text/plain']);
      $event->setResponse($error_response);
    }
  }

  /**
   * Removes any noscript HTML tags.
   *
   * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
   *   The response event.
   */
  public function onResponse(FilterResponseEvent $event) {
    // Only care about partial (frontend-routed) HTML responses.
    if (!self::isRouted() || stripos($event->getResponse()->headers->get('Content-Type'), 'text/html') === FALSE) {
      return;
    }

    $response = $event->getResponse();
    $response->setContent(static::filterNoScriptTags($response->getContent()));
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
  public static function filterNoScriptTags($html_markup) {
    // No <noscript> tags found, return early.
    if (stripos($html_markup, '<noscript') === FALSE) {
      return $html_markup;
    }

    $dom = new \DOMDocument();
    @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html_markup);

    foreach ($dom->getElementsByTagName('noscript') as $node) {
      $node->parentNode->removeChild($node);
    }

    return $dom->saveHTML();
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // Just after DynamicPageCacheSubscriber::onRequest()
    $events[KernelEvents::REQUEST][] = ['onRequest', 26];
    // Should run after any other response subscriber that modifies the markup.
    // This is just after ActiveLinkResponseFilter (priority -512).
    $events[KernelEvents::RESPONSE][] = ['onResponse', -513];

    return $events;
  }

}
