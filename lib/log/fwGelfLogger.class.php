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
   * @var string
   */
  protected $facility;

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

    if (isset($options['facility']))
    {
      $this->facility = $options['facility'];
    }
  }

  /**
   * @{inheritDoc}
   */
  protected function doLog($log, $priority)
  {
    $message = new GELFMessage;
    $message
      ->setFullMessage($log)
      ->setShortMessage($log)
      ->setLevel($priority)
      ->setTimestamp(time())
      ->setHost(gethostname())
      ->setFacility($this->facility);

    try
    {
      $request = sfContext::getInstance()->getRequest();
      if ($request instanceof sfWebRequest)
      {
        $pathInfos = $request->getPathInfoArray();
        $message->setHost($pathInfos['HTTP_HOST']);
        $message->setAdditional('request_parameters', $request->getRequestParameters());
      }
    }
    catch (sfException $e) {}

    $this->publisher->publish($message);
  }
}
