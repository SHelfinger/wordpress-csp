<?php
/*
Plugin Name: SHelfinger CSP Valid Code
Plugin URI:  https://developer.wordpress.org/plugins/the-basics/
Description: CSP Nonces for WordPress, without changing the Core code
Version:     20170728
Author:      SHelfinger
Author URI:  https://shelfinger.eu
License:     GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/

// Your Nonce from the Header
$cspnonce = 'jahfjkdhlfjkdhfjldhlfjdfhdkfd';

ob_start();

add_action( 'shutdown', function() {
    $html = '';
    $levels = ob_get_level();

    for ( $i = 0; $i < $levels; $i++ ) {
        $html .= ob_get_clean();
    }
   
    echo apply_filters( 'csp_output', $html );
}, 0);

add_filter( 'csp_output', function( $html ) {
  $dom = new DOMDocument;
  libxml_use_internal_errors( true );
	$dom->loadHTML( $html );
	libxml_clear_errors();
	$scripts = $dom->getElementsByTagName( 'script' );
	foreach ( $scripts as $script ) {
		if ( ! $script->hasAttribute( 'nonce' ) ) {
			if ( $script->hasAttribute( 'type' ) ) {
				if ( $script->getAttribute( 'type' ) === 'text/javascript' ) {
					$script->setAttribute( 'nonce', $cspnonce );
				}
			}
		}
	}
	$styles = $dom->getElementsByTagName( 'style' );
	foreach ( $styles as $style ) {
		if ( ! $style->hasAttribute( 'nonce' ) ) {
			if ( $style->hasAttribute( 'type' ) ) {
				if ( $style->getAttribute( 'type' ) === 'text/css' ) {
					$style->setAttribute( 'nonce', $cspnonce );
				}
			}
		}
	}
	$html = $dom->saveHTML();
  return $html;
});
?>
