<?php

abstract class Request{

	public function request( $type, $url, $data = array() ){

		$ch = curl_init();
		$url = $this->server . $url;
		$data_string = json_encode( $data );

		curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_HEADER, false );
		//curl_setopt($c,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.91 Safari/537.36 OPR/27.0.1689.54');


		switch ( $type ) {

			case 'POST':
				curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt( $ch, CURLOPT_POST, true );

				if( isset( $_FILES[ 'Filedata'] ) ){

					$data[ "Filedata" ] = '@' . $_FILES['Filedata']['tmp_name']
			          	. ';filename=' . $_FILES['Filedata']['name']
					    . ';type='     . $_FILES['Filedata']['type'];

				}

				curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $data ) );
				break;

			case 'DELETE':
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
				break;

			case 'PUT':
				curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "PUT");
				curl_setopt( $ch, CURLOPT_POST, true );
				curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $data ) );
				break;

		}



		$resp = curl_exec($ch);
		if(!$resp){
		    die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
		}
		curl_close($ch);


		return $resp;
	}

}

class Proxy extends Request{

	protected $server = "";

	public function __construct( $server ){
		$this->server = $server;
	}


	public function get( $url ){
		return $this->request( "GET", $url, array() );
	}

	public function post( $url, $data ){
		return $this->request( "POST", $url, $data );

	}

	public function delete( $url ){
		return $this->request( "DELETE", $url, array() );

	}

	public function put( $url, $data ){
		return $this->request( "PUT", $url, $data );
	}
}


$baseName = '/services/';
$proxyUrl = 'http://' . $_SERVER[ 'SERVER_ADDR' ] . $baseName;
$proxyServer = new Proxy( $proxyUrl );
$result = array();
switch ( strtolower( $_SERVER[ "REQUEST_METHOD"] ) ) {

	case 'get':
		$result = $proxyServer->get( $_REQUEST[ "url" ] );
		break;

	case 'post':
		$result = $proxyServer->post( $_REQUEST[ "url" ], $_POST );
		break;

	case 'delete':
		$result = $proxyServer->delete( $_REQUEST[ "url" ] );
		break;

	case 'put':
		$result = $proxyServer->put( $_REQUEST[ "url" ], $_POST );
		break;


}

header('Content-Type: application/json');
echo ( $result );

?>