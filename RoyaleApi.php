<?php

/////////////////////////////////////////////
//===========================================
//         Api Shield RoyaleHosting        //
//===========================================
// Version: 1.6
// Created by iSt-Panel (Petcu Mihai)
// Email: developer@ist-panel.ro
//===========================================
// (c) iSt-Panel
//===========================================

class RoyaleApi {

    var $key = '';
    var $timeout = 30;
    var $apiUrl = 'https://shield.royalehosting.net/api';
    var $token = '';

    var $post = [];

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

    private function callShield(string $version, string $route, bool $post = false, bool $delete = false)
    {

        $url = $this->apiUrl . "/". $version. "/". $route;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        if ($post) {
            $post = $this->jsonEncode($this->post);

            $header = [
                'Content-Type: application/json',
                'token:' . $this->key
            ];

            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        } else {
            $header = [
                'Content-Type: application/json',
                'token:' . $this->key
            ];

            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

            if ($delete) {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            }

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
    public function loginShield(string $version): RoyaleApi
    {
        $data = $this->callShield($version, 'auth/api', true);
        $this->token = $data->data->token;
        return $this;
    }

    public function addPostVars(array $data): RoyaleApi
    {
        $this->post = $data;
        return $this;
    }

    public function listAttack(string $version)
    {
        $response = $this->callShield($version, 'attacks?chunk=0');
        return $response->data->attacks;
    }

    public function getAttack(string $version, string $attackId)
    {
        $response = $this->callShield($version, "attacks/". $attackId);
        return $response;
    }

    public function listIps(string $version)
    {
        $response = $this->callShield($version, "ips");
        return $response;
    }

    public function listRules(string $version)
    {
        $response = $this->callShield($version, "rules?limit=200");
        return $response;
    }

    public function ruteAddRule(string $version)
    {
        $response = $this->callShield($version, "rules", true);
        return $response;
    }

    public function deleteRule(string $version, int $ruleId)
    {
        $response = $this->callShield($version, "/rules/". $ruleId, false, true);
        return $response;
    }

    public function disableRule(string $version, int $ruleId)
    {
        $response = $this->callShield($version, "rules/".$ruleId."/disable");
        return $response;
    }

    public function traffic(string $version, string $unit = 'mbps', int $time = 3600)
    {
        $response = $this->callShield($version, "analytics/traffic?unit=".$unit."&time=". $time);
        return $response;
    }
}