<?php
/**
 * Created by PhpStorm.
 * User: evrard
 * Date: 2019/01/07
 * Time: 11:07 AM
 */

namespace App\Event\Listener;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\GuardEvent;

class UserReviewListener implements EventSubscriberInterface
{
    public function guardReview(GuardEvent $event)
    {
        $user = $event->getSubject();
        $email = $user->getEmail();

        if (empty($email)) {//user with no email with not be allowed
            $event->setBlocked(true);
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            'workflow.user_admin.guard.submit_for_review' => array('guardReview'),
        );
    }
}