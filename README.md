ZNDSIMApiBundle
====================

This a [Symfony3](http://symfony.com) implementation of [zandu user bundle](http://zandu.cd/).

> ApiBundle is a projetc wich configure all default common stacks will be defini in deferents modules

Server-side, it uses [FOSRestBundle](https://github.com/FriendsOfSymfony/FOSRestBundle) as REST Api generator, [JMSSerializerBundle](http://jmsyst.com/bundles/JMSSerializerBundle) as JSON serializer.
Demo
----

Install
-------

First, [install Symfony using Composer](http://symfony.com/doc/current/book/installation.html).
Go to your application directory and use composer to install the bundle and its dependencies:

    composer require ghostcodelover/api-bundle

Next, enable these bundles in `AppKernel.php`:

```php
// app/AppKernel.php
public function registerBundles()
{
    return array(
        // ...
        new JMS\SerializerBundle\JMSSerializerBundle(),
        new FOS\RestBundle\FOSRestBundle(),
        new ZND\SIM\ApiBundle\ZNDApiBundle(),
        // ...
    );
}
```

And the routes to `app/config/routing.yml`:

Dump assets if you want to use the app in prod mode:

    php app/console assetic:dump --env=prod --no-debug

Done! Open *http://localhost/app_dev.php/* (don't fshopet the trailing slash) in your browser and try this Symfony implementation of Api.

Credits
-------

This bundle has been created by [Mukendi Emmanuel](mukendiemmanuel15@gmail.cd)