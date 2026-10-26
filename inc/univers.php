<?php
/**
 * Univers graphiques des pôles : CréaBot, Schlouk de Sciences et Sciences participatives ont leur propre habillage.
 *
 * On ajoute une classe au <body> (sv-univers-creabot, sv-univers-schlouk, sv-univers-sciences) sur la page du pôle,
 * ses sous-pages et l'archive de son pôle dans l'agenda. Le CSS (section 10 de src/css/main.css)
 * n'applique la charte du pôle qu'au contenu principal (<main>) : l'en-tête, le bloc « Suivre /
 * Soutenir » et le pied de page restent aux couleurs de Savoirs Vivants.
 *
 * Aucune modification des compositions n'est nécessaire : les pages déjà créées
 * prennent le nouvel habillage automatiquement. Dans l'éditeur (pas de <body> de page),
 * c'est la classe sv-pole-… de chaque section de pôle qui applique la charte du pôle.
 *
 * Multilingue : une page traduite (ex. /de/creabot-de/) a un autre slug ; on compare donc aussi
 * le slug de sa traduction dans la langue par défaut du site (Polylang), voir sv_univers_source().
 */
defined( 'ABSPATH' ) || exit;

/**
 * Identifiant de page (slug) ou terme de pôle → univers graphique.
 */
function sv_univers_pages(): array {
	return [
		'creabot'                 => 'creabot',
		'schlouk-de-sciences'     => 'schlouk',
		'sciences-participatives' => 'sciences',
	];
}

/**
 * Identifiant d'une page (ou d'un terme) dans la langue par défaut du site : la version allemande
 * d'une page de pôle hérite ainsi de l'univers de la page française dont elle est la traduction.
 * Sans extension multilingue, l'identifiant est renvoyé tel quel. Filtre « sv_univers_source »
 * pour une autre extension (WPML…).
 */
function sv_univers_source( int $id, string $type = 'post' ): int {
	if ( function_exists( 'pll_default_language' ) && function_exists( 'pll_get_post' ) && function_exists( 'pll_get_term' ) ) {
		$langue = pll_default_language();
		$source = 'term' === $type ? pll_get_term( $id, $langue ) : pll_get_post( $id, $langue );
		if ( $source ) {
			$id = (int) $source;
		}
	}
	return (int) apply_filters( 'sv_univers_source', $id, $type );
}

/**
 * Univers graphique de la page affichée : « creabot », « schlouk », « sciences » ou chaîne vide.
 */
function sv_univers_courant(): string {
	static $courant = null;
	if ( null !== $courant ) {
		return $courant;
	}
	// Requête principale pas encore connue : on ne mémorise rien.
	if ( ! did_action( 'wp' ) ) {
		return '';
	}

	$univers = sv_univers_pages();
	$slugs   = [];

	if ( is_page() ) {
		$page = get_queried_object();
		// La page elle-même puis ses parents (les sous-pages héritent de l'univers),
		// chacune sous son slug et sous celui de sa traduction dans la langue par défaut.
		foreach ( array_merge( [ $page->ID ], get_post_ancestors( $page ) ) as $id ) {
			$slugs[] = get_post_field( 'post_name', $id );
			$source  = sv_univers_source( (int) $id );
			if ( $source !== (int) $id ) {
				$slugs[] = get_post_field( 'post_name', $source );
			}
		}
	} elseif ( is_tax( 'sv_pole' ) ) {
		$terme       = get_queried_object();
		$slugs[]     = $terme->slug;
		$source      = sv_univers_source( (int) $terme->term_id, 'term' );
		$slug_source = $source !== (int) $terme->term_id ? get_term_field( 'slug', $source, 'sv_pole' ) : '';
		if ( is_string( $slug_source ) && '' !== $slug_source ) {
			$slugs[] = $slug_source;
		}
	}

	$courant = '';
	foreach ( $slugs as $slug ) {
		if ( isset( $univers[ $slug ] ) ) {
			$courant = $univers[ $slug ];
			break;
		}
	}

	return $courant;
}

add_filter(
	'body_class',
	function ( $classes ) {
		if ( sv_univers_courant() ) {
			$classes[] = 'sv-univers-' . sv_univers_courant();
		}
		return $classes;
	}
);
