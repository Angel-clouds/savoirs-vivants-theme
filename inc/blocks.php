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
	}
);
