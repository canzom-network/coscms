<?php

/**
 * File containing an example shell command 
 */

/**
 * example shell command
 * @ignore
 */
function hello_world_output () {
    echo "Hello world" . "\n";
}

/**
 * example shell command
 * @ignore
 * @param type $params
 */
function hello_world_something ($params) {
    echo $params['sentence'] . "\n";
}

self::setCommand('hello_world', array(
    'description' => 'Command saying hello world.',
));

self::setOption('hello_world_output', array(
    'long_name'   => '--say-it',
    'description' => 'Set this flag in order to really say hello world',
    'action'      => 'StoreTrue'
));

self::setOption('hello_world_something', array(
    'long_name'   => '--say-something',
    'short_name' => '-s',
    'description' => 'Set this flag in order to say something else',
    'action'      => 'StoreTrue'
));

self::setArgument(
    'sentence',
    array('description'=> 'Say something else',
        'optional' => true,
));
