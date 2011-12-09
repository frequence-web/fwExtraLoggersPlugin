# fwExtraLoggersPlugin

this plugin adds some loggers (mail logger for the moment) to symfony 1.x

## Mail logger


The mail logger sends you logs by mail. It's useful for logging errors in production.

The plugin doesn't use the factories mailer configuration, you must define a separated config.
(It allows you to use an other SMTP server, and adds the ability to log errors that happen before or during the factories
initialisation)

### Usage

factories.yml

    prod:
      logger:
        class:   fwMailLogger
        param:
          from:    log@yourdomainname.com
          to:      your.mail.address@foo.bar
          transport:
            class: Swift_SmtpTransport
            param:
              host: localhost
              port: 25

