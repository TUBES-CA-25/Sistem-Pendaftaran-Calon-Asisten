<?php

define('APP_NAME', getenv('APP_NAME') ?: 'IC-ASSIST');
define('BASE_URL', getenv('BASE_URL') ?: (getenv('APP_URL') ?: 'http://localhost/tubes_web/public'));
define('BASEURL', BASE_URL);
define('APP_URL', BASE_URL);
define('APP_DEBUG', filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN));
define('APP_BASE_PATH', getenv('APP_BASE_PATH') ?: '');
define('RES_PATH', getenv('RES_PATH') ?: '');