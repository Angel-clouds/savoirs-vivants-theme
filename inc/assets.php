<?php
/**
 * Chargement des feuilles de style et scripts.
 */
defined( 'ABSPATH' ) || exit;

/**
 * Front : CSS Tailwind compilé + gestionnaire de cookies (le script du formulaire est chargé par
 * [sv_formulaire_contact], seulement sur la page qui l'affiche).
 * Version = date de modification du fichier → cache navigateur invalidé à chaque build.
 */
add_action(
	'wp_enqueue_scripts',
	function () {
		$css = '/assets/css/main.css';
		if ( file_exists( SV_DIR . $css ) ) {
			wp_enqueue_style( 'sv-main', SV_URI . $css, [], filemtime( SV_DIR . $css ) );
		}

		/*
		* Gestionnaire de consentement tarteaucitron.js (Privacy by Design).
		* Aucun service tiers n'est déclaré pour l'instant : le site ne dépose aucun traceur, et
		* tarteaucitron n'est chargé qu'au clic sur « Gestion des cookies » (assets/js/cookies.js).
		* Pour ajouter un service plus tard (ex. vidéos YouTube) : filtre « sv_services_tiers »
		* (ex. [ 'youtube' ]) ; tarteaucitron est alors chargé sur toutes les pages, seul moyen de
		* bloquer le service avant le consentement. Voir le GUIDE (section RGPD).
		*/
		$tac = '/assets/vendor/tarteaucitron/tarteaucitron.min.js';
		if ( ! file_exists( SV_DIR . $tac ) ) {
			return;
		}
		// Langue de la bannière = langue de la page (fr, de…), si tarteaucitron la fournit ; sinon français.
		$langue = strtolower( substr( determine_locale(), 0, 2 ) );
		if ( ! preg_match( '/^[a-z]{2}$/', $langue ) || ! file_exists( SV_DIR . '/assets/vendor/tarteaucitron/lang/tarteaucitron.' . $langue . '.min.js' ) ) {
			$langue = 'fr';
		}
		$config   = [
			'privacyUrl'              => home_url( '/politique-de-confidentialite/' ),
			'hashtag'                 => '#tarteaucitron',
			'cookieName'              => 'sv_consentement',
			'orientation'             => 'bottom',
			'showAlertSmall'          => false,
			'cookieslist'             => false,
			'showIcon'                => false, // le bouton « Gestion des cookies » du pied de page suffit
			'highPrivacy'             => true,  // rien n'est chargé sans consentement explicite
			'handleBrowserDNTRequest' => true,
			'DenyAllCta'              => true,
			'AcceptAllCta'            => true,
			'removeCredit'            => true,
			'moreInfoLink'            => true,
			'useExternalCss'          => false,
			'mandatory'               => true,
		];
		$services = array_filter( (array) apply_filters( 'sv_services_tiers', [] ), 'is_string' );

		if ( ! $services ) {
			$js = '/assets/js/cookies.js';
			wp_enqueue_script(
				'sv-cookies',
				SV_URI . $js,
				[],
				filemtime( SV_DIR . $js ),
				[
					'strategy'  => 'defer',
					'in_footer' => true,
				]
			);
			$reglages = [
				'src'    => SV_URI . $tac . '?ver=1.34.0',
				'langue' => $langue,
				'config' => $config,
			];
			wp_add_inline_script( 'sv-cookies', 'window.svCookies = ' . wp_json_encode( $reglages ) . ';', 'before' );
			return;
		}

		// Pied de page : ne bloque pas l'affichage.
		wp_enqueue_script( 'tarteaucitron', SV_URI . $tac, [], '1.34.0', true );
		wp_add_inline_script( 'tarteaucitron', 'var tarteaucitronForceLanguage = ' . wp_json_encode( $langue ) . ';', 'before' );
		$taches = '';
		foreach ( $services as $service ) {
			$taches .= "\n(tarteaucitron.job = tarteaucitron.job || []).push(" . wp_json_encode( $service ) . ');';
		}
		wp_add_inline_script( 'tarteaucitron', 'tarteaucitron.init(' . wp_json_encode( $config ) . ');' . $taches );
	}
);

