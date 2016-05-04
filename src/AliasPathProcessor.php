<?php

/**
 * @file
 * Contains \Drupal\smart_alias\PathProcessorAlias.
 */

namespace Drupal\smart_alias;

use Drupal\Core\PathProcessor\InboundPathProcessorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AliasPathProcessor.
 *
 * @package Drupal\smart_alias
 */
class AliasPathProcessor implements InboundPathProcessorInterface {

  /**
   * {@inheritdoc}
   */
  public function processInbound($path, Request $request) {
    $request_uri = $request->getRequestUri();
    $pieces = explode('-', $request_uri);
    $id = end($pieces);

    if (is_numeric($id)) {
      $alias = \Drupal::service('path.alias_manager')
        ->getAliasByPath('/node/' . $id);
      if ($path !== $alias) {
        $response = new RedirectResponse($alias);
        $response->send();
      }
    }

    return $path;
  }

}
