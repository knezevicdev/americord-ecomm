<?php

namespace App;

use GuzzleHttp\Client;
use Exception;

class SalesForceAPI {

    private const OPTIONS = [
        'api_url',
        'client_id',
        'client_secret',
        'username',
        'password'
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
            'timeout'  => 5.0,
        ]);

        $response = $this->get_oauth_token();

        var_dump($this->access_token);
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


}