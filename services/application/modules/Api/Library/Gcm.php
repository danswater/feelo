<?php

class Api_Library_Gcm {


	private $gcmUrl = "https://android.googleapis.com/gcm/send";
	private $googleApiKey = "AIzaSyBKBwOm2tJGYPX5zO3m-NmHF7haxQdUFcE";
	private $registration_ids = array();
    private $dataMessage = array();

	public function setGoogleApiKey( $googleApiKey ) {
		$this->googleApiKey = $googleApiKey;
	}

	public function getGoogleApiKey() {
		return $this->googleApiKey;
	}

	public function setRegistrationId( $registration_ids ) {

		if( !is_array($registration_ids) )
			$registration_ids = array( $registration_ids );

		$this->registration_ids = $registration_ids;
	}

	public function getRegistrationId() {
		return $this->registration_ids;
	}

    public function setMessageData( $message, $options = array() ) {

        $data = array( "message" => $message );

        $data = array_merge( $data, $options );

        $this->dataMessage = array(
            'registration_ids' => $this->getRegistrationId(),
            'data' => $data,
        );

    }

	public function send() {
       

        $headers = array(
            'Authorization: key=' . $this->getGoogleApiKey(),
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $this->gcmUrl);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $this->dataMessage ) );

        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }

        // Close connection
        curl_close($ch);

        $resultObject = json_decode( $result );

        if( $resultObject->success == 1) {
            return true;
        }
        else{
            return false;
        } 

    }
	

}