<?php

/**
 *
 * Created by Jimmy Hickey
 * Date: 8/11/14
 * Time: 10:23 AM
 * Branderati 2014
 */
class Hybrid_Providers_Signedrequest extends Hybrid_Provider_Model
{
    function initialize() {

    }

    function login() {
        $sharedKey = $this->config['keys']['secret'];
        $signaturePost = $this->params['signature'];
        list($encoded_sig, $payload) = explode('.', $signaturePost, 2);
        $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
        if (!isset($data['id'])){
            header("Location: /no_auth");
            exit();
        }

        $this->user->profile->identifier = $data['id'];
        unset($data['id']);
        foreach ($data as $key => $val){
            $this->user->profile->{$key} = $val;
        }
        $signature = '';
        if ($this->user->profile->identifier != '') {
            $signature = base64_encode(hash_hmac('sha1', $this->user->profile->identifier, $sharedKey));
        }

        if ($signature != '' || $encoded_sig != '') {
            if ($signature === $encoded_sig) {
                $this->setUserConnected();
            }
            else{
                header("Location: /no_auth");
                exit();
            }
        }
        else{
            header("Location: /no_auth");
            exit();
        }


    }

    function loginBegin() {

    }

    function loginFinish() {

    }

    function getUserProfile() {
        //$this->user->profile->identifier = $this->params['eid'];
        return $this->user->profile;
    }

    function getAccessToken() {
        return false;
    }
} 