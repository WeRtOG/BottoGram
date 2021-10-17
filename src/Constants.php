<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram;

define('BOTTOGRAM_REPO_ROOT', dirname(__DIR__));
define('BOTTOGRAM_ROOT', __DIR__);
    
define('BOT_COMMAND_START', '/start');
define('BOT_COMMAND_RESET', '/reset');
define('BOT_COMMAND_INIT', '/init');
define('BOT_COMMAND_GETID', '/getid');
define('BOT_COMMAND_GODREBUILDMAP', '/god rebuild-menu-map');
define('BOT_COMMAND_CALLBACK_NODELETE', '/nodelete');

define('BOTTOGRAM_DB_TABLE_BOTUSERS', 'bottogram_users');
define('BOTTOGRAM_DB_TABLE_BOTLOG', 'bottogram_log');
define('BOTTOGRAM_DB_TABLE_ADMIN_USERS', 'bottogram_admin_users');

define('BOTTOGRAM_RELATIVE_ROOT', str_replace(dirname($_SERVER['SCRIPT_NAME']), '', BOTTOGRAM_ROOT));