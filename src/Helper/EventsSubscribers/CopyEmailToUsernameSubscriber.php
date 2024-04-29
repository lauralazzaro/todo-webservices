<?php

namespace App\Helper\EventsSubscribers;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class CopyEmailToUsernameSubscriber implements EventSubscriberInterface
{

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::SUBMIT => 'onFormSubmit',
        ];
    }

    public function onFormSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        // Copy the value of the email field to the username field
        $data->setUsername($data->getEmail());

        // Update the form data
        $event->setData($data);
    }
}