#!/usr/local/bin/php
<?php

if ($argc == 1) { 
    echo 'Error. No argument supplied.';
} else if ($argc == 2) {
    echo 'Error. Missing arguments.';
} else {
    switch ($argv[1]) {
        case 'create-controller':
            if (file_exists(__DIR__ . "/../src/controllers/{$argv[2]}.php")) {
                echo "./src/controllers/{$argv[2]}.php already exists.";
                break;
            }
            $content = file_get_contents(__DIR__ . '/assets/controller.txt');
            $file_content = str_replace('%controller_name%', $argv[2], $content); 
            file_put_contents(__DIR__ . "/../src/controllers/{$argv[2]}.php", $file_content);
            echo "./src/controllers/{$argv[2]}.php created succesfully.";   
            break;
        case 'create-module':
            if (file_exists(__DIR__ . "/../src/engine/{$argv[2]}.php")) {
                echo "./src/engine/{$argv[2]}.php already exists.";
                break;
            }
            $content = file_get_contents(__DIR__ . '/assets/module.txt'); 
            $file_content = str_replace('%module_name%', $argv[2], $content); 
            file_put_contents(__DIR__ . "/../src/engine/{$argv[2]}.php", $file_content);
            echo "./src/engine/{$argv[2]}.php created succesfully."; 
            break;
        case 'create-template':
            if (file_exists(__DIR__ . "/../src/templates/{$argv[2]}.php")) {
                echo "./src/templates/{$argv[2]}.php already exists.";
                break;
            }
            $content = file_get_contents(__DIR__ . '/assets/template.txt'); 
            $file_content = str_replace('%template_name%', $argv[2], $content); 
            file_put_contents(__DIR__ . "/../src/templates/{$argv[2]}.php", $file_content);
            echo "./src/templates/{$argv[2]}.php created succesfully."; 
            break;
        default:
        echo "Error. Unknown command.";
    }    
}
