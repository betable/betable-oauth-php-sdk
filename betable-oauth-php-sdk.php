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
        # TODO Do any of these need to be URL encoded by hand?
        header("Location: ${this->authorize_endpoint}?client_id=${this->client_id}&redirect_uri=${this->redirect_uri}&response_type=code\r\n");
        exit;
    }

    function token() {
        if (!isset($_GET["code"])) {
            # TODO error_log
            return false;
        }
        $response = curl_quickie("POST", "/token", array(
            "code" => $_GET["code"],
            "grant_type" => "authorization_code",
            "redirect_uri" => "${this->redirect_uri}",
        ));
        if (!$response) {
            # TODO error_log
            return false;
        }
        $response = parse_str($response); # TODO Error handling.
        $this->access_token = $response["access_token"];
        return $this->access_token;
    }

    function account() {
        if (!isset($this->access_token)) {
            # TODO error_log
            return;
        }
    }

    function wallet() {
        if (!isset($this->access_token)) {
            # TODO error_log
            return;
        }
    }

    function curl_quickie($method, $path, $fields = array()) {
        $url = "${this->endpoint}${path}";
        if ("POST" !== $method) {
            $url .= "?" . http_build_query($fields);
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        if ("POST" === $method) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if (200 != curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
            # TODO error_log
            return false;
        }
        curl_close($ch);
        return $response;
    }

}
