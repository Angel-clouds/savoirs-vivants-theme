<?php
defined( 'ABSPATH' ) || exit;

/**
 * Front : charge la feuille Tailwind compilée.
 * La version = date de modification du fichier → le cache du
 * navigateur se met à jour automatiquement après chaque build.
 */
add_action( 'wp_enqueue_scripts', function () {
	$css = '/assets/css/main.css';
	if ( file_exists( SV_DIR . $css ) ) {
		wp_enqueue_style( 'sv-main', SV_URI . $css, [], filemtime( SV_DIR . $css ) );
	}
} );

/**
 * Éditeur Gutenberg : même feuille, pour un rendu identique au site.
 */
add_action( 'after_setup_theme', function () {
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/main.css' );
} );