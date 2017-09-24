<?php
use Codeman\Template as Page;
require 'core/Template.php';

$data = [
	'index'		=>	[
		'title'			=>	'Codeman',
		'description'	=>	'Description',
	],
	'about'	=>	[
		'title'			=>	'Company - Codeman',
		'description'	=>	'Description',
	],
	'contact'	=>	[
		'title'			=>	'Contact - Codeman',
		'description'	=>	'Description',
	],
];

try {
	$page = new Page( $data );
	// $page -> build_project();
	$page -> show();
}	// end try

catch( Exception $error ) {
	echo $error -> getMessage();
}	// end catch