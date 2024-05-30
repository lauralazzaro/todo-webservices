<?php

namespace App\Helper\EventsSubscribers;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class GeneratedPasswordResetSubscriber implements EventSubscriberInterface
{
    private Security $security;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(Security $security, UrlGeneratorInterface $urlGenerator)
    {
        $this->security = $security;
        $this->urlGenerator = $urlGenerator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['forcePasswordChange', 0]],
        ];
    }

    public function forcePasswordChange(RequestEvent $event): void
    {
        $user = $this->security->getUser();

        if (
            ($user instanceof User) &&
            ($user->isPasswordGenerated()) &&
            ($event->getRequest()->get('_route') !== 'user_edit_generated_password')
        ) {
            $resetPasswordUrl = $this->urlGenerator->generate('user_edit_generated_password', ['id' => $user->getId()]);

            $response = new RedirectResponse($resetPasswordUrl);
            $response->send();
        }
    }
}
