<?php

/////////////////////////////////////////////
//===========================================
//         Api Shield RoyaleHosting        //
//===========================================
// Version: 1.5
// Created by iSt-Panel (Petcu Mihai)
// Email: developer@ist-panel.ro
//===========================================
// (c) iSt-Panel
//===========================================

class RoyalApi {

    var $key = '';
    var $timeout = 30;
    var $apiUrl = 'https://shield.royalehosting.net/api';
    var $token = '';

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function jsonEncode(array $data)
    {
        return json_encode($data);
    }

    public function jsonDecode(string $data)
    {
        return json_decode($data);
    }

    private function callShield(string $version, string $route, bool $post = false)
    {

        $url = $this->apiUrl . "/". $version. "/". $route;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        if ($post) {
            $postFields = [];
            $postFields['key'] = $this->key;

            $post = $this->jsonEncode($postFields);

            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        } else {
            $header = [
                'Content-Type: application/json',
                'token:' . $this->key
            ];

            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        }

        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        curl_close($ch);

        $response = $this->jsonDecode($data);

        return $response;
    }

    /*
     * @deprecated
     */
    public function loginShield(string $version)
    {
        $data = $this->callShield($version, 'auth/api', true);
        $this->token = $data->data->token;
        return $this;
    }

    public function listAttack(string $version)
    {
        $response = $this->callShield($version, 'attacks?chunk=0');
        return $response->data->attacks;
    }

    public function getAttack(string $version, int $attackId)
    {
        $response = $this->callShield($version, "attacks/". $attackId);
        return $response;
    }

    public function listIps(string $version)
    {
        $response = $this->callShield($version, "ips");
        return $response;
    }
}
