#!/usr/local/bin/php
<?php

if ($argc == 1) { 
    echo 'Error. No argument supplied';
} else if ($argc == 2) {
    echo 'Error. Missing argument %controller_name%';
} else {
    switch ($argv[1]) {
        case 'create-controller':
            $content = file_get_contents(__DIR__ . '/assets/controller.txt');
            $file_content = str_replace('%controller_name%', $argv[2], $content); 
            file_put_contents(__DIR__ . "/../src/controllers/{$argv[2]}.php", $file_content);
            echo "{$argv[2]} created.";   
            break;
        case 'create-module':
            $content = file_get_contents(__DIR__ . '/assets/module.txt'); 
            $file_content = str_replace('%module_name%', $argv[2], $content); 
            file_put_contents(__DIR__ . "/../src/engine/{$argv[2]}.php", $file_content);
            echo "{$argv[2]} created."; 
            break;
        default:
        echo "Unknown command";
    }    
}