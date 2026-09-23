<?php
/**
 * Types de contenus : Événements (Agenda) et Matériel (Ressourcerie).
 *
 * Note d'architecture : ces types sont déclarés dans le thème pour rester simples
 * à maintenir. Si l'association change un jour de thème, déplacer ce fichier dans
 * wp-content/mu-plugins/ pour conserver les contenus.
 */
defined( 'ABSPATH' ) || exit;

/**
 * Définition des champs saisis dans le panneau « Informations » de l'éditeur.
 * Source unique : sert à la fois à l'enregistrement des métadonnées et au panneau JS.
 */
function sv_champs_editeur(): array {
	return [
		'sv_evenement' => [
			'titre'  => 'Informations de l’événement',
			'champs' => [
				[ 'cle' => 'sv_date', 'label' => 'Date', 'type' => 'date', 'aide' => 'Obligatoire pour apparaître dans l’agenda.' ],
				[ 'cle' => 'sv_heure_debut', 'label' => 'Heure de début', 'type' => 'time' ],
				[ 'cle' => 'sv_heure_fin', 'label' => 'Heure de fin', 'type' => 'time' ],
				[ 'cle' => 'sv_lieu', 'label' => 'Lieu', 'type' => 'text', 'aide' => 'Ex. : IUT de Haguenau, 30 rue du Maire André Traband' ],
				[ 'cle' => 'sv_lien_inscription', 'label' => 'Lien d’inscription (facultatif)', 'type' => 'url' ],
			],
		],
		'sv_materiel'  => [
			'titre'  => 'Informations du matériel',
			'champs' => [
				[ 'cle' => 'sv_age', 'label' => 'Âge conseillé', 'type' => 'text', 'aide' => 'Ex. : Dès 6 ans' ],
				[ 'cle' => 'sv_tarif', 'label' => 'Tarif de location', 'type' => 'text', 'aide' => 'Ex. : 15 € la semaine' ],
				[ 'cle' => 'sv_prerequis', 'label' => 'Équipement nécessaire à la maison', 'type' => 'text', 'aide' => 'Ex. : ordinateur ou tablette' ],
			],
		],
	];
}

