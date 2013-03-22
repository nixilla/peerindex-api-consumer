PeerIndexApiConsumer
====================

This is small library that allows you to easily interact with `PeerIndex API`_.

.. _`PeerIndex API`: https://developers.peerindex.com/page

.. image:: https://travis-ci.org/nixilla/peerindex-api-consumer.png?branch=master

Installation
````````````

The easiest way - via packagist:

..code-block:: json

    {
        "require": {
            "nixilla/klout-api-consumer": "~0.5"
        }
    }

Usage:
``````

See example.php

Contributing (with tests):
``````````````````````````

..code-block:: sh

    git clone git://github.com/nixilla/peerindex-api-consumer.git && \
    cd peerindex-api-consumer && \
    mkdir bin && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=bin && \
    ./bin/composer.phar install --dev && \
    ./bin/phpunit


Now you can add your code and send me pull request.
