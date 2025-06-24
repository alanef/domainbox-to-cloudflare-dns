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
		    ->opt( 'domain:d', 'Domain / Zone to download.', true )
		    ->opt( 'sandbox:s', 'Use sandbox API instead of live', false, 'boolean' );
		$args            = $cli->parse( $argv, true );
		$this->domain = $args->getOpt( 'domain' );
		$this->domainbox = new DomainBox(
			$args->getOpt( 'reseller' ),
			$args->getOpt( 'username' ),
			$args->getOpt( 'password' ),
			$args->getOpt( 'sandbox' )
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
			
			// Check for API errors first
			if (isset($records->QueryDnsRecordsResult)) {
				$result = $records->QueryDnsRecordsResult;
				
				// Check for error codes
				if (isset($result->ResultCode) && $result->ResultCode != 100) {
					// Non-success result code
					$errorMsg = isset($result->ResultMsg) ? $result->ResultMsg : 'Unknown error';
					fprintf(STDERR, "API Error (Code %d): %s\n", $result->ResultCode, $errorMsg);
					
					// Add helpful message for authentication errors
					if ($result->ResultCode == 201) {
						fprintf(STDERR, "\nAuthentication failed. Please check:\n");
						fprintf(STDERR, "1. Your credentials are correct (reseller, username, password)\n");
						fprintf(STDERR, "2. Your IP address is whitelisted in DomainBox API settings\n");
						fprintf(STDERR, "3. API access is enabled for your account\n");
						fprintf(STDERR, "4. Try using --sandbox flag if you have sandbox credentials\n\n");
						fprintf(STDERR, "To whitelist your IP:\n");
						fprintf(STDERR, "- Go to https://admin.domainbox.net/account/ip-address/\n");
						fprintf(STDERR, "- Add your current IP address to the allowed list\n");
						fprintf(STDERR, "- Save the changes and wait a few minutes for them to take effect\n");
					}
					
					exit(1);
				}
				
				// Check if we have records
				if (isset($result->Records)) {
					// Handle both single record and array of records
					if (isset($result->Records->DnsRecordQueryResult)) {
						$dnsRecords = $result->Records->DnsRecordQueryResult;
						// Ensure it's an array for consistent handling
						if (!is_array($dnsRecords)) {
							$dnsRecords = array($dnsRecords);
						}
						$this->outputBind($dnsRecords);
					}
				}
			}
		} while ( isset($records->QueryDnsRecordsResult) && 
		          isset($records->QueryDnsRecordsResult->PageNumber) && 
		          isset($records->QueryDnsRecordsResult->TotalPages) &&
		          $records->QueryDnsRecordsResult->PageNumber < $records->QueryDnsRecordsResult->TotalPages );
	}

	/**
	 * @param $array
	 */
	private function outputBind( $array ) {
		// Check if array is valid before iterating
		if (!is_array($array) && !is_object($array)) {
			return;
		}
		
		foreach ( $array as $element ) {
			if (isset($element->RecordType)) {
				$function = 'output' . $element->RecordType;
				if ( method_exists( $this, $function ) ) {
					$this->$function( $element );
				}
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
