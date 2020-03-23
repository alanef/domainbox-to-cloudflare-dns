<?php

namespace Command;

use Garden\Cli\Cli;

class Execute {

	public function __construct() {
		global $argv;
		$cli = new Cli();

		$cli->description('Extracts DomainBox DNS into Bind format for Cloudflare.')
		    ->opt('reseller:r', 'Domainbox Reseller.', true)
		    ->opt('username:u', 'Username.', true)
		    ->opt('password:p', 'Password for Domainbox.', true);

		$args = $cli->parse($argv, true);

		$this->domainbox = new DomainBox( $args->getOpt('reseller'), $args->getOpt('username'), $args->getOpt('password'), false );
	}

	public function run() {
		$page = 1;
		do {
			$records = $this->domainbox->doCall(
				'QueryDnsRecords',
				array(
					'Zone' => 'fullworks.net',
					'PageNumber' => $page++,
				)
			);
			$this->outputBind($records->QueryDnsRecordsResult->Records->DnsRecordQueryResult);
		} while ( $records->QueryDnsRecordsResult->PageNumber < $records->QueryDnsRecordsResult->TotalPages );

	}
	private function outputBind($array) {
		foreach ( $array as $element) {
			$function = 'output'.$element->RecordType;
			if ( method_exists( $this, $function )) {
				$this->$function($element);
			}
		}
	}

	private function outputA($record) {
		printf("$record->HostName\t\tIN\tA\t$record->Content\r\n");
	}
	private function outputMX($record) {
		printf("$record->HostName\t\tIN\tMX\t$record->Priority\t$record->Content\r\n");
	}
	private function outputCNAME($record) {
		printf("$record->HostName\t\tIN\tCNAME\t$record->Content\r\n");
	}
	private function outputTXT($record) {
		printf("$record->HostName\t\tIN\tTXT\t\"$record->Content\"\r\n");
	}
}
