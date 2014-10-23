ParsePromos Class
============

Parse promotion arrays from the Wayne State University API

[![Latest Stable Version](https://poser.pugx.org/waynestate/parse-promos/v/stable.svg)](https://packagist.org/packages/waynestate/parse-promos)
[![Build Status](https://travis-ci.org/waynestate/parse-promos.svg?branch=develop)](https://travis-ci.org/waynestate/parse-promos)
[![Total Downloads](https://poser.pugx.org/waynestate/parse-promos/downloads.svg)](https://packagist.org/packages/waynestate/parse-promos)
[![License](https://poser.pugx.org/waynestate/parse-promos/license.svg)](https://packagist.org/packages/waynestate/parse-promos)

Installation
------------

To install this library, run the command below and you will get the latest version

    composer require waynestate/parse-promos


Usage
------------

Create the object

    # start.php

    use Waynestate\Promotions\ParsePromos;

    ...

    $parsePromos = new Waynestate\Promotions\ParsePromos();

Make an API call for promotions

    # controller.php

    // Promotion groups to pull ( id => short_name )
    $group_reference = array(
        '123' => 'circle',
        '124' => 'alumni-links',
        '125' => 'contact',
        '126' => 'progress-amount',
        '127' => 'progress-text',
    );

    // How to parse each group after the return ( short_name => config_option )
    $group_config = array(
        'contact' => 'first',
        'progress-amount' => 'limit:1',
        'progress-text' => 'randomize|first',
    );

    // Pull all the active items from the API
    $params = array(
        'promo_group_id' => array_keys($group_reference),
        'is_active' => '1',
        'ttl' => TTL,
    );

    // Get the raw promotions from the API
    $raw_promos = $api->sendRequest('cms.promotions.listing', $params);

    // Parse the promotions based on the config set
    $parsed_promos = $parsePromos->parse($raw_promos, $group_reference, $group_config);

Config options

    'first' = Return only the first item in the list (not part of an array)
    'randomize' = Take the returned list and mix it up
    'limit:#' = Return just # number of results from the list
    'order:start_date_desc' = Return an ordered list by 'start_date' DESC
    'order:start_date_asc' = Return an ordered list by 'start_date' ASC
    'order:display_date_desc' = Return an ordered list by 'display_date' DESC
    'order:display_date_asc' = Return an ordered list by 'display_date' ASC
    'page_id:#' = Return only promotions in the list marked for this page

    'order:start_date_desc|first' = Options can be run in combination by piping to another config


Tests

    phpunit

Code Coverage

    phpunit --coverage-html ./coverage