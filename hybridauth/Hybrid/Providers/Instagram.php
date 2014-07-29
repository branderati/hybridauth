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
	function initialize()
	{
		parent::initialize();

		// Provider api end-points
		$this->api->api_base_url  = "https://api.instagram.com/v1/";
		$this->api->authorize_url = "https://api.instagram.com/oauth/authorize/";
		$this->api->token_url     = "https://api.instagram.com/oauth/access_token";
	}

	/**
	* load the user profile from the IDp api client
	*/
	function getUserProfile(){ 
		$data = $this->api->api("users/self/" ); 

		if ( $data->meta->code != 200 ){
			throw new Exception( "User profile request failed! {$this->providerId} returned an invalid response.", 6 );
		}

		$this->user->profile->identifier  = $data->data->id;

    $counts = (array) $data->data->counts;

    $this->user->profile->reach = $counts["followed_by"];

		$this->user->profile->displayName = $data->data->full_name ? $data->data->full_name : $data->data->username;
    $nameBreak = explode(" ",$this->user->profile->displayName);
    $this->user->profile->firstName = $nameBreak[0];
    if (count($nameBreak) > 1)
      $this->user->profile->lastName = $nameBreak[1];
    $this->user->profile->description = $data->data->bio;
		$this->user->profile->photoURL    = $data->data->profile_picture;

		$this->user->profile->webSiteURL  = $data->data->website; 

		return $this->user->profile;
	}
}
