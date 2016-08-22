<?php
$autoload  = 'vendor/autoload.php';
$patchwork = 'vendor/antecedent/patchwork/Patchwork.php';

# require patchwork first
if ( file_exists( $patchwork ) ) {
	require_once $patchwork;
}

if ( file_exists( $autoload ) ) {
	require_once $autoload;
}
