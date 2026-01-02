<?php

define('APP_NAME', getenv('APP_NAME') ?: 'IC-ASSIST');
define('APP_URL', getenv('APP_URL') ?: 'http://localhost');
define('APP_DEBUG', filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN));
define('APP_BASE_PATH', getenv('APP_BASE_PATH') ?: '');
define('BASE_URL', getenv('BASE_URL') ?: '');
define('RES_PATH', getenv('RES_PATH') ?: '');