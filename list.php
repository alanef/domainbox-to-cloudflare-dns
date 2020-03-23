<?php
require __DIR__ . '/vendor/autoload.php';
if ( ! extension_loaded( 'soap' ) ) {
	throw new Exception( 'DomainBox needs the SOAP PHP extension.' );
}

$command = new Command\Execute();
$command->Run();
