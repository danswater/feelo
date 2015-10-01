<?php

class Api_Library_PushNotifications {

	private $sslUrl = "ssl://gateway.sandbox.push.apple.com:2195";
	private $deviceToken = "";
	private $passphrase = "15under15";
	private $dataMessage = array();

	public function setDeviceToken( $deviceToken ) {
		$this->deviceToken = $deviceToken;
	}

	public function getDeviceToken() {
		return $this->deviceToken;
	}

	public function getCertificate() {
		$pemfile = dirname( __FILE__ ) . "/pem/yamba_aps_development.pem"  ;
		if( !file_exists( $pemfile ) ) {
			$pemfile = false;
		}
		return $pemfile;
	}


	public function setMessageData( $message, $options = array() ){

		$data = array(
			"aps" => array(
				"content-available" => 1,
				'alert' => $message,
				'sound' => 'default'
			)
		);

		$data = array_merge( $data, $options );

		$this->dataMessage = $data;

	}

	
	public function send() {
		$return = false;
		$certificate = $this->getCertificate();
		$deviceToken = $this->getDeviceToken();

		if( $certificate === false) return false;

		$ctx = stream_context_create();
		stream_context_set_option($ctx, 'ssl', 'local_cert', $certificate);
		stream_context_set_option($ctx, 'ssl', 'passphrase', $this->passphrase);

		$fp = stream_socket_client(
			$this->sslUrl, $err,
			$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

		if (!$fp)
			exit("Failed to connect: $err $errstr" . PHP_EOL);

		$payload = json_encode( $this->dataMessage );

		$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

		$result = fwrite($fp, $msg, strlen($msg));

		if ($result)
			$return = true;

		fclose($fp);

		return $return;

	}

}