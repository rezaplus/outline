<?php

require_once 'Outline.php'; // Include the Outline class file

class Main {
    private $outline;

    public function __construct($url) {
        $this->outline = new Outline($url);
    }

    public function createAccessKey() {
        try {
            $response = $this->outline->postNewOutlineAccessKey();
            if ($response !== false) {
                echo "Access Key created successfully:\n";
                echo "ID: " . $response->ID . "\n";
                echo "Name: " . $response->Name . "\n";
                echo "Password: " . $response->Password . "\n";
                echo "Port: " . $response->Port . "\n";
                echo "Method: " . $response->Method . "\n";
                echo "Access URL: " . $response->AccessUrl . "\n";
            } else {
                echo "Failed to create Access Key.\n";
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }

    public function editAccessKeyName($id, $name, $reseller_id) {
        try {
            $response = $this->outline->changeAccessKeyName($id, $name, $reseller_id);
            if ($response !== false) {
                echo "Access Key name updated successfully.\n";
            } else {
                echo "Failed to update Access Key name.\n";
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }

    public function viewAccessKeyInfo($id, $password) {
        try {
            $accessKey = $this->outline->getAccessKeyInfo($id, $password);
            if ($accessKey !== false) {
                echo "Access Key Information:\n";
                echo "ID: " . $accessKey->ID . "\n";
                echo "Name: " . $accessKey->Name . "\n";
                echo "Password: " . $accessKey->Password . "\n";
                echo "Port: " . $accessKey->Port . "\n";
                echo "Method: " . $accessKey->Method . "\n";
                echo "Access URL: " . $accessKey->AccessUrl . "\n";
                echo "Usage (Bytes): " . $accessKey->Usage->Bytes . "\n";
                echo "Data Limit (Bytes): " . $accessKey->DataLimit->Bytes . "\n";
            } else {
                echo "Access Key not found.\n";
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
}

// Usage example:
$outlineUrl = 'https://3.78.54.190:8862/ymGeDIwaORifIqZIg-BXow'; // Replace with your Outline server URL
$main = new Main($outlineUrl);

// Create an access key
// $main->createAccessKey();

// Edit the access key name
// $main->editAccessKeyName('access_key_id', 'new_name', 123);

// View access key information
// $main->viewAccessKeyInfo('3', 'FJqtuunH8GihgOCz6TD1Rj');

var_dump($main->getAccessKeyList());
