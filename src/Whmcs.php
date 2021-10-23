<?php

namespace FintechSystems\Whmcs;

use FintechSystems\Whmcs\Contracts\BillingProvider;

class Whmcs implements BillingProvider
{    
    private $url;
    private $api_identifier;
    private $api_secret;

    public function __construct($client)
    {        
        $this->url            = $client['url'];
        $this->api_identifier = $client['api_identifier'];
        $this->api_secret     = $client['api_secret'];
    }

    /**
     * https://developers.whmcs.com/api-reference/addclient/
     */
    public function addUser($data)
    {
        $action = "AddClient";
        return $this->call($action, $data);
    }

    public function addOrder($data) {
        $action = "AddOrder";
        $this->call($action, $data);
    }

    public function changePlan(Array $serviceId) {
        $action = "ModuleChangePackage";
        $this->call($action, $serviceId);
    }

    public function getClientsDetails($client)
    {
        $action = "GetClientsDetails";

        $data = [
            'limitnum' => 1000,            
        ];
        
        if (isset($client['email'])) {
            $data = array_merge($data, ['email' => $client['email']]);
        }

        if (isset($client['clientid'])) {
            $data = array_merge($data, ['clientid' => $client['clientid']]);
        }

        return $this->call($action, $data);
    }

    /**
     * https://whmcs.community/topic/309195-confusion-between-getclients-and-getclientsdetails-api-calls-and-how-to-retrieve-phone-numbers-in-one-shot/?tab=comments#comment-1360588
     */
    public function getClientByPhoneNumber(Array $data) {
        $action = "GetClientByPhoneNumber";

        return $this->call($action, $data);
    }

    public function getClientsDomains()
    {
        $action = "GetClientsDomains";
        $data = ['limitnum' => 2500];
        return $this->call($action, $data);
    }

    public function getProducts()
    {
        $action = "GetProducts";
        return $this->call($action);
    }

    /**
     * Get a list of servers
     *
     * https://developers.whmcs.com/api-reference/getservers/
     */
    public function getServers()
    {
        return $this->call("GetServers");
    }

    public function getServices()
    {
        $action = "GetClientsProducts";
        return $this->call($action);
    }

    /**
     *  
     */
    public function getClients()
    {
        $action = "GetClients";
        $data = ['limitnum' => 1000];
        return $this->call($action, $data);
    }

    public function updateService($params) {
        $action = "UpdateClientProduct";
        return $this->call($action, $params);
    }

    private function call($action, $data = null) {
        $postfields = array(
            'identifier'   => $this->api_identifier,
            'secret'       => $this->api_secret,
            'action'       => $action,
            'responsetype' => 'json',
        );
        if ($data) {
            $postfields = array_merge($data, $postfields);
        }
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->url . 'includes/api.php');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Avoid Unable to connect: 60 - SSL certificate problem: self signed certificate
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));

        $response = curl_exec($ch);

        if (curl_error($ch)) {
            $message= "WHMCS API CURL error for action $action: Unable to connect: " . curl_errno($ch) . ' - ' . curl_error($ch);

            ray($message);
            
            curl_close($ch);

            return null;
        }
        curl_close($ch);

        ray($response);

        ray (json_decode($response, true));
        
        return json_decode($response, true);

        $jsonData = json_decode($response, true);
                
        if ($jsonData['result'] == "error") {
            $message = "WHMCS API error for action $action: {$jsonData['message']}";

            ray($message);
            
            return null;
        }
        
        return $jsonData;
    }
}
