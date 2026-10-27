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
			'titre'   => __( 'Informations de l’événement', 'savoirs-vivants' ),
			// Choix obligatoire des agendas (termes sv_pole) : enregistrement bloqué tant qu'aucun n'est coché.
			'agendas' => [
				'legende'        => __( 'Agendas où afficher l’événement', 'savoirs-vivants' ),
				'aide'           => __( 'Cochez au moins un agenda. L’agenda général (/agenda/) affiche tous les événements.', 'savoirs-vivants' ),
				'manquant'       => __( 'Choisissez au moins un agenda pour pouvoir enregistrer l’événement.', 'savoirs-vivants' ),
				'date_manquante' => __( 'Sans date, l’événement n’apparaît dans aucun agenda.', 'savoirs-vivants' ),
				'date_passee'    => __( 'Cette date est passée : l’événement n’apparaît plus dans les agendas.', 'savoirs-vivants' ),
				'fin_invalide'   => __( 'La date de fin doit être après la date de début : sinon elle est ignorée.', 'savoirs-vivants' ),
			],
			'champs'  => [
				[
					'cle'   => 'sv_date',
					'label' => _x( 'Date', 'champ d’un événement', 'savoirs-vivants' ),
					'type'  => 'date',
					'aide'  => __( 'Obligatoire pour apparaître dans l’agenda. Premier jour si l’événement dure plusieurs jours.', 'savoirs-vivants' ),
				],
				[
					'cle'   => 'sv_date_fin',
					'label' => __( 'Date de fin (plusieurs jours)', 'savoirs-vivants' ),
					'type'  => 'date',
					'aide'  => __( 'À laisser vide pour un événement d’une seule journée. Les heures s’appliquent à chaque jour.', 'savoirs-vivants' ),
				],
				[
					'cle'   => 'sv_heure_debut',
					'label' => __( 'Heure de début', 'savoirs-vivants' ),
					'type'  => 'time',
				],
				[
					'cle'   => 'sv_heure_fin',
					'label' => __( 'Heure de fin', 'savoirs-vivants' ),
					'type'  => 'time',
				],
				[
					'cle'   => 'sv_lieu',
					'label' => _x( 'Lieu', 'champ d’un événement', 'savoirs-vivants' ),
					'type'  => 'text',
					'aide'  => __( 'Ex. : IUT de Haguenau, 30 rue du Maire André Traband', 'savoirs-vivants' ),
				],
				[
					'cle'   => 'sv_lien_inscription',
					'label' => __( 'Lien d’inscription (facultatif)', 'savoirs-vivants' ),
					'type'  => 'url',
				],
			],
		],
		'sv_materiel'  => [
			'titre'  => __( 'Informations du matériel', 'savoirs-vivants' ),
			'champs' => [
				[
					'cle'   => 'sv_age',
					'label' => __( 'Âge conseillé', 'savoirs-vivants' ),
					'type'  => 'text',
					'aide'  => __( 'Ex. : Dès 6 ans', 'savoirs-vivants' ),
				],
				[
					'cle'   => 'sv_tarif',
					'label' => __( 'Tarif de location', 'savoirs-vivants' ),
					'type'  => 'text',
					'aide'  => __( 'Ex. : 15 € la semaine', 'savoirs-vivants' ),
				],
				[
					'cle'   => 'sv_prerequis',
					'label' => __( 'Équipement nécessaire à la maison', 'savoirs-vivants' ),
					'type'  => 'text',
					'aide'  => __( 'Ex. : ordinateur ou tablette', 'savoirs-vivants' ),
				],
			],
		],
	];
}

