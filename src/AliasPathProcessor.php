<?php
/**
 * @file
 * Contains \Drupal\entity_alias\PathProcessorAlias.
 */

namespace Drupal\entity_alias;

use Drupal\Core\PathProcessor\InboundPathProcessorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class AliasPathProcessor implements InboundPathProcessorInterface {

  /**
   * Processes the inbound path.
   *
   * @param string                                    $path
   *   The path to process, with a leading slash.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The HttpRequest object representing the current request.
   *
   * @return string
   *   The processed path.
   */
  public function processInbound($path, Request $request) {
    $redirect    = FALSE;
    $parsed_path = parse_url($path);

    if (isset($parsed_path['path'])) {
      $pieces = explode('-', $parsed_path['path']);
      $id     = end($pieces);

      if (is_numeric($id)) {
        $alias = \Drupal::service('path.alias_manager')
          ->getAliasByPath("/node/" . $id);

        if ($path != $alias) {
          $response = new RedirectResponse($alias);
          $response->send();
          }
      }
    }
  }
}