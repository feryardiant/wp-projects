<?php

$root_path = dirname(__DIR__);

require_once $root_path . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable($root_path);

$dotenv->safeLoad();

putenv("WORDPRESS_DB_NAME={$_ENV['DB_NAME']}");
putenv("WORDPRESS_DB_USER={$_ENV['DB_USER']}");
putenv("WORDPRESS_DB_PASSWORD={$_ENV['DB_PASS']}");
putenv("WORDPRESS_DB_HOST=127.0.0.1:{$_ENV['FORWARD_DB_PORT']}");
