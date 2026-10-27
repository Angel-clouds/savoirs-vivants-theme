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
			'sv-agenda'             => [ __( 'SV · Agenda', 'savoirs-vivants' ), __( 'Sections de la page Agenda.', 'savoirs-vivants' ) ],
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

/**
 * Le contenu porte-t-il déjà un H1 ? Les compositions insérées par référence
 * (<!-- wp:pattern {"slug":…} /-->, page « Nos projets ») sont lues dans le registre,
 * car leur H1 n'apparaît pas dans post_content.
 *
 * @param string $contenu    Contenu sérialisé (blocs).
 * @param int    $profondeur Garde-fou contre une composition qui s'inclurait elle-même.
 */
function sv_contenu_a_un_h1( string $contenu, int $profondeur = 0 ): bool {
	if ( preg_match( '/<h1[\s>]/i', $contenu ) ) {
		return true;
	}
	if ( $profondeur > 3 || ! preg_match_all( '/<!-- wp:pattern \{"slug":"([^"]+)"/', $contenu, $refs ) ) {
		return false;
	}
	$registre = WP_Block_Patterns_Registry::get_instance();
	foreach ( $refs[1] as $slug ) {
		$composition = $registre->get_registered( $slug );
		if ( $composition && sv_contenu_a_un_h1( (string) $composition['content'], $profondeur + 1 ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Un seul H1 par page (RGAA 9.1) : le titre H1 du modèle « Page » s'efface quand le contenu
 * en porte déjà un (pages complètes et héros des compositions). L'équipe n'a donc plus à
 * penser au modèle « Page sans titre ». Les titres de niveau 2 et 3 (listes, cartes) ne sont pas visés.
 *
 * @param string   $html  Rendu du bloc Titre.
 * @param array    $bloc  Bloc analysé.
 * @param WP_Block $objet Instance du bloc (contexte postId).
 */
function sv_titre_page_sans_doublon_h1( string $html, array $bloc, WP_Block $objet ): string {
	if ( 1 !== ( $bloc['attrs']['level'] ?? 2 ) || ! is_singular( 'page' ) ) {
		return $html;
	}
	$id = (int) ( $objet->context['postId'] ?? get_the_ID() );
	if ( get_queried_object_id() !== $id ) {
		return $html;
	}
	return sv_contenu_a_un_h1( (string) get_post_field( 'post_content', $id ) ) ? '' : $html;
}
add_filter( 'render_block_core/post-title', 'sv_titre_page_sans_doublon_h1', 10, 3 );

/**
 * Menu de l'en-tête : lien de la page en cours marqué aria-current="page", lien de la rubrique
 * en cours (sous-page, événement de /agenda/…) marqué aria-current="true". WordPress ne le fait
 * que pour les liens vers un contenu par identifiant ; ceux de parts/header.html sont « custom ».
 * Repère attendu par les lecteurs d'écran (WCAG 2.4.8), rendu visible par la section 4 du CSS.
 *
 * @param string $html Rendu du lien de menu.
 * @param array  $bloc Bloc analysé.
 */
function sv_menu_lien_en_cours( string $html, array $bloc ): string {
	$cible = (string) wp_parse_url( (string) ( $bloc['attrs']['url'] ?? '' ), PHP_URL_PATH );
	if ( '' === $cible ) {
		return $html;
	}
	$cible  = trailingslashit( $cible );
	$actuel = trailingslashit( (string) wp_parse_url( add_query_arg( [] ), PHP_URL_PATH ) );
	if ( '/' === $cible || ! str_starts_with( $actuel, $cible ) ) {
		return $html;
	}
	$lien = new WP_HTML_Tag_Processor( $html );
	if ( $lien->next_tag( 'a' ) ) {
		$lien->set_attribute( 'aria-current', $actuel === $cible ? 'page' : 'true' );
	}
	return $lien->get_updated_html();
}
add_filter( 'render_block_core/navigation-link', 'sv_menu_lien_en_cours', 10, 2 );

/**
 * Nom accessible du menu (liste des régions des lecteurs d'écran) : le bloc Navigation à liens
 * écrits dans le modèle n'en reçoit aucun. Traduisible ici, contrairement à parts/header.html.
 *
 * @param string $html Rendu du bloc Navigation.
 */
function sv_menu_nom_accessible( string $html ): string {
	$nav = new WP_HTML_Tag_Processor( $html );
	if ( $nav->next_tag( 'nav' ) && null === $nav->get_attribute( 'aria-label' ) ) {
		$nav->set_attribute( 'aria-label', __( 'Menu principal', 'savoirs-vivants' ) );
	}
	return $nav->get_updated_html();
}
add_filter( 'render_block_core/navigation', 'sv_menu_nom_accessible' );
