<?php

namespace App;

use GuzzleHttp\Client;
use Exception;
use GuzzleHttp\Exception\ClientException;

class SalesForceAPI {

    private const OPTIONS = [
        'api_url',
        'client_id',
        'client_secret',
        'username',
        'password'
    ];

    private const OPPORTUNITY_FIELDS = [
        'Ecomm_Status__c',
        'Baby_s_Due_Date__c',
        'FirstName__c',
        'LastName__c',
        'Email__c',
        'Phone__c',
        'Relation_to_Baby__c',
        'Contact2_FirstName__c',
        'Contact2_LastName__c',
        'Contact2_Email__c',
        'Contact2_Phone__c',
        'Contact2_Relation_to_Baby__c',
        'Surrogate__c',
        'Coupon_Code__c',
        'Sales_Discount__c',
        'Referred_by_email_address__c',
        'Number_of_Births__c',
        'Product2',
        'Payment_Plans__c',
        'Storage_Plan__c'
    ];

    private $api_url = null;
    private $access_token = null;
    private $client_id = null;
    private $client_secret = null;
    private $username = null;
    private $password = null;
    private $client = null;

    function array_keys_exists(array $keys, array $arr) {
        return !array_diff_key(array_flip($keys), $arr);
    }

    function __construct($options)
    {
        if(!$this->array_keys_exists(self::OPTIONS, $options)) {
            throw new Exception('Options not valid!');
        }

        $this->api_url = $options['api_url'];
        $this->client_id = $options['client_id'];
        $this->client_secret = $options['client_secret'];
        $this->username = $options['username'];
        $this->password = $options['password'];

        $this->client = new Client([
            'base_uri' => $this->api_url,
            'timeout'  => 30.0,
        ]);

        $response = $this->get_oauth_token();
    }

    private function get_oauth_token()
    {
        $response = $this->client->request('POST', '/services/oauth2/token', [
            'form_params' => [
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'username' => $this->username,
                'password' => $this->password,
                'grant_type' => 'password'
            ]
        ]);
        
        if($response->getStatusCode() === 200) {
            $response = json_decode($response->getBody()->getContents());
        } else {
            throw new Exception('Invalid credentials!');
        }

        $this->access_token = $response->access_token;
    }

    public function create_record($data, $name)
    {
        try {
            $response = $this->client->request('POST', '/services/data/v44.0/sobjects/'.$name.'/', [
                'json' => $data,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->access_token
                ]
            ]);

            $response = json_decode($response->getBody()->getContents());

            return $response;
        } catch(ClientException $exception) {
            var_dump($exception->getMessage());
            return false;
        }
    }

    public function update_record($id, $data, $name)
    {
        try {
            $response = $this->client->request('PATCH', '/services/data/v44.0/sobjects/'.$name.'/'.$id, [
                'json' => $data,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->access_token
                ]
            ]);
        } catch(ClientException $exception) {
            var_dump($exception->getMessage());
        }
    }

    public function find_opportunity($email, $phone, $email2 = false, $phone2 = false)
    {
        $query = "SELECT id FROM Opportunity WHERE Email__c = '{$email}' OR Contact2_Email__c = '{$email}' OR Phone__c = '{$phone}' OR Contact2_Phone__c = '{$phone}'";

        if($email2 && $email2 !== NULL) $query .= " OR Email__c = '{$email2}' OR Contact2_Email__c = '{$email2}'";
        if($phone2 && $phone2 !== NULL) $query .= " OR Phone__c = '{$phone2}' OR Contact2_Phone__c = '{$phone2}'";
        
        try {
            $response = $this->client->request('GET', '/services/data/v44.0/query/', [
                'query' => [
                    'q' => $query
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->access_token
                ]
            ]);
            
            if($response->getStatusCode() === 200) {
                $response = json_decode($response->getBody()->getContents());

                if($response->done && count($response->records) > 0) {
                    return $response->records[0];
                }
            }
        } catch(ClientException $exception) {
            var_dump($exception->getMessage());
        }
        return false;
    }

    public function pay($ccNumber, $cvv, $expMonth, $expYear, $firstName, $lastName, $zip, $object, $id)
    {
        try {
            $response = $this->client->request('POST', '/services/apexrest/fw1/v2/payments', [
                'json' => [
                    "amount"                => 199.00, 
                    "credit_card_number"    => $ccNumber, 
                    "cvv2"                  => $cvv,
                    "expiry_month"          => $expMonth, 
                    "expiry_year"           => $expYear, 
                    "first_name"            => $firstName, 
                    "last_name"             => $lastName, 
                    "billing_zip"           => $zip,
                    "Related_object_field"  => $object,
                    "Related_record_id"     => $id,
                    "Reference"             => "ecomm"
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->access_token
                ]
            ]);

            $response = json_decode($response->getBody()->getContents());
            
            return $response;
        } catch(ClientException $exception) {
            var_dump($exception->getMessage());
        }
        return false;
    }
}