add_action( 'init', function () {

	/* ---------- Événements (Agenda) ---------- */
	register_post_type( 'sv_evenement', [
		'labels'        => [
			'name'               => 'Événements',
			'singular_name'      => 'Événement',
			'menu_name'          => 'Agenda',
			'add_new'            => 'Ajouter un événement',
			'add_new_item'       => 'Ajouter un événement',
			'edit_item'          => 'Modifier l’événement',
			'new_item'           => 'Nouvel événement',
			'view_item'          => 'Voir l’événement',
			'search_items'       => 'Rechercher un événement',
			'not_found'          => 'Aucun événement trouvé',
			'all_items'          => 'Tous les événements',
		],
		'public'        => true,
		'show_in_rest'  => true,
		'menu_icon'     => 'dashicons-calendar-alt',
		'menu_position' => 20,
		'has_archive'   => 'agenda',
		'rewrite'       => [ 'slug' => 'agenda', 'with_front' => false ],
		'supports'      => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ],
		'template'      => [
			[ 'core/paragraph', [ 'placeholder' => 'Décrivez l’événement : programme, public concerné, intervenant·es…' ] ],
		],
	] );

	register_taxonomy( 'sv_pole', [ 'sv_evenement' ], [
		'labels'            => [
			'name'          => 'Pôles',
			'singular_name' => 'Pôle',
			'add_new_item'  => 'Ajouter un pôle',
		],
		'hierarchical'      => true,
		'public'            => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'rewrite'           => [ 'slug' => 'agenda/pole', 'with_front' => false ],
	] );

	/* ---------- Matériel (Ressourcerie) ---------- */
	register_post_type( 'sv_materiel', [
		'labels'        => [
			'name'               => 'Matériel',
			'singular_name'      => 'Matériel',
			'menu_name'          => 'Ressourcerie',
			'add_new'            => 'Ajouter du matériel',
			'add_new_item'       => 'Ajouter du matériel',
			'edit_item'          => 'Modifier le matériel',
			'view_item'          => 'Voir la fiche',
			'search_items'       => 'Rechercher du matériel',
			'not_found'          => 'Aucun matériel trouvé',
			'all_items'          => 'Tout le matériel',
		],
		'public'        => true,
		'show_in_rest'  => true,
		'menu_icon'     => 'dashicons-archive',
		'menu_position' => 21,
		'has_archive'   => false, // la page « Ressourcerie » liste le matériel
		'rewrite'       => [ 'slug' => 'ressourcerie', 'with_front' => false ],
		'supports'      => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ],
		'template'      => [
			[ 'core/paragraph', [ 'placeholder' => 'Présentez le matériel : ce qu’il permet de faire, comment l’utiliser…' ] ],
		],
	] );

	register_taxonomy( 'sv_type_materiel', [ 'sv_materiel' ], [
		'labels'            => [
			'name'          => 'Types de matériel',
			'singular_name' => 'Type de matériel',
			'add_new_item'  => 'Ajouter un type',
		],
		'hierarchical'      => true,
		'public'            => false,
		'show_ui'           => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
	] );

	/* ---------- Champs (métadonnées) ---------- */
	foreach ( sv_champs_editeur() as $type => $config ) {
		foreach ( $config['champs'] as $champ ) {
			register_post_meta( $type, $champ['cle'], [
				'type'              => 'string',
				'label'             => $champ['label'],
				'single'            => true,
				'default'           => '',
				'show_in_rest'      => true,
				'sanitize_callback' => 'url' === $champ['type'] ? 'esc_url_raw' : 'sanitize_text_field',
				'auth_callback'     => fn() => current_user_can( 'edit_posts' ),
			] );
		}
	}
}, 5 );

/**
 * Termes par défaut (créés une seule fois).
 */
add_action( 'init', function () {
	$defauts = [
		'sv_pole'          => [ 'CréaBot' => 'creabot', 'Schlouk de Sciences' => 'schlouk-de-sciences', 'Sciences participatives' => 'sciences-participatives', 'Savoirs Vivants' => 'savoirs-vivants' ],
		'sv_type_materiel' => [ 'Robotique' => 'robotique', 'Électronique' => 'electronique', 'Créativité' => 'creativite', 'Mécanique' => 'mecanique' ],
	];
	foreach ( $defauts as $taxonomie => $termes ) {
		foreach ( $termes as $nom => $slug ) {
			if ( ! term_exists( $slug, $taxonomie ) ) {
				wp_insert_term( $nom, $taxonomie, [ 'slug' => $slug ] );
			}
		}
	}
}, 6 );

/**
 * Permaliens régénérés à l'activation du thème (sinon /agenda/ et /ressourcerie/xxx/ renverraient une 404).
 */
add_action( 'after_switch_theme', 'flush_rewrite_rules' );

/**
 * Source de liaison (Block Bindings) : affiche les informations d'un événement
 * mises en forme (jour, mois, date complète, horaires, lieu) dans n'importe quel
 * bloc Paragraphe, y compris à l'intérieur d'une boucle de requête.
 */
add_action( 'init', function () {
	if ( ! function_exists( 'register_block_bindings_source' ) ) {
		return;
	}
	register_block_bindings_source( 'savoirs-vivants/evenement', [
		'label'              => 'Événement (Savoirs Vivants)',
		'get_value_callback' => 'sv_valeur_evenement',
		'uses_context'       => [ 'postId' ],
	] );
} );

function sv_heure( string $heure ): string {
	return $heure ? str_replace( ':', ' h ', $heure ) : '';
}