add_action(
	'init',
	function () {

		/*
		* Pôles déclarés AVANT les événements : leurs règles d'adresse (/agenda/pole/creabot/)
		* passent ainsi devant celles des fichiers joints aux événements (/agenda/xxx/yyy/),
		* qui les capturaient et renvoyaient une 404.
		*/
		register_taxonomy(
			'sv_pole',
			[ 'sv_evenement' ],
			[
				'labels'            => [
					'name'          => __( 'Pôles', 'savoirs-vivants' ),
					'singular_name' => __( 'Pôle', 'savoirs-vivants' ),
					'add_new_item'  => __( 'Ajouter un pôle', 'savoirs-vivants' ),
				],
				'hierarchical'      => true,
				'public'            => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'rewrite'           => [
					'slug'       => 'agenda/pole',
					'with_front' => false,
				],
			]
		);

		/* ---------- Événements (Agenda) ---------- */
		register_post_type(
			'sv_evenement',
			[
				'labels'        => [
					'name'          => __( 'Événements', 'savoirs-vivants' ),
					'singular_name' => __( 'Événement', 'savoirs-vivants' ),
					'menu_name'     => _x( 'Agenda', 'menu d’administration', 'savoirs-vivants' ),
					'add_new'       => __( 'Ajouter un événement', 'savoirs-vivants' ),
					'add_new_item'  => __( 'Ajouter un événement', 'savoirs-vivants' ),
					'edit_item'     => __( 'Modifier l’événement', 'savoirs-vivants' ),
					'new_item'      => __( 'Nouvel événement', 'savoirs-vivants' ),
					'view_item'     => __( 'Voir l’événement', 'savoirs-vivants' ),
					'search_items'  => __( 'Rechercher un événement', 'savoirs-vivants' ),
					'not_found'     => __( 'Aucun événement trouvé', 'savoirs-vivants' ),
					'all_items'     => __( 'Tous les événements', 'savoirs-vivants' ),
				],
				'public'        => true,
				'show_in_rest'  => true,
				'menu_icon'     => 'dashicons-calendar-alt',
				'menu_position' => 20,
				'has_archive'   => false, // /agenda/ est une vraie page (composition page-agenda, filtres : inc/agenda-filtres.php)
				'rewrite'       => [
					'slug'       => 'agenda',
					'with_front' => false,
				],
				'supports'      => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ],
				'template'      => [
					[ 'core/paragraph', [ 'placeholder' => __( 'Décrivez l’événement : programme, public concerné, intervenant·es…', 'savoirs-vivants' ) ] ],
				],
			]
		);

		/* ---------- Matériel (Ressourcerie) ---------- */
		register_post_type(
			'sv_materiel',
			[
				'labels'        => [
					'name'          => _x( 'Matériel', 'type de contenu (pluriel)', 'savoirs-vivants' ),
					'singular_name' => _x( 'Matériel', 'type de contenu (singulier)', 'savoirs-vivants' ),
					'menu_name'     => _x( 'Ressourcerie', 'menu d’administration', 'savoirs-vivants' ),
					'add_new'       => __( 'Ajouter du matériel', 'savoirs-vivants' ),
					'add_new_item'  => __( 'Ajouter du matériel', 'savoirs-vivants' ),
					'edit_item'     => __( 'Modifier le matériel', 'savoirs-vivants' ),
					'view_item'     => __( 'Voir la fiche', 'savoirs-vivants' ),
					'search_items'  => __( 'Rechercher du matériel', 'savoirs-vivants' ),
					'not_found'     => __( 'Aucun matériel trouvé', 'savoirs-vivants' ),
					'all_items'     => __( 'Tout le matériel', 'savoirs-vivants' ),
				],
				'public'        => true,
				'show_in_rest'  => true,
				'menu_icon'     => 'dashicons-archive',
				'menu_position' => 21,
				'has_archive'   => false, // la page « Ressourcerie » liste le matériel
				'rewrite'       => [
					'slug'       => 'ressourcerie',
					'with_front' => false,
				],
				'supports'      => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ],
				'template'      => [
					[ 'core/paragraph', [ 'placeholder' => __( 'Présentez le matériel : ce qu’il permet de faire, comment l’utiliser…', 'savoirs-vivants' ) ] ],
				],
			]
		);

		register_taxonomy(
			'sv_type_materiel',
			[ 'sv_materiel' ],
			[
				'labels'            => [
					'name'          => __( 'Types de matériel', 'savoirs-vivants' ),
					'singular_name' => __( 'Type de matériel', 'savoirs-vivants' ),
					'add_new_item'  => __( 'Ajouter un type', 'savoirs-vivants' ),
				],
				'hierarchical'      => true,
				'public'            => false,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
			]
		);

		/* ---------- Champs (métadonnées) ---------- */
		foreach ( sv_champs_editeur() as $type => $config ) {
			foreach ( $config['champs'] as $champ ) {
				register_post_meta(
					$type,
					$champ['cle'],
					[
						'type'              => 'string',
						'label'             => $champ['label'],
						'single'            => true,
						'default'           => '',
						'show_in_rest'      => true,
						'sanitize_callback' => 'url' === $champ['type'] ? 'esc_url_raw' : 'sanitize_text_field',
						// Droit vérifié sur le contenu modifié, pas en général (défense en profondeur).
						'auth_callback'     => fn( $autorise, $cle, $post_id ) => current_user_can( 'edit_post', (int) $post_id ),
					]
				);
			}
		}
	},
	5
);

