<?php

require 'vendor/autoload.php';
require 'SalesForceAPI.php';

use App\SalesForceAPI;
use Dotenv\Dotenv;

$dotenv = new Dotenv(__DIR__);
$dotenv->load();
$dotenv->required(['SF_API_URL', 'SF_CLIENT_ID', 'SF_CLIENT_SECRET', 'SF_USERNAME', 'SF_PASSWORD'])->notEmpty();

$sfApi = new SalesForceAPI([
    'api_url'       => getenv('SF_API_URL'),
    'client_id'     => getenv('SF_CLIENT_ID'),
    'client_secret' => getenv('SF_CLIENT_SECRET'),
    'username'      => getenv('SF_USERNAME'),
    'password'      => getenv('SF_PASSWORD')
]);