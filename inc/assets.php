<?php
/**
 * Chargement des feuilles de style et scripts.
 */
defined( 'ABSPATH' ) || exit;

/**
 * Front : CSS Tailwind compilé + script du thème + gestionnaire de cookies.
 * Version = date de modification du fichier → cache navigateur invalidé à chaque build.
 */
add_action( 'wp_enqueue_scripts', function () {
	$css = '/assets/css/main.css';
	if ( file_exists( SV_DIR . $css ) ) {
		wp_enqueue_style( 'sv-main', SV_URI . $css, [], filemtime( SV_DIR . $css ) );
	}

	$js = '/assets/js/sv.js';
	if ( file_exists( SV_DIR . $js ) ) {
		wp_enqueue_script( 'sv-main', SV_URI . $js, [], filemtime( SV_DIR . $js ), [ 'strategy' => 'defer', 'in_footer' => true ] );
	}

	/*
	 * Gestionnaire de consentement tarteaucitron.js (Privacy by Design).
	 * Aucun service tiers n'est déclaré pour l'instant : le site ne dépose aucun traceur.
	 * Pour ajouter un service plus tard (ex. vidéos YouTube), voir le GUIDE (section RGPD).
	 */
	$tac = '/assets/vendor/tarteaucitron/tarteaucitron.min.js';
	if ( file_exists( SV_DIR . $tac ) ) {
		wp_enqueue_script( 'tarteaucitron', SV_URI . $tac, [], '1.34.0', false );
		wp_add_inline_script( 'tarteaucitron', 'var tarteaucitronForceLanguage = "fr";', 'before' );

		$config = [
			'privacyUrl'               => home_url( '/politique-de-confidentialite/' ),
			'hashtag'                  => '#tarteaucitron',
			'cookieName'               => 'sv_consentement',
			'orientation'              => 'bottom',
			'showAlertSmall'           => false,
			'cookieslist'              => false,
			'showIcon'                 => false, // le bouton « Gestion des cookies » du pied de page suffit
			'highPrivacy'              => true,  // rien n'est chargé sans consentement explicite
			'handleBrowserDNTRequest'  => true,
			'DenyAllCta'               => true,
			'AcceptAllCta'             => true,
			'removeCredit'             => true,
			'moreInfoLink'             => true,
			'useExternalCss'           => false,
			'mandatory'                => true,
		];
		wp_add_inline_script(
			'tarteaucitron',
			'tarteaucitron.init(' . wp_json_encode( $config ) . ');'
			. "\n// Services tiers : (tarteaucitron.job = tarteaucitron.job || []).push('youtube');"
		);
	}
} );

/**
 * Éditeur : même feuille que le site, pour un rendu identique.
 */
add_action( 'after_setup_theme', function () {
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/main.css' );
} );

/**
 * Éditeur : panneau « Informations » (date, lieu, âge…) pour les événements et le matériel.
 * Script sans étape de compilation (wp.element.createElement).
 */
add_action( 'enqueue_block_editor_assets', function () {
	$ecran = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $ecran || ! in_array( $ecran->post_type, [ 'sv_evenement', 'sv_materiel' ], true ) ) {
		return;
	}

	$js = '/assets/js/editeur-champs.js';
	wp_enqueue_script(
		'sv-editeur-champs',
		SV_URI . $js,
		[ 'wp-plugins', 'wp-editor', 'wp-edit-post', 'wp-components', 'wp-data', 'wp-core-data', 'wp-element' ],
		filemtime( SV_DIR . $js ),
		true
	);
	wp_add_inline_script( 'sv-editeur-champs', 'window.svChampsEditeur = ' . wp_json_encode( sv_champs_editeur() ) . ';', 'before' );
} );
