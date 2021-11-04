<?php

namespace ZfRollbar;

use Rollbar\Payload\Level;
use Rollbar\Rollbar;
use Laminas\Mvc\MvcEvent;

class Module
{
    private $config;

    public function getConfig()
    {
        return include(__DIR__ . '/../config/module.config.php');
    }

    /**
     * @param MvcEvent $e The MvcEvent instance
     * @return void
     */
    public function onBootstrap(MvcEvent $e): void
    {
        $application   = $e->getApplication();
        $this->config = $application->getServiceManager()->get('Config');

        if (!empty($this->config['rollbar']['access_token'])) {
            $eventManager = $application->getEventManager();
            $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'onError']);
            $eventManager->attach(MvcEvent::EVENT_RENDER_ERROR, [$this, 'onError']);
        }
    }

    public function onError(MvcEvent $event)
    {
        $exception = $event->getParam('exception');
        Rollbar::init($this->config['rollbar']);
        Rollbar::logger()->log(Level::ERROR, $exception, [], true);
    }
}
