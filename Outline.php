<?php

class Outline {
    private $url;
    
    public function __construct($url) {
        $this->url = $url;
    }
    
    public function postNewOutlineAccessKey() {
        $values = array("name" => "newOutlineACCESSKEY", "data-limit" => "1000000000");
        $j = json_encode($values);
        
        $options = array(
            'http' => array(
                'header'  => "Content-Type: application/json\r\n",
                'method'  => 'POST',
                'content' => $j,
            ),
        );
        $context = stream_context_create($options);
        $response = file_get_contents($this->getUrl("Create"), false, $context);
        
        $result = json_decode($response);
        echo json_encode($result);
        return $result;
    }
    
    public function changeAccessKeyName($id, $name, $reseller_id) {
        $values = array("name" => $name . "__" . strval($reseller_id));
        $j = json_encode($values);
        
        $options = array(
            'http' => array(
                'header'  => "Content-Type: application/json\r\n",
                'method'  => 'PUT',
                'content' => $j,
            ),
        );
        $context = stream_context_create($options);
        $url = $this->getUrl("Edit", $id);
        $response = file_get_contents($url, false, $context);
        
        echo http_response_code() . " " . $response . "id: " . $id . ", name: " . $name;
        return $response;
    }
    
    public function changeAccessKeyLimit($id, $limit) {
        $values = array("limit" => array("bytes" => $limit));
        $j = json_encode($values);
        
        $options = array(
            'http' => array(
                'header'  => "Content-Type: application/json\r\n",
                'method'  => 'PUT',
                'content' => $j,
            ),
        );
        $context = stream_context_create($options);
        $url = $this->getUrl("EditLimit", $id);
        $response = file_get_contents($url, false, $context);
        
        echo http_response_code() . " " . $response;
        return $response;
    }
    
    public function changeAccessKeyLimitWithPassword($id, $password, $limit) {
        $result = $this->getAccessKeyInfo($id, $password);
        if (!$result) {
            return false;
        }
        
        return $this->changeAccessKeyLimit($result->ID, $limit);
    }
    
    public function getAccessKeyInfo($id, $password) {
        $list = $this->getAccessKeyList();
        if (!$list) {
            return false;
        }
        
        foreach ($list->accessKeys as $key) {
            if ($key->ID == $id && $key->Password == $password) {
                $usage = $this->getAccessKeyUsageById($id);
                if ($usage !== false) {
                    $key->Usage->Bytes = $usage;
                }
                $key->Name = explode("__", $key->Name)[0];
                return $key;
            }
        }
        
        return false;
    }
    
    public function getAccessKeyList() {
        $options = array(
            'http' => array(
                'header'  => "Content-Type: application/json\r\n",
                'method'  => 'GET',
            ),
        );
        $context = stream_context_create($options);
        $response = file_get_contents($this->getUrl("List"), false, $context);
        
        $result = json_decode($response);
        return $result;
    }
    
    private function getAllUsages() {
        $options = array(
            'http' => array(
                'header'  => "Content-Type: application/json\r\n",
                'method'  => 'GET',
            ),
        );
        $context = stream_context_create($options);
        $response = file_get_contents($this->getUrl("Usage"), false, $context);
        
        $result = json_decode($response, true);
        return $result;
    }
    
    public function getAccessKeyUsageById($id) {
        $usages = $this->getAllUsages();
        if (!$usages || !isset($usages["bytesTransferredByUserId"][$id])) {
            return false;
        }
        
        return $usages["bytesTransferredByUserId"][$id];
    }
    
    private function getUrl($target, $id = null) {
        $baseUrl = $this->url;
        $url = "";
        switch ($target) {
            case "Create":
            case "List":
            case "Usage":
                $url = $baseUrl . Outline::$urls[$target];
                break;
            case "Edit":
            case "EditLimit":
            case "KeyInfo":
                if ($id !== null) {
                    $url = $baseUrl . sprintf(Outline::$urls[$target], $id);
                }
                break;
        }
        return $url;
    }
    
    public static $urls = array(
        "Create" => "/access-keys",
        "List" => "/access-keys",
        "Edit" => "/access-keys/%s/name",
        "EditLimit" => "/access-keys/%s/data-limit",
        "KeyInfo" => "/access-keys/%s/",
        "Usage" => "/metrics/transfer",
    );
}

class CreateResponse {
    public $ID;
    public $Name;
    public $Password;
    public $Port;
    public $Method;
    public $AccessUrl;
    public $Usage;
    public $DataLimit;
}

class AccessKeysResponse {
    public $accessKeys;
}

class UsageResponse {
    public $ID;
    public $Usage;
}

?>
