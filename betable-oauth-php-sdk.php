<?php

#  Copyright (c) 2012, Betable Limited
#  All rights reserved.
#  
#  Redistribution and use in source and binary forms, with or without
#  modification, are permitted provided that the following conditions are met:
#     *  Redistributions of source code must retain the above copyright
#        notice, this list of conditions and the following disclaimer.
#     *  Redistributions in binary form must reproduce the above copyright
#        notice, this list of conditions and the following disclaimer in the
#        documentation and/or other materials provided with the distribution.
#     *  Neither the name of Betable Limited nor the names of its contributors
#        may be used to endorse or promote products derived from this software
#        without specific prior written permission.
#  
#  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
#  AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
#  IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
#  ARE DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
#  DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
#  (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
#  LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
#  ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
#  (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
#  THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

class Betable {

    var $authorize_endpoint = "https://betable.com/authorize";
    var $endpoint = "https://api.betable.com/1.0";

    function Betable($client_id, $client_secret, $redirect_uri) {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->redirect_uri = $redirect_uri;
    }

    function authorize() {
        $location = sprintf(
            "%s?client_id=%s&redirect_uri=%s&response_type=code",
            $this->authorize_endpoint,
            $this->client_id,
            $this->redirect_uri
        );
        error_log("[Betable authorize] redirecting to $location");
        header("Location: $location\r\n");
        exit;
    }

    function token() {
        if (!isset($_GET["code"])) {
            error_log("[Betable token] code not found");
            return false;
        }
        $response = $this->curl_quickie("POST", "/token", true, array(
            "code" => $_GET["code"],
            "grant_type" => "authorization_code",
            "redirect_uri" => $this->redirect_uri,
        ));
        if (false === $response) {
            return false;
        }
        $this->access_token = $response["access_token"];
        error_log("[Betable token] access_token: " . $this->access_token);
        return $this->access_token;
    }

    function account() {
        if (!isset($this->access_token)) {
            error_log("[Betable account] access_token not found");
            return;
        }
        return $this->curl_quickie("GET", "/account", false, array(
            "access_token" => $this->access_token,
        ));
    }

    function wallet() {
        if (!isset($this->access_token)) {
            error_log("[Betable wallet] access_token not found");
            return;
        }
        return $this->curl_quickie("GET", "/account/wallet", false, array(
            "access_token" => $this->access_token,
        ));
    }

    function curl_quickie(
        $method,
        $path,
        $http_basic_auth = true,
        $fields = array()
    ) {
        $url = $this->endpoint . $path;
        if ("POST" !== $method) {
            $url .= "?" . http_build_query($fields);
        }
        $ch = curl_init($url);
        if ($http_basic_auth) {
            curl_setopt(
                $ch,
                CURLOPT_USERPWD,
                $this->client_id . ":" . $this->client_secret
            );
        }
        if ("POST" === $method) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $array = json_decode($response, true);
        if (!is_array($array)) {
            error_log("[Betable] $method $path responded $response");
            return false;
        }
        if (200 !== $status) {
            error_log("[Betable] $method $path responded $status");
        }
        return $array;
    }

}
