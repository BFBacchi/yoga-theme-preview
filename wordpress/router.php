<?php
/**
 * Router for the PHP built-in web server so WordPress pretty permalinks work.
 * Usage: php -S 0.0.0.0:8080 -t . router.php
 *
 * Note: the active theme is symlinked from outside the docroot, so paths are
 * checked lexically (not via realpath) before being handed to the server.
 */

$path = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
$path = urldecode( $path );
$root = __DIR__;

// Normalise and block path traversal.
$relative = ltrim( str_replace( '\\', '/', $path ), '/' );
if ( strpos( $relative, '..' ) !== false ) {
	http_response_code( 400 );
	echo 'Bad request';
	return true;
}

$target = $root . '/' . $relative;

// Serve real static files (css, js, images, fonts) directly.
if ( $relative !== '' && is_file( $target ) ) {
	$ext = strtolower( pathinfo( $target, PATHINFO_EXTENSION ) );
	if ( $ext !== 'php' ) {
		return false; // let the built-in server stream it
	}
	$_SERVER['SCRIPT_NAME'] = $path;
	require $target;
	return true;
}

// Directory requests: use the directory's index.php if it exists.
if ( $relative !== '' && is_dir( $target ) ) {
	$index = rtrim( $target, '/' ) . '/index.php';
	if ( file_exists( $index ) ) {
		$_SERVER['SCRIPT_NAME'] = rtrim( $path, '/' ) . '/index.php';
		require $index;
		return true;
	}
}

// Everything else goes through WordPress.
$_SERVER['SCRIPT_NAME'] = '/index.php';
require $root . '/index.php';
