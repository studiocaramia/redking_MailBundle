RedkingMailBundle
=====================

This bundle brings Mailgun to symfony 2 via swiftmailer

## Installation

Add bundle to composer.json

```js
{
    "require": {
        "redking/sonata-mail-bundle": "dev-master"
    },
    "repositories": [
        {
            "type": "vcs",
            "url":  "git@bitbucket.org:redkingteam/redkingmailbundle.git"
        }
    ]
}
```

Register the bundle

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        new Redking\Bundle\MailBundle\RedkingMailBundle(),
        // Bundle gérant les string twig
        new LK\TwigstringBundle\LKTwigstringBundle(),
    );
}
```

## Configuration

Rajouter les données de l'API dans les paramètres : 

``` ymal
# app/config/parameters.yml.dist
parameters: 
    mailer_transport:  mailgun
    #...
    mailgun.key: ~
    mailgun.domain: ~
```
Et dans la configuration du projet : 

``` ymal
# app/config/config.yml
redking_mail:
    key: "%mailgun.key%"
    domain: "%mailgun.domain%"
    # Définition des fonctionnalités REST
    rest:
        # Recherche simple dans email_activities
        search_activities: true
        # Gestion d'une messagerie pour utilisateurs
        messaging: false
```

## Routage

### API REST

``` ymal
# app/config/routing.yml

redking_mail_rest_api:
    resource: "@RedkingMailBundle/Resources/config/routing_rest.xml"
    type: rest
    prefix:   /api/v1
```


### Webhooks de mailgun

``` ymal
# app/config/routing.yml

RedkingMailBundle:
    resource: '@RedkingMailBundle/Resources/config/routing.xml'
    prefix: /_redking-mail
```

Il faut ensuite rajouter les urls dans l'interface mailgun, par exemple :

`/_redking-mail/mailgun-hook/opened`
