<?php

class fwMailLogger extends sfLogger
{
  protected
    $options = null,
    $mailer  = null;

  public function initialize(sfEventDispatcher $dispatcher, $options = array())
  {
    if (!isset($options['from']))
    {
      throw new sfConfigurationException('You must provide a "from" parameter for this logger.');
    }
    if (!isset($options['to']))
    {
      throw new sfConfigurationException('You must provide a "to" parameter for this logger.');
    }

    parent::initialize($dispatcher, $options);
    $this->options = $options;
  }

  /**
   * Logs a message.
   *
   * @param string $message   Message
   * @param string $priority  Message priority
   */
  protected function doLog($message, $priority)
  {
    $subject = $body = $message;

    if (isset($this->options['subject_pattern']))
    {
      $subject = strtr($this->options['subject_pattern'], array('%priority%' => $priority, '%message%' => $message));
    }

    if (isset($this->options['body_pattern']))
    {
      $body = strtr($this->options['body_pattern'], array('%priority%' => $priority, '%message%' => $message));
    }

    $this->getMailer()->composeAndSend($this->options['from'], $this->options['to'], $subject, $body);
  }

  /**
   * @return sfMailer
   */
  protected function getMailer()
  {
    if (null === $this->mailer)
    {
      $this->mailer = new sfMailer($this->dispatcher, $this->options);
    }

    return $this->mailer;
  }

}
