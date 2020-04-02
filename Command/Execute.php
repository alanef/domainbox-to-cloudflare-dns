<?php
/**
 *  @copyright (c) 2020.
 *  @author            Alan Fuller (support@fullworks)
 *  @licence           GPL V3 https://www.gnu.org/licenses/gpl-3.0.en.html
 *  @link                  https://fullworks.net
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

use Garden\Cli\Cli;

/**
 * Class Execute
 * @package Command
 */
class Execute {

	public $domain;
	/**
	 * Execute constructor.
	 * @throws \SoapFault
	 */
	public function __construct() {
		global $argv;
		$cli = new Cli();
		$cli->description( 'Extracts DomainBox DNS into Bind format for Cloudflare.' )
		    ->opt( 'reseller:r', 'Domainbox Reseller.', true )
		    ->opt( 'username:u', 'Username.', true )
		    ->opt( 'password:p', 'Password for Domainbox.', true )
		    ->opt( 'domain:d', 'Domain / Zone to download.', true );
		$args            = $cli->parse( $argv, true );
		$this->domain = $args->getOpt( 'domain' );
		$this->domainbox = new DomainBox(
			$args->getOpt( 'reseller' ),
			$args->getOpt( 'username' ),
			$args->getOpt( 'password' ),
			false
		);
	}

	/**
	 *
	 */
	public function run() {
		$page = 1;
		do {
			$records = $this->domainbox->doCall(
				'QueryDnsRecords',
				array(
					'Zone'       => $this->domain,
					'PageNumber' => $page ++,
				)
			);
			$this->outputBind( $records->QueryDnsRecordsResult->Records->DnsRecordQueryResult );
		} while ( $records->QueryDnsRecordsResult->PageNumber < $records->QueryDnsRecordsResult->TotalPages );
	}

	/**
	 * @param $array
	 */
	private function outputBind( $array ) {
		foreach ( $array as $element ) {
			$function = 'output' . $element->RecordType;
			if ( method_exists( $this, $function ) ) {
				$this->$function( $element );
			}
		}
	}

	/**
	 * @param $record
	 */
	private function outputA( $record ) {
		printf( "$record->HostName\t\tIN\tA\t$record->Content\r\n" );
	}

        /**
         * @param $record
         */
        private function outputAAAA( $record ) {
                printf( "$record->HostName\t\tIN\tAAAA\t$record->Content\r\n" );
        }

        /**
         * @param $record
         */
        private function outputNS( $record ) {
                printf( "$record->HostName\t\tIN\tNS\t$record->Content\r\n" );
        }

        /**
         * @param $record
         */
        private function outputSRV( $record ) {
                printf( "$record->HostName\t\tIN\tSRV\t$record->Priority $record->Weight $record->Port $record->Content\r\n" );
        }

	/**
	 * @param $record
	 */
	private function outputMX( $record ) {
		printf( "$record->HostName\t\tIN\tMX\t$record->Priority $record->Content\r\n" );
	}

	/**
	 * @param $record
	 */
	private function outputCNAME( $record ) {
		printf( "$record->HostName\t\tIN\tCNAME\t$record->Content\r\n" );
	}

	/**
	 * @param $record
	 */
	private function outputTXT( $record ) {
		printf( "$record->HostName\t\tIN\tTXT\t\"$record->Content\"\r\n" );
	}
}