/**
 * Termes par défaut (créés une seule fois).
 * L'option « sv_termes_defaut » (chargée d'office avec les réglages, donc sans requête) évite
 * 16 requêtes SQL à chaque page : incrémenter SV_TERMES_DEFAUT si la liste ci-dessous change.
 * Un terme supprimé ensuite par l'équipe n'est pas recréé.
 */
const SV_TERMES_DEFAUT = '1';
add_action(
	'init',
	function () {
		if ( get_option( 'sv_termes_defaut' ) === SV_TERMES_DEFAUT ) {
			return;
		}
		// Noms des termes créés une seule fois en français : ce sont des contenus, traduits ensuite
		// par l'extension multilingue (pas par gettext).
		$defauts = [
			'sv_pole'          => [
				'CréaBot'                 => 'creabot',
				'Schlouk de Sciences'     => 'schlouk-de-sciences',
				'Sciences participatives' => 'sciences-participatives',
				'Savoirs Vivants'         => 'savoirs-vivants',
			],
			'sv_type_materiel' => [
				'Robotique'    => 'robotique',
				'Électronique' => 'electronique',
				'Créativité'   => 'creativite',
				'Mécanique'    => 'mecanique',
			],
		];
		foreach ( $defauts as $taxonomie => $termes ) {
			foreach ( $termes as $nom => $slug ) {
				if ( ! term_exists( $slug, $taxonomie ) ) {
					wp_insert_term( $nom, $taxonomie, [ 'slug' => $slug ] );
				}
			}
		}
		update_option( 'sv_termes_defaut', SV_TERMES_DEFAUT );
	},
	6
);

/**
 * Permaliens régénérés à l'activation du thème (sinon /agenda/pole/xxx/ et /ressourcerie/xxx/ renverraient une 404).
 */
add_action( 'after_switch_theme', 'flush_rewrite_rules' );

/**
 * Permaliens régénérés quand les règles d'adresse du thème changent (incrémenter SV_REGLES_ADRESSES
 * à chaque modification d'un « rewrite »), ou quand elles ont disparu : règles enregistrées pendant
 * que le thème n'était pas chargé (changement de thème, mise à jour…) → /agenda/ en 404.
 * Vérification gratuite : l'option rewrite_rules est déjà chargée en mémoire.
 */
const SV_REGLES_ADRESSES = '3';
add_action(
	'init',
	function () {
		$regles  = get_option( 'rewrite_rules' );
		$perdues = is_array( $regles ) && get_option( 'permalink_structure' ) && ! isset( $regles['agenda/pole/([^/]+)/?$'] );
		if ( $perdues || get_option( 'sv_regles_adresses' ) !== SV_REGLES_ADRESSES ) {
			flush_rewrite_rules( false );
			update_option( 'sv_regles_adresses', SV_REGLES_ADRESSES );
		}
	},
	99
);

/**
 * Source de liaison (Block Bindings) : affiche les informations d'un événement
 * mises en forme (jour, mois, date complète, horaires, lieu) dans n'importe quel
 * bloc Paragraphe, y compris à l'intérieur d'une boucle de requête.
 */
add_action(
	'init',
	function () {
		if ( ! function_exists( 'register_block_bindings_source' ) ) {
			return;
		}
		register_block_bindings_source(
			'savoirs-vivants/evenement',
			[
				'label'              => __( 'Événement (Savoirs Vivants)', 'savoirs-vivants' ),
				'get_value_callback' => 'sv_valeur_evenement',
				'uses_context'       => [ 'postId' ],
			]
		);
	}
);