function sv_valeur_evenement( array $args, $bloc ) {
	$id   = $bloc->context['postId'] ?? get_the_ID();
	$date = get_post_meta( $id, 'sv_date', true );
	$ts   = $date ? strtotime( $date . ' 12:00:00' ) : false;

	$debut = sv_heure( (string) get_post_meta( $id, 'sv_heure_debut', true ) );
	$fin   = sv_heure( (string) get_post_meta( $id, 'sv_heure_fin', true ) );
	$lieu  = (string) get_post_meta( $id, 'sv_lieu', true );

	$horaires = '';
	if ( $debut && $fin ) {
		$horaires = sprintf( 'De %s à %s', $debut, $fin );
	} elseif ( $debut ) {
		$horaires = sprintf( 'À partir de %s', $debut );
	}

	switch ( $args['key'] ?? '' ) {
		case 'jour':
			$valeur = $ts ? wp_date( 'j', $ts ) : '';
			break;
		case 'mois':
			$valeur = $ts ? wp_date( 'F', $ts ) : '';
			break;
		case 'date':
			$valeur = $ts ? wp_date( 'l j F Y', $ts ) : 'Date à venir';
			break;
		case 'horaires':
			$valeur = $horaires;
			break;
		case 'lieu':
			$valeur = $lieu;
			break;
		case 'horaires_lieu':
			$valeur = implode( ' · ', array_filter( [ $horaires, $lieu ] ) );
			break;
		default:
			$valeur = '';
	}

	return esc_html( $valeur );
}

/**
 * Agenda : n'afficher que les événements à venir, du plus proche au plus lointain.
 * S'applique aux blocs « Boucle de requête » et à la page /agenda/.
 */
function sv_requete_evenements_a_venir( array $vars ): array {
	$vars['meta_key']   = 'sv_date';
	$vars['orderby']    = 'meta_value';
	$vars['order']      = 'ASC';
	$vars['meta_query'] = [
		[
			'key'     => 'sv_date',
			'value'   => current_time( 'Y-m-d' ),
			'compare' => '>=',
			'type'    => 'DATE',
		],
	];
	return $vars;
}

add_filter( 'query_loop_block_query_vars', function ( $vars ) {
	$type = $vars['post_type'] ?? '';
	if ( 'sv_evenement' === $type || ( is_array( $type ) && in_array( 'sv_evenement', $type, true ) ) ) {
		$vars = sv_requete_evenements_a_venir( $vars );
	}
	return $vars;
} );

add_action( 'pre_get_posts', function ( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( $query->is_post_type_archive( 'sv_evenement' ) || $query->is_tax( 'sv_pole' ) ) {
		foreach ( sv_requete_evenements_a_venir( [] ) as $cle => $valeur ) {
			$query->set( $cle, $valeur );
		}
	}
} );
/**
 * Confort de saisie : si aucun pôle n'est coché, on le devine d'après le titre
 * de l'événement (« CréaBot », « Schlouk », « Pint of Science »…).
 * La case cochée manuellement reste toujours prioritaire.
 */
add_action( 'save_post_sv_evenement', function ( $id, $post ) {
	if ( wp_is_post_autosave( $id ) || wp_is_post_revision( $id ) ) {
		return;
	}
	if ( has_term( '', 'sv_pole', $id ) ) {
		return; // un pôle est déjà choisi : on ne touche à rien
	}

	$titre = strtolower( remove_accents( $post->post_title ) );

	$indices = [
		'creabot'                 => [ 'creabot', 'robotique', 'robotech', 'club maker', 'factory kids' ],
		'schlouk-de-sciences'     => [ 'schlouk', 'pint of science', 'choco', 'cafe botanique', 'quiz' ],
		'sciences-participatives' => [ 'sure', 'usuel', 'vir-age', 'otez l', 'crep', 'ilot', 'participativ' ],
	];

	foreach ( $indices as $pole => $mots ) {
		foreach ( $mots as $mot ) {
			if ( str_contains( $titre, $mot ) ) {
				wp_set_object_terms( $id, $pole, 'sv_pole' );
				return;
			}
		}
	}
}, 10, 2 );
