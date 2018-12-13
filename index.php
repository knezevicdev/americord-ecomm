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

// $sfApi->create_opportinity([
//     'Baby_s_Due_Date__c' => '2019-28-04',
//     'FirstName__c' => 'Nikola',
//     'LastName__c' => 'Knezevic',
//     'Email__c' => 'knezevicdev@gmail.com',
//     'Phone__c' => '(888) 987-2345',
//     'Relation_to_Baby__c' => 'Mother',
//     'Number_of_Births__c' => 'Single',
//     'Surrogate__c' => 'false',
//     'Payment_Plans__c' => 'Annual Storage',
//     'Storage_Plan__c' => 'Annual Storage Plan',
//     'Name' => 'test16 test16 - 11/30/2018',
//     'StageName' => 'Started',
//     'CloseDate' => '2019-28-04',
//     'Cord_Blood_2_0__c' => 1
// ]);

// $sfApi->create_record([
//     'Babys_Due_Date__c' => '2019-28-04',
//     'FirstName' => 'Nikola',
//     'LastName' => 'Knezevic',
//     'Email' => 'knezevicdev@gmail.com',
//     'Phone' => '(888) 987-2345',
//     'Relation_to_Baby__c' => 'Mother',
//     'Number_of_Babies__c' => 'Single',
//     'Surrogate__c' => 'false',
//     'Payment_Plans__c' => 'Annual Storage',
//     'Storage_Plan__c' => 'Annual Storage Plan',
//     'Cord_Blood_2_0__c' => 1
// ], 'Lead');

// $sfApi->update_record('0065C000003yx7u', [
//     'Cord_Tissue__c' => NULL
// ], 'Opportunity');

// $opportunity = $sfApi->find_opportunity("aaa", "test");

//fw1__Opportunity__c
//Lead__c

$sfApi->pay("4111111111111111", "321", "12", "2019", "Nikola", "Knezevic", "22222", "fw1__Opportunity__c", "0065C000003yx7u");