<?php

namespace Drupal\frontend_router\PageCache\ResponsePolicy;

use Drupal\Core\PageCache\ResponsePolicyInterface;
use Drupal\frontend_router\EventSubscriber\FrontendRouterResponseSubscriber;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cache policy for fragment frontend-routed routes.
 *
 * This policy rule denies caching of responses generated for frontend-routed
 * routes.
 */
class FragmentNoCache implements ResponsePolicyInterface {

  /**
   * {@inheritdoc}
   */
  public function check(Response $response, Request $request) {
    if (FrontendRouterResponseSubscriber::isRouted()) {
      return static::DENY;
    }
  }

}
