<?php
/**
 * Sécurité et nettoyage : comptes non exposés, commentaires fermés.
 */
defined( 'ABSPATH' ) || exit;

/**
 * API REST : la liste des comptes (/wp-json/wp/v2/users) n'est servie qu'aux personnes
 * connectées qui rédigent du contenu (l'éditeur de blocs en a besoin pour choisir l'auteur).
 */
add_filter(
	'rest_endpoints',
	function ( $routes ) {
		if ( current_user_can( 'edit_posts' ) ) {
			return $routes;
		}
		foreach ( array_keys( $routes ) as $route ) {
			if ( 0 === strpos( $route, '/wp/v2/users' ) ) {
				unset( $routes[ $route ] );
			}
		}
		return $routes;
	}
);

/**
 * Archives d'auteur (/author/direction/, ?author=1) : redirection 301 vers l'accueil.
 * Priorité 1 : avant redirect_canonical(), qui révélerait l'identifiant dans l'adresse.
 */
add_action(
	'template_redirect',
	function () {
		if ( is_author() || isset( $_GET['author'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			wp_safe_redirect( home_url( '/' ), 301 );
			exit;
		}
	},
	1
);

/**
 * Flux RSS / Atom (actualités, agenda) : nom d'auteur remplacé par celui de l'équipe,
 * pour ne pas révéler les noms des comptes. Les flux restent actifs pour la syndication.
 */
function sv_anonymize_author_in_feed( $author ) {
	if ( is_feed() ) {
		return _x( 'L’équipe Savoirs Vivants', 'auteur affiché dans les flux RSS', 'savoirs-vivants-fonctionnalites' );
	}
	return $author;
}
add_filter( 'the_author', 'sv_anonymize_author_in_feed' );
add_filter( 'get_the_author_display_name', 'sv_anonymize_author_in_feed' );

// Pas de comptes dans le plan du site XML (/wp-sitemap-users-1.xml).
add_filter(
	'wp_sitemaps_add_provider',
	function ( $provider, $nom ) {
		return 'users' === $nom ? false : $provider;
	},
	10,
	2
);

// Pas de nom d'auteur dans les données oEmbed (/wp-json/oembed/1.0/embed).
add_filter(
	'oembed_response_data',
	function ( $donnees ) {
		unset( $donnees['author_name'], $donnees['author_url'] );
		return $donnees;
	}
);

/**
 * Commentaires et rétroliens fermés sur tout le site, contenus existants compris.
 */
add_action(
	'init',
	function () {
		foreach ( get_post_types() as $type ) {
			remove_post_type_support( $type, 'comments' );
			remove_post_type_support( $type, 'trackbacks' );
		}
	},
	100
);

add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );
add_filter( 'comments_array', '__return_empty_array', 20 );
add_filter( 'feed_links_show_comments_feed', '__return_false' );

// Réglage « Autoriser les commentaires » forcé à « fermé » pour les nouveaux contenus.
add_filter( 'pre_option_default_comment_status', fn() => 'closed' );
add_filter( 'pre_option_default_ping_status', fn() => 'closed' );

/**
 * En-têtes de sécurité des pages publiques (les mini-sites envoient les leurs, inc/mini-sites.php).
 */
add_action(
	'send_headers',
	function () {
		header( 'X-Content-Type-Options: nosniff' );
		header( 'X-Frame-Options: SAMEORIGIN' );
		header( 'Referrer-Policy: strict-origin-when-cross-origin' );
		header( 'Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=(), usb=()' );
	}
);

// Version de WordPress retirée du code des pages et des flux.
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );

/**
 * Version de WordPress masquée dans les adresses des CSS et JS (?ver=7.1.3) : remplacée par
 * une empreinte. Le paramètre est gardé, sinon les navigateurs conserveraient l'ancien fichier
 * en cache après une mise à jour ; les fichiers du thème et de l'extension (?ver=date du fichier) sont inchangés.
 */
function sv_masquer_version_wp( string $src ): string {
	$version = get_bloginfo( 'version' );
	if ( str_contains( $src, 'ver=' . $version ) ) {
		$src = add_query_arg( 'ver', substr( wp_hash( 'ver-' . $version ), 0, 10 ), $src );
	}
	return $src;
}
add_filter( 'style_loader_src', 'sv_masquer_version_wp', 20 );
add_filter( 'script_loader_src', 'sv_masquer_version_wp', 20 ); // y compris l'adresse du script des émojis

/**
 * XML-RPC désactivé : ni authentification, ni pingback, ni méthode publiée,
 * et toute requête vers xmlrpc.php reçoit un refus (403) avant d'être traitée.
 */
add_filter( 'xmlrpc_enabled', '__return_false' );
add_filter( 'xmlrpc_methods', '__return_empty_array' );
remove_action( 'wp_head', 'rsd_link' );
add_filter(
	'wp_headers',
	function ( array $entetes ): array {
		unset( $entetes['X-Pingback'] );
		return $entetes;
	}
);
add_action(
	'init',
	function () {
		if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {
			status_header( 403 );
			header( 'Content-Type: text/plain; charset=utf-8' );
			exit( esc_html__( 'XML-RPC est désactivé sur ce site.', 'savoirs-vivants-fonctionnalites' ) );
		}
	},
	0
);
