<?php
/**
 * @copyright (c) 2020.
 * @author            Alan Fuller (support@fullworks)
 * @licence           GPL V3 https://www.gnu.org/licenses/gpl-3.0.en.html
 * @link                  https://fullworks.net
 *
 * This is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This software is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 */

namespace Command;

use SoapClient;

/**
 * Class DomainBox
 * @package Command
 */
class DomainBox {
	/**
	 * @var
	 */
	private $reseller;
	/**
	 * @var
	 */
	private $user;
	/**
	 * @var
	 */
	private $pass;
	/**
	 * @var SoapClient
	 */
	private $client;

	/**
	 * DomainBox constructor.
	 *
	 * @param $reseller
	 * @param $username
	 * @param $password
	 * @param bool $sandbox
	 *
	 * @throws \SoapFault
	 */
	function __construct( $reseller, $username, $password, $sandbox = true ) {
		$this->reseller = $reseller;
		$this->user     = $username;
		$this->pass     = $password;
		$uri            = $sandbox ? "https://sandbox.domainbox.net/?WSDL" : "https://live.domainbox.net/?WSDL";
		$this->client   = new SoapClient( $uri, array( 'soap_version' => SOAP_1_2 ) );
	}


	/**
	 * @param $func
	 * @param $params
	 *
	 * @return mixed
	 */
	public function doCall( $func, $params ) {
		$auth    = array(
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
