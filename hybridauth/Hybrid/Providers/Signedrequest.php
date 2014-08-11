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
        $this->user->profile->identifier = $this->params['eid'];
        $signaturePost = $this->params['signature'];

        $signature = '';
        if ($this->user->profile->identifier != '') {
            $signature = base64_encode(hash_hmac('sha1', $this->user->profile->identifier, $sharedKey));
        }

        $passed = false;
        if ($signature != '' || $signaturePost != '') {
            if ($signature === $signaturePost) {
                $passed = true;
            }
        }

        if ($passed) {
            $this->setUserConnected();
        }
        else {
            throw new Exception("Authentication failed!", 5);
        }

    }

    function loginBegin() {

    }

    function loginFinish() {

    }

    function getUserProfile() {
        $this->user->profile->identifier = $this->params['eid'];
        return $this->user->profile;
    }

    function getAccessToken() {
        return false;
    }
} 