/**
 * Carrousel accessible : script chargé uniquement sur les pages qui contiennent
 * une « Boucle de requête » portant la classe sv-carrousel.
 */
add_filter(
	'render_block_core/query',
	function ( $html, $bloc ) {
		if ( str_contains( $bloc['attrs']['className'] ?? '', 'sv-carrousel' ) ) {
			$js = '/assets/js/carrousel.js';
			wp_enqueue_script(
				'sv-carrousel',
				SV_URI . $js,
				[],
				filemtime( SV_DIR . $js ),
				[
					'strategy'  => 'defer',
					'in_footer' => true,
				]
			);
		}
		return $html;
	},
	10,
	2
);

/**
 * Images SVG du thème (icônes, images de substitution) : dimensions lues dans le viewBox.
 * Sans width/height, WordPress n'ajoute pas loading="lazy" et le navigateur ne peut pas
 * réserver la place de l'image (décalage de mise en page, CLS). Les images de la médiathèque
 * reçoivent déjà ces attributs de WordPress.
 */
function sv_dimensions_svg( string $fichier ): ?array {
	static $cache = [];
	if ( ! array_key_exists( $fichier, $cache ) ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- lecture locale des 1 024 premiers octets d'un SVG du thème.
		$debut             = is_file( $fichier ) ? (string) file_get_contents( $fichier, false, null, 0, 1024 ) : '';
		$cache[ $fichier ] = preg_match( '/viewBox="[\d.\s-]*?\s([\d.]+)\s+([\d.]+)"/', $debut, $m )
		? [ (int) round( (float) $m[1] ), (int) round( (float) $m[2] ) ]
		: null;
	}
	return $cache[ $fichier ];
}

add_filter(
	'render_block_core/image',
	function ( string $html, array $bloc ): string {
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
		// Sauf le logo de l'en-tête (patterns/logo.php) : visible dès l'arrivée, jamais différé.
		$logo = str_contains( $bloc['attrs']['className'] ?? '', 'sv-logo-site' );
		if ( ! $logo && null === $img->get_attribute( 'loading' ) ) {
			$img->set_attribute( 'loading', 'lazy' );
		}
		return $img->get_updated_html();
	},
	10,
	2
);

/**
 * Éditeur : même feuille que le site, pour un rendu identique.
 */
add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'editor-styles' );
		add_editor_style( 'assets/css/main.css' );
	}
);

/**
 * Émojis : le script de détection de WordPress (wp-emoji-loader, imprimé dans <head> de chaque
 * page) et sa feuille de style ne servent qu'à remplacer les émojis par des images sur les
 * systèmes trop anciens pour les afficher. Les navigateurs actuels les affichent eux-mêmes.
 * Seul l'AFFICHAGE est retiré : l'encodage des émojis en entités HTML à l'enregistrement
 * (wp_encode_emoji, inc/contenus.php et import Google), indispensable tant que la base en ligne
 * est en utf8 sur 3 octets, n'est pas touché. L'administration garde le script (éditeurs sur
 * d'anciens postes).
 */
add_action(
	'init',
	function () {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'embed_head', 'print_emoji_detection_script' );
		remove_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' ); // WordPress < 6.4
	}
);

/**
 * Police des titres préchargée : le H1, souvent le plus grand élément à l'écran, et les boutons
 * de l'en-tête attendent Space Mono 700 (≈ 17 Ko). Sans préchargement, le navigateur ne la
 * découvre qu'après avoir lu le CSS. Même adresse que la règle @font-face générée depuis
 * theme.json (sinon double téléchargement) ; crossorigin obligatoire pour une police.
 */
add_action(
	'wp_head',
	function () {
		$police = '/assets/fonts/space-mono-latin-700-normal.woff2';
		if ( file_exists( SV_DIR . $police ) ) {
			printf(
				'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
				esc_url( SV_URI . $police )
			);
		}
	},
	1
);
