<?php
require dirname(__DIR__).'/bootstrap.php';
\AranduGo\Auth::logout();\AranduGo\Support::redirect('dashboardcliente/login.php');
