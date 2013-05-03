<?php

/**
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class fwGelfLogger extends sfLogger
{
  protected static $levels = array(
    sfLogger::EMERG   => GELFMessage::EMERGENCY,
    sfLogger::ALERT   => GELFMessage::ALERT,
    sfLogger::CRIT    => GELFMessage::CRITICAL,
    sfLogger::ERR     => GELFMessage::ERROR,
    sfLogger::WARNING => GELFMessage::WARNING,
    sfLogger::NOTICE  => GELFMessage::NOTICE,
    sfLogger::INFO    => GELFMessage::INFO,
    sfLogger::DEBUG   => GELFMessage::DEBUG,
  );

  /**
   * @var GELFMessagePublisher
   */
  protected $publisher;

  /**
   * @{inheritDoc}
   */
  public function __construct(sfEventDispatcher $dispatcher, $options = array())
  {
    parent::__construct($dispatcher, $options);

    $this->publisher = new GELFMessagePublisher(
      $this->options['host'],
      isset($this->options['port']) ? $this->options['port'] : GELFMessagePublisher::GRAYLOG2_DEFAULT_PORT
    );
  }

  /**
   * @{inheritDoc}
   */
  protected function doLog($message, $priority)
  {
    $message = new GELFMessage;
    $message
      ->setFullMessage($message)
      ->setShortMessage($message)
      ->setLevel($priority)
      ->setTimestamp(time())
      ->setHost(gethostname());

    $this->publisher->publish($message);
  }
}
