<?php
define('DB_CONNECTION', getenv('DB_CONNECTION') ?: 'mysql');
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USERNAME') ?: 'root');
define('DB_PASS', getenv('DB_PASSWORD') ?: '');
define('PORT', getenv('DB_PORT') ?: 3306);
define('DB_NAME', getenv('DB_DATABASE') ?: 'DB_TUBES');

