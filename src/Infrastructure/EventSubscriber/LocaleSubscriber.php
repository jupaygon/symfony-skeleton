<?php

declare(strict_types=1);

namespace App\Infrastructure\EventSubscriber;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class LocaleSubscriber implements EventSubscriberInterface
{
    /** @param array<string, array{code: string, name: string}> $allowedLanguages */
    public function __construct(
        #[Autowire(param: 'app.languages')]
        private array $allowedLanguages,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // After SessionListener (128), before LocaleListener (16)
            KernelEvents::REQUEST => [['applyLocale', 100]],
        ];
    }

    /**
     * Apply locale from query param or session.
     * Allowlisted against app.languages so an attacker can't inject arbitrary _locale values.
     */
    public function applyLocale(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$event->isMainRequest()) {
            return;
        }

        $queryLocale = $request->query->get('_locale');
        if (\is_string($queryLocale) && $this->isAllowed($queryLocale)) {
            $request->attributes->set('_locale', $queryLocale);
            $request->setLocale($queryLocale);
            if ($request->hasSession()) {
                $request->getSession()->set('_locale', $queryLocale);
            }

            return;
        }

        if ($request->hasSession() && $request->getSession()->has('_locale')) {
            $sessionLocale = $request->getSession()->get('_locale');
            if (\is_string($sessionLocale) && $this->isAllowed($sessionLocale)) {
                $request->attributes->set('_locale', $sessionLocale);
                $request->setLocale($sessionLocale);
            }
        }
    }

    private function isAllowed(string $locale): bool
    {
        return array_key_exists($locale, $this->allowedLanguages);
    }
}
