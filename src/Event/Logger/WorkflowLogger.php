<?php
/**
 * Created by PhpStorm.
 * User: evrard
 * Date: 2019/01/07
 * Time: 9:26 AM
 */

namespace App\Event\Logger;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class WorkflowLogger implements EventSubscriberInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onLeave(Event $event)
    {
        $classInfo = new \ReflectionClass($event->getSubject());
        $className = $classInfo->getShortName();

        $this->logger->alert(sprintf(
            'Transaction "%s" from "%s" to "%s" performed on "%s" (id: "%s")',
            $event->getTransition()->getName(),
            implode(', ', array_keys($event->getMarking()->getPlaces())),
            implode(', ', $event->getTransition()->getTos()),
            $className,
            $event->getSubject()->getId()
        ));
    }

    public static function getSubscribedEvents()
    {
        return array(
            'workflow.leave' => 'onLeave',
        );
    }
}