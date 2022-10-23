<?php

namespace FintechSystems\Whmcs;

use Illuminate\Support\Facades\Http;
use FintechSystems\Whmcs\Contracts\BillingProvider;

class Whmcs implements BillingProvider
{    
    private $url;
    private $api_identifier;
    private $api_secret;

    public function __construct($server)
    {        
        $this->url            = $server['url'];
        $this->api_identifier = $server['api_identifier'];
        $this->api_secret     = $server['api_secret'];
    }

    /**
     * Sometimes you want to connect to another server
     */
    public function setServer($server) {
        $this->url            = $server['url'];
        $this->api_identifier = $server['api_identifier'];
        $this->api_secret     = $server['api_secret'];
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

    /**
     * getClientsDetails
     * 
     * https://developers.whmcs.com/api-reference/getclientsdetails/
     * 
     * The client id to obtain the details for. $clientid or $email is required
     */
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
     * This is a custom API 'getclientbyphonenumber.php'
     * 
     * See: https://whmcs.community/topic/309195-confusion-between-getclients-and-getclientsdetails-api-calls-and-how-to-retrieve-phone-numbers-in-one-shot/?tab=comments#comment-1360588
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

    private function call($action, $data = []) {
        $postfields = array(
            'identifier'   => $this->api_identifier,
            'secret'       => $this->api_secret,
            'action'       => $action,
            'responsetype' => 'json',
        );                    
        $postfields = array_merge($data, $postfields);

        $apiUrl = $this->url . '/includes/api.php';        
                        
        $response = Http::withOptions(["verify"=>false])
            ->asForm()
            ->post($apiUrl, $postfields);
                                
        return $response->json();
    }
    
}
