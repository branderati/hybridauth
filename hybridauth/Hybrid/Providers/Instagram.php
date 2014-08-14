<?php
/*!
* HybridAuth
* http://hybridauth.sourceforge.net | https://github.com/hybridauth/hybridauth
*  (c) 2009-2012 HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html
*/

/**
 * Hybrid_Providers_Instagram (By Sebastian Lasse - https://github.com/sebilasse)
 */
class Hybrid_Providers_Instagram extends Hybrid_Provider_Model_OAuth2
{
    // default permissions
    public $scope = "basic";

    /**
     * IDp wrappers initializer
     */
    function initialize() {
        parent::initialize();

        // Provider api end-points
        $this->api->api_base_url = "https://api.instagram.com/v1/";
        $this->api->authorize_url = "https://api.instagram.com/oauth/authorize/";
        $this->api->token_url = "https://api.instagram.com/oauth/access_token";
    }

    /**
     * load the user profile from the IDp api client
     */
    function getUserProfile() {
        $response = $this->api->api("users/self/");

        if ($response->meta->code != 200) {
            throw new Exception("User profile request failed! {$this->providerId} returned an invalid response.", 6);
        }
        $counts = (array)$response->data->counts;

        $this->user->profile->reach = $counts["followed_by"];
        $this->user->profile->profileURL = "https://instagram.com/" . $response->data->username;

        $this->user->profile->identifier = $response->data->id;
        $this->user->profile->displayName = $response->data->full_name ? $response->data->full_name : $response->data->username;
        $this->user->profile->description = $response->data->bio;
        $this->user->profile->photoURL = $response->data->profile_picture;

        $this->user->profile->webSiteURL = $response->data->website;

        $this->user->profile->username = $response->data->username;

        return $this->user->profile;
    }

    function getUserActivity($stream) {

        if ($stream !== "me") {
            return false;
        }

        $response = $this->api->api("users/self/feed");

        if ($response->meta->code != 200) {
            throw new Exception("User profile request failed! {$this->providerId} returned an invalid response.", 6);
        }

        if (!$response) {
            return false;
        }

        return $response->data;
    }

    function search($tag) {
        $countInfo = $this->api->api("/tags/".$tag);
        $count = $countInfo->data->media_count;
        print $tag.": ".$count.PHP_EOL;
        $response = $this->api->api("/tags/".$tag."/media/recent");

        if ($response->meta->code != 200) {
            throw new Exception("User profile request failed! {$this->providerId} returned an invalid response.", 6);
        }

        if (!$response) {
            return false;
        }

        return $response->data;
    }
}
