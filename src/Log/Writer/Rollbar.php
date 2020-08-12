<?php
namespace ZfRollbar\Log\Writer;

use Rollbar\Payload\Level;
use Zend\Log\Logger;
use Zend\Log\Writer\AbstractWriter;

class Rollbar extends AbstractWriter
{
    private $option = [];

    protected $levelMap = array(
        Logger::DEBUG     => Level::DEBUG,
        Logger::INFO      => Level::INFO,
        Logger::NOTICE    => Level::NOTICE,
        Logger::WARN      => Level::WARNING,
        Logger::ERR       => Level::ERROR,
        Logger::CRIT      => Level::CRITICAL,
        Logger::ALERT     => Level::ALERT,
        Logger::EMERG     => Level::EMERGENCY,
    );

    private $defaultPriority = 'error';

    /**
     * @inheritDoc
     */
    public function __construct($options = null) {
        parent::__construct($options);
        if (is_array($options)) {
            $this->option = $options;
        }
    }

    /**
     * @inheritDoc
     */
    protected function doWrite(array $event)
    {
        if ($this->formatter) {
            $line = $this->formatter->format($event);
        }
        else {
            $line = $event['message'];
        }

        if (array_key_exists($event['priority'], $this->levelMap)) {
            $priority = $this->levelMap[$event['priority']];
        } else {
            $priority = $this->defaultPriority;
        }

        \Rollbar\Rollbar::init($this->option);
        \Rollbar\Rollbar::logger()->log($priority, $event['message'], $event, true);
    }
}
