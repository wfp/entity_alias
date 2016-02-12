<?php

/**
 * @file
 * Contains \Drupal\entity_alias\ნEntityAliasRequsetSubscriber.
 */

namespace Drupal\entity_alias\EventSubscriber;

use Drupal\entity_alias\AliasPathProcessor;
use Drupal\entity_alias\PathProcessorAlias;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ნEntityAliasRequsetSubscriber.
 *
 * @package Drupal\entity_alias
 */
class EntityAliasRequsetSubscriber implements EventSubscriberInterface {

  /**
   * Constructor.
   */
  public function __construct() {

  }


  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents() {
    $events['kernel.request'] = ['resolveUrl', 1000];
    return $events;
  }


  /**
   * This method is called whenever the kernel.request event is
   * dispatched.
   *
   * @param GetResponseEvent $event
   */
  public function resolveUrl(Event $event) {
    $aliasPathProcessor = new AliasPathProcessor();
    $aliasPathProcessor->processInbound($event->getRequest()->server->get('REDIRECT_URL'),
      $event->getRequest());

    drupal_set_message('Event kernel.request thrown by Subscriber in module entity_alias.',
      'status', TRUE);
  }

}
