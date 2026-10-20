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
		// Pied de page : ne bloque pas l'affichage (aucun service tiers à intercepter dans <head>).
		wp_enqueue_script( 'tarteaucitron', SV_URI . $tac, [], '1.34.0', true );
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
 * Carrousel accessible : script chargé uniquement sur les pages qui contiennent
 * une « Boucle de requête » portant la classe sv-carrousel.
 */
add_filter( 'render_block_core/query', function ( $html, $bloc ) {
	if ( str_contains( $bloc['attrs']['className'] ?? '', 'sv-carrousel' ) ) {
		$js = '/assets/js/carrousel.js';
		wp_enqueue_script( 'sv-carrousel', SV_URI . $js, [], filemtime( SV_DIR . $js ), [ 'strategy' => 'defer', 'in_footer' => true ] );
	}
	return $html;
}, 10, 2 );

/**
 * Images SVG du thème (icônes, images de substitution) : dimensions lues dans le viewBox.
 * Sans width/height, WordPress n'ajoute pas loading="lazy" et le navigateur ne peut pas
 * réserver la place de l'image (décalage de mise en page, CLS). Les images de la médiathèque
 * reçoivent déjà ces attributs de WordPress.
 */
function sv_dimensions_svg( string $fichier ): ?array {
	static $cache = [];
	if ( ! array_key_exists( $fichier, $cache ) ) {
		$debut = is_file( $fichier ) ? (string) file_get_contents( $fichier, false, null, 0, 1024 ) : '';
		$cache[ $fichier ] = preg_match( '/viewBox="[\d.\s-]*?\s([\d.]+)\s+([\d.]+)"/', $debut, $m )
			? [ (int) round( (float) $m[1] ), (int) round( (float) $m[2] ) ]
			: null;
	}
	return $cache[ $fichier ];
}

add_filter( 'render_block_core/image', function ( string $html ): string {
	$img = new WP_HTML_Tag_Processor( $html );
	if ( ! $img->next_tag( 'img' ) || null !== $img->get_attribute( 'width' ) ) {
		return $html;
	}
	$src    = strtok( (string) $img->get_attribute( 'src' ), '?' );
	$racine = SV_URI . '/assets/svg/';
	if ( ! str_starts_with( $src, $racine ) ) {
		return $html;
	}
	$dimensions = sv_dimensions_svg( SV_DIR . '/assets/svg/' . basename( $src ) );
	if ( ! $dimensions ) {
		return $html;
	}
	$img->set_attribute( 'width', (string) $dimensions[0] );
	$img->set_attribute( 'height', (string) $dimensions[1] );
	// Décor léger : chargement différé, pour que fetchpriority="high" reste aux vraies photos.
	if ( null === $img->get_attribute( 'loading' ) ) {
		$img->set_attribute( 'loading', 'lazy' );
	}
	return $img->get_updated_html();
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