function sv_heure( string $heure ): string {
	if ( '' === $heure ) {
		return '';
	}
	$parties = explode( ':', $heure );
	if ( count( $parties ) < 2 ) {
		return $heure;
	}
	/* translators: heure d'un événement. 1 : heures, 2 : minutes (français « 10 h 50 », allemand « %1$s:%2$s Uhr »). */
	return sprintf( _x( '%1$s h %2$s', 'heure d’un événement', 'savoirs-vivants' ), $parties[0], $parties[1] );
}

/**
 * Dates d'un événement : [ début, fin ] au format AAAA-MM-JJ.
 * La fin est vide pour un événement d'une seule journée, ou si elle n'est pas après le début.
 *
 * @return array{0:string, 1:string}
 */
function sv_evenement_dates( int $id ): array {
	$debut = (string) get_post_meta( $id, 'sv_date', true );
	$fin   = (string) get_post_meta( $id, 'sv_date_fin', true );
	if ( '' === $debut || ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $fin ) || $fin <= $debut ) {
		$fin = '';
	}
	return [ $debut, $fin ];
}

function sv_valeur_evenement( array $args, $bloc ): string {
	$id                  = (int) ( $bloc->context['postId'] ?? get_the_ID() );
	[ $date, $fin_date ] = sv_evenement_dates( $id );
	$ts                  = $date ? strtotime( $date . ' 12:00:00' ) : false;
	$ts_fin              = $fin_date ? strtotime( $fin_date . ' 12:00:00' ) : false;
	$meme_mois           = $ts_fin && wp_date( 'Y-m', $ts ) === wp_date( 'Y-m', $ts_fin );

	$debut = sv_heure( (string) get_post_meta( $id, 'sv_heure_debut', true ) );
	$fin   = sv_heure( (string) get_post_meta( $id, 'sv_heure_fin', true ) );
	$lieu  = (string) get_post_meta( $id, 'sv_lieu', true );

	$horaires = '';
	if ( $debut && $fin ) {
		$horaires = sprintf(
			/* translators: horaires d'un événement. 1 : heure de début, 2 : heure de fin. */
			__( 'De %1$s à %2$s', 'savoirs-vivants' ),
			$debut,
			$fin
		);
	} elseif ( $debut ) {
		$horaires = sprintf(
			/* translators: %s : heure de début d'un événement sans heure de fin. */
			__( 'À partir de %s', 'savoirs-vivants' ),
			$debut
		);
	}

	switch ( $args['key'] ?? '' ) {
		case 'jour':
			// Plusieurs jours : « 26–28 » (pastille de l'agenda).
			$valeur = $ts ? wp_date( 'j', $ts ) . ( $ts_fin ? '–' . wp_date( 'j', $ts_fin ) : '' ) : '';
			break;
		case 'mois':
			// Plusieurs jours sur deux mois : « nov. – déc. ».
			if ( $ts_fin && ! $meme_mois ) {
				$valeur = wp_date( 'M', $ts ) . ' – ' . wp_date( 'M', $ts_fin );
			} else {
				$valeur = $ts ? wp_date( 'F', $ts ) : '';
			}
			break;
		case 'date':
			/* translators: format de la date complète d'un événement (voir https://www.php.net/manual/fr/datetime.format.php ; allemand : « l, j. F Y »). */
			$format = _x( 'l j F Y', 'format de date d’un événement', 'savoirs-vivants' );
			if ( ! $ts ) {
				$valeur = __( 'Date à venir', 'savoirs-vivants' );
			} elseif ( $ts_fin ) {
				if ( $meme_mois ) {
					/* translators: premier jour d'un événement de plusieurs jours dans le même mois (« jeudi 26 ») ; allemand : « l, j. ». */
					$format_debut = _x( 'l j', 'premier jour, même mois', 'savoirs-vivants' );
				} elseif ( wp_date( 'Y', $ts ) === wp_date( 'Y', $ts_fin ) ) {
					/* translators: premier jour d'un événement de plusieurs jours sur deux mois (« samedi 28 novembre ») ; allemand : « l, j. F ». */
					$format_debut = _x( 'l j F', 'premier jour, même année', 'savoirs-vivants' );
				} else {
					$format_debut = $format;
				}
				$valeur = sprintf(
					/* translators: dates d'un événement de plusieurs jours. 1 : premier jour, 2 : dernier jour (« Du jeudi 26 au samedi 28 novembre 2026 »). */
					__( 'Du %1$s au %2$s', 'savoirs-vivants' ),
					wp_date( $format_debut, $ts ),
					wp_date( $format, $ts_fin )
				);
			} else {
				$valeur = wp_date( $format, $ts );
			}
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
 * Émojis : si la connexion à la base est en utf8 sur 3 octets (DB_CHARSET « utf8 » dans wp-config.php),
 * un caractère sur 4 octets (🤖) devient « ? », même dans une colonne utf8mb4. WordPress ne les encode en
 * entités HTML (&#x1F916;) que d'après le jeu de caractères de la colonne : on le fait donc à chaque
 * enregistrement tant que connexion et colonne ne sont pas toutes deux en utf8mb4.
 */
add_filter(
	'wp_insert_post_data',
	function ( array $data ): array {
		global $wpdb;
		if ( 'utf8mb4' === $wpdb->charset && 'utf8mb4' === $wpdb->get_col_charset( $wpdb->posts, 'post_content' ) ) {
			return $data;
		}
		foreach ( [ 'post_title', 'post_content', 'post_excerpt' ] as $champ ) {
			if ( isset( $data[ $champ ] ) && is_string( $data[ $champ ] ) ) {
				$data[ $champ ] = wp_encode_emoji( $data[ $champ ] );
			}
		}
		return $data;
	},
	20
);

/**
 * Vignette d'un événement dans les listes : l'image mise en avant, insérée juste après la pastille
 * de date (groupe sv-date) de toutes les compositions d'agenda, y compris leurs copies en base.
 * Rien sans image. Alternative vide (RGAA 1.2) : illustration, le titre de la carte porte l'information.
 */
add_filter(
	'render_block_core/group',
	function ( string $html, array $bloc ): string {
		$classes = (string) ( $bloc['attrs']['className'] ?? '' );
		if ( ! in_array( 'sv-date', explode( ' ', $classes ), true ) || 'sv_evenement' !== get_post_type() || ! has_post_thumbnail() ) {
			return $html;
		}
		return $html . get_the_post_thumbnail(
			null,
			'medium',
			[
				'class'   => 'sv-evenement__vignette',
				'alt'     => '',
				'sizes'   => '6rem',
				'loading' => 'lazy',
			]
		);
	},
	10,
	2
);

/**
 * Aperçu de la carte d'un événement dans l'éditeur (assets/js/editeur-champs.js, fenêtre « Aperçu de la carte »).
 * Le panneau envoie en POST, dans une iframe, les valeurs en cours de saisie (même non enregistrées) ;
 * cette page les rend avec la vraie carte de la composition agenda-liste et les styles du site.
 * Réservé aux personnes qui peuvent modifier l'événement (jeton + droit edit_post).
 */
function sv_apercu_carte_valeurs( int $id ): array {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- jeton vérifié dans sv_apercu_carte().
	$texte = static function ( string $cle ): string {
		return isset( $_POST[ $cle ] ) ? sanitize_text_field( wp_unslash( $_POST[ $cle ] ) ) : '';
	};
	$motif = static function ( string $cle, string $regex ) use ( $texte ): string {
		$valeur = $texte( $cle );
		return preg_match( $regex, $valeur ) ? $valeur : '';
	};
	$poles = isset( $_POST['poles'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['poles'] ) ) : [];
	// phpcs:enable WordPress.Security.NonceVerification.Missing

	return [
		'titre' => $texte( 'titre' ),
		'image' => absint( $texte( 'image' ) ),
		'poles' => array_filter( $poles ),
		'meta'  => [
			'sv_date'        => $motif( 'sv_date', '/^\d{4}-\d{2}-\d{2}$/' ),
			'sv_date_fin'    => $motif( 'sv_date_fin', '/^\d{4}-\d{2}-\d{2}$/' ),
			'sv_heure_debut' => $motif( 'sv_heure_debut', '/^\d{2}:\d{2}$/' ),
			'sv_heure_fin'   => $motif( 'sv_heure_fin', '/^\d{2}:\d{2}$/' ),
			'sv_lieu'        => $texte( 'sv_lieu' ),
		],
	];
}

/**
 * Carte de l'agenda général (premier modèle de « Modèle de publication » de la composition agenda-liste).
 */
function sv_apercu_carte_modele(): ?array {
	$composition = WP_Block_Patterns_Registry::get_instance()->get_registered( 'savoirs-vivants/agenda-liste' );
	$chercher    = static function ( array $blocs ) use ( &$chercher ): ?array {
		foreach ( $blocs as $bloc ) {
			if ( 'core/post-template' === $bloc['blockName'] && ! empty( $bloc['innerBlocks'][0] ) ) {
				return $bloc['innerBlocks'][0];
			}
			$trouve = $chercher( $bloc['innerBlocks'] ?? [] );
			if ( $trouve ) {
				return $trouve;
			}
		}
		return null;
	};
	return $composition ? $chercher( parse_blocks( $composition['content'] ) ) : null;
}

function sv_apercu_carte(): void {
	$id = isset( $_POST['sv_apercu_carte'] ) ? absint( $_POST['sv_apercu_carte'] ) : 0;
	if ( ! $id || 'sv_evenement' !== get_post_type( $id ) || ! current_user_can( 'edit_post', $id )
		|| ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['_wpnonce'] ), 'sv-apercu-carte' ) ) {
		wp_die( esc_html__( 'Aperçu indisponible : rechargez l’éditeur.', 'savoirs-vivants' ), '', [ 'response' => 403 ] );
	}
	$valeurs = sv_apercu_carte_valeurs( $id );
	$modele  = sv_apercu_carte_modele();

	// Valeurs saisies à la place des valeurs enregistrées, pour cet événement seulement.
	add_filter(
		'get_post_metadata',
		static function ( $valeur, $objet, $cle ) use ( $id, $valeurs ) {
			if ( (int) $objet !== $id ) {
				return $valeur;
			}
			if ( '_thumbnail_id' === $cle ) {
				return [ (string) $valeurs['image'] ];
			}
			return array_key_exists( $cle, $valeurs['meta'] ) ? [ $valeurs['meta'][ $cle ] ] : $valeur;
		},
		10,
		3
	);
	add_filter(
		'the_title',
		static function ( $titre, $objet = 0 ) use ( $id, $valeurs ) {
			return (int) $objet === $id ? esc_html( $valeurs['titre'] ) : $titre;
		},
		10,
		2
	);
	add_filter(
		'get_the_terms',
		static function ( $termes, $objet, $taxonomie ) use ( $id, $valeurs ) {
			if ( (int) $objet !== $id || 'sv_pole' !== $taxonomie ) {
				return $termes;
			}
			$choisis = array_filter( array_map( static fn( $t ) => get_term( $t, 'sv_pole' ), $valeurs['poles'] ), static fn( $t ) => $t instanceof WP_Term );
			return $choisis ? array_values( $choisis ) : false;
		},
		10,
		3
	);

	// Page nue : ni barre d'administration, ni bannière de cookies ; styles de tous les blocs chargés d'emblée.
	show_admin_bar( false );
	add_filter( 'should_load_separate_core_block_assets', '__return_false' );
	add_action(
		'wp_enqueue_scripts',
		static function () {
			wp_dequeue_script( 'tarteaucitron' );
		},
		20
	);

	global $post;
	$post = get_post( $id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- contexte de la carte (vignette, liaisons).
	setup_postdata( $post );
	$carte = $modele ? ( new WP_Block(
		$modele,
		[
			'postId'   => $id,
			'postType' => 'sv_evenement',
		]
	) )->render() : '';

	nocache_headers();
	?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
	<?php wp_head(); ?>
<style>html{margin-top:0!important}#wpadminbar{display:none!important}body{margin:0;background:var(--color-blanc,#fff)}.sv-apercu{max-width:60rem;margin:0 auto;padding:1rem}.sv-apercu a{pointer-events:none}</style>
</head>
<body class="sv-apercu-carte">
<main class="sv-apercu"><?php echo $carte; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendu des blocs. ?></main>
	<?php wp_footer(); ?>
<script>(function(){function h(){parent.postMessage({svApercuHauteur:Math.ceil(document.body.getBoundingClientRect().height)},"*");}new ResizeObserver(h).observe(document.body);h();})();</script>
</body>
</html>
	<?php
	exit;
}

add_action(
	'template_redirect',
	function () {
		if ( isset( $_POST['sv_apercu_carte'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- vérifié dans sv_apercu_carte().
			sv_apercu_carte();
		}
	},
	0
);

// Adresse et jeton de l'aperçu, textes du choix d'image et de la fenêtre (assets/js/editeur-champs.js).
add_action(
	'enqueue_block_editor_assets',
	function () {
		if ( ! wp_script_is( 'sv-editeur-champs', 'enqueued' ) ) {
			return;
		}
		$config = [
			'url'    => home_url( '/' ),
			'nonce'  => wp_create_nonce( 'sv-apercu-carte' ),
			'textes' => [
				'image'          => __( 'Image d’illustration', 'savoirs-vivants' ),
				'image_aide'     => __( 'Affichée à côté de la date dans les listes de l’agenda et en haut de la page de l’événement. Une affiche convient : le haut de l’image est conservé.', 'savoirs-vivants' ),
				'choisir'        => __( 'Choisir une image', 'savoirs-vivants' ),
				'remplacer'      => __( 'Remplacer', 'savoirs-vivants' ),
				'retirer'        => __( 'Retirer', 'savoirs-vivants' ),
				'apercu'         => __( 'Aperçu de la carte', 'savoirs-vivants' ),
				'apercu_aide'    => __( 'Carte telle qu’elle apparaîtra dans l’agenda, avec les informations en cours de saisie.', 'savoirs-vivants' ),
				'ordinateur'     => __( 'Ordinateur', 'savoirs-vivants' ),
				'mobile'         => __( 'Mobile', 'savoirs-vivants' ),
				'titre_iframe'   => __( 'Aperçu de la carte de l’événement', 'savoirs-vivants' ),
				'titre_manquant' => __( 'Titre de l’événement', 'savoirs-vivants' ),
			],
		];
		wp_add_inline_script( 'sv-editeur-champs', 'window.svApercuCarte = ' . wp_json_encode( $config ) . ';', 'before' );
	},
	20
);

/**
 * Agenda : n'afficher que les événements à venir ou en cours (plusieurs jours : jusqu'au dernier),
 * du plus proche au plus lointain (tri sur le premier jour).
 * S'applique aux blocs « Boucle de requête » et à la page /agenda/.
 * Groupe « OU » imbriqué : les critères ajoutés ensuite (filtres de l'agenda) restent en « ET ».
 */
function sv_requete_evenements_a_venir( array $vars ): array {
	$aujourdhui         = current_time( 'Y-m-d' );
	$vars['meta_key']   = 'sv_date';
	$vars['orderby']    = 'meta_value';
	$vars['order']      = 'ASC';
	$vars['meta_query'] = [
		[
			'relation' => 'OR',
			[
				'key'     => 'sv_date',
				'value'   => $aujourdhui,
				'compare' => '>=',
				'type'    => 'DATE',
			],
			[
				'key'     => 'sv_date_fin',
				'value'   => $aujourdhui,
				'compare' => '>=',
				'type'    => 'DATE',
			],
		],
	];
	return $vars;
}

add_filter(
	'query_loop_block_query_vars',
	function ( $vars ) {
		$type = $vars['post_type'] ?? '';
		if ( 'sv_evenement' === $type || ( is_array( $type ) && in_array( 'sv_evenement', $type, true ) ) ) {
			$vars = sv_requete_evenements_a_venir( $vars );
		}
		return $vars;
	}
);

add_action(
	'pre_get_posts',
	function ( $query ) {
		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}
		if ( $query->is_post_type_archive( 'sv_evenement' ) || $query->is_tax( 'sv_pole' ) ) {
			foreach ( sv_requete_evenements_a_venir( [] ) as $cle => $valeur ) {
				$query->set( $cle, $valeur );
			}
		}
	}
);
