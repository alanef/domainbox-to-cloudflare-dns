<?php

namespace Command;

use SoapClient;

class DomainBox {
	private $reseller;
	private $user;
	private $pass;
	private $client;

	function __construct( $reseller, $username, $password, $sandbox = true ) {
		$this->reseller = $reseller;
		$this->user     = $username;
		$this->pass     = $password;

		$uri = $sandbox ? "https://sandbox.domainbox.net/?WSDL" : "https://live.domainbox.net/?WSDL";

		$this->client = new SoapClient( $uri, array( 'soap_version' => SOAP_1_2 ) );
	}


	public function doCall( $func, $params ) {
		$auth = array(
			'AuthenticationParameters' => array(
				'Reseller' => $this->reseller,
				'Username' => $this->user,
				'Password' => $this->pass
			)
		);

		$command = array( 'CommandParameters' => $params );

		$request = array_merge( $auth, $command );

		$results = $this->client->$func( $request );

		return $results;
	}

}
