<?php

namespace App\EventSubscriber;

use Twig\Environment;
use App\Repository\CathegoriesRepository;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DropdownCathegoriesSubscriber implements EventSubscriberInterface {
    
    const ROUTES = ['cathegorie_index', 'cours_index'];

    public function __construct (
        private CathegoriesRepository $repo,
        private Environment $twig
    ) {}

    public function injectGlobalVariable(RequestEvent $event) {
        $route = $event->getRequest()->get('_route');
        if (in_array($route, self::ROUTES)) {
            $cathegories = $this->repo->findAll();
            $this->twig->addGlobal('allCathegories', $cathegories);
        }
    }

    public static function getSubscribedEvents() {
        return [KernelEvents::REQUEST => 'injectGlobalVariable'];
    }
}