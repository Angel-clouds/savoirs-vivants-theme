<?php
/**
 * Styles de blocs et catégories de compositions.
 * Les styles apparaissent dans l'éditeur (panneau « Styles » du bloc) :
 * l'équipe les applique en un clic, sans toucher au code.
 */
defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	function () {
		/*
		* Catégories de compositions (inserteur « + » → Compositions).
		* Chaque section de chaque page est une composition autonome, réutilisable partout.
		*/
		$categories = [
			'savoirs-vivants'       => [ __( 'SV · Composants génériques', 'savoirs-vivants' ), __( 'Sections vierges à remplir, conformes à la charte et au RGAA.', 'savoirs-vivants' ) ],
			'savoirs-vivants-pages' => [ __( 'SV · Pages complètes', 'savoirs-vivants' ), __( 'Pages entières construites d’après les maquettes.', 'savoirs-vivants' ) ],
			'sv-accueil'            => [ __( 'SV · Accueil', 'savoirs-vivants' ), __( 'Sections de la page d’accueil.', 'savoirs-vivants' ) ],
			'sv-nos-projets'        => [ __( 'SV · Nos projets', 'savoirs-vivants' ), __( 'Sections de la page Nos projets.', 'savoirs-vivants' ) ],
			'sv-creabot'            => [ __( 'SV · CréaBot', 'savoirs-vivants' ), __( 'Sections du pôle robotique éducative.', 'savoirs-vivants' ) ],
			'sv-schlouk'            => [ __( 'SV · Schlouk de Sciences', 'savoirs-vivants' ), __( 'Sections du pôle médiation scientifique.', 'savoirs-vivants' ) ],
			'sv-sciences'           => [ __( 'SV · Sciences participatives', 'savoirs-vivants' ), __( 'Sections du pôle recherche participative.', 'savoirs-vivants' ) ],
			'sv-ressourcerie'       => [ __( 'SV · Ressourcerie', 'savoirs-vivants' ), __( 'Sections de la Ressourcerie.', 'savoirs-vivants' ) ],
			'sv-outils'             => [ __( 'SV · Outils numériques', 'savoirs-vivants' ), __( 'Sections de la page USUEL.', 'savoirs-vivants' ) ],
			'sv-a-propos'           => [ __( 'SV · À propos', 'savoirs-vivants' ), __( 'Sections de la page À propos.', 'savoirs-vivants' ) ],
			'sv-contact'            => [ __( 'SV · Contact', 'savoirs-vivants' ), __( 'Sections de la page Contact.', 'savoirs-vivants' ) ],
		];
		foreach ( $categories as $slug => [ $label, $description ] ) {
			register_block_pattern_category(
				$slug,
				[
					'label'       => $label,
					'description' => $description,
				]
			);
		}

		register_block_style(
			'core/paragraph',
			[
				'name'  => 'surtitre',
				'label' => __( 'Surtitre', 'savoirs-vivants' ),
			]
		);
		register_block_style(
			'core/heading',
			[
				'name'  => 'titre-carte',
				'label' => __( 'Titre de carte', 'savoirs-vivants' ),
			]
		);
		register_block_style(
			'core/group',
			[
				'name'  => 'carte',
				'label' => __( 'Carte', 'savoirs-vivants' ),
			]
		);
		register_block_style(
			'core/group',
			[
				'name'  => 'carte-image',
				'label' => __( 'Carte avec image', 'savoirs-vivants' ),
			]
		);
		register_block_style(
			'core/details',
			[
				'name'  => 'faq',
				'label' => __( 'Question fréquente', 'savoirs-vivants' ),
			]
		);

		/*
		 * Styles tirés de la charte graphique 2023 (voir src/css/main.css, section 6).
		 * - Sous-titre : « Sous-titre 1 » de la charte, Space Mono gras italique vert sauge, placé SOUS le titre.
		 * - Étiquette : donnée courte encadrée (date, pôle, statut), comme les encadrés de la charte.
		 * - Filet : filet vertical bleu nuit à gauche, à la place d'une carte encadrée.
		 * - Mise en avant : grille dont le premier élément occupe la colonne large (7/12).
		 */
		$styles = [
			[ 'core/paragraph', 'sous-titre', __( 'Sous-titre', 'savoirs-vivants' ) ],
			[ 'core/paragraph', 'etiquette', __( 'Étiquette', 'savoirs-vivants' ) ],
			[ 'core/post-terms', 'etiquette', __( 'Étiquette', 'savoirs-vivants' ) ],
			[ 'core/group', 'filet', __( 'Filet', 'savoirs-vivants' ) ],
			[ 'core/group', 'mise-en-avant', __( 'Mise en avant', 'savoirs-vivants' ) ],
		];
		foreach ( $styles as [ $bloc, $nom, $libelle ] ) {
			register_block_style(
				$bloc,
				[
					'name'  => $nom,
					'label' => $libelle,
				]
			);
		}
	}
);
