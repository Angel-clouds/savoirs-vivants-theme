<?php
/**
 * Agenda général (page « Agenda », /agenda/) : filtres par agenda (pôles) et par mois.
 *
 * Code court [sv_filtres_agenda] (composition agenda-liste) : formulaire GET classique, sans JavaScript,
 * puis titre « N rendez-vous à venir » qui annonce le résultat. Les filtres restreignent la boucle
 * de requête dont la requête porte "svFiltres":true (même composition).
 * Adresse partageable : /agenda/?agendas[]=creabot&agendas[]=schlouk-de-sciences&mois=2026-11
 *
 * Accessibilité : cases groupées dans un fieldset avec légende (RGAA 11.5, 11.6), étiquettes liées
 * (11.1), bouton d'envoi explicite (11.9), le formulaire renvoie sur le titre des résultats (#resultats).
 *
 * @package savoirs-vivants
 */

defined( 'ABSPATH' ) || exit;

/**
 * Agendas cochés (slugs de termes sv_pole existants uniquement).
 */
function sv_filtres_agendas_choisis(): array {
	// Lecture seule d'un filtre d'affichage public : pas de jeton (nonce) nécessaire.
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$demandes = isset( $_GET['agendas'] ) ? array_map( 'sanitize_title', (array) wp_unslash( $_GET['agendas'] ) ) : [];
	if ( ! $demandes ) {
		return [];
	}
	$existants = get_terms(
		[
			'taxonomy'   => 'sv_pole',
			'hide_empty' => false,
			'fields'     => 'id=>slug',
		]
	);
	return is_array( $existants ) ? array_values( array_intersect( $existants, $demandes ) ) : [];
}

/**
 * Mois choisi (AAAA-MM) ou chaîne vide.
 */
function sv_filtres_mois_choisi(): string {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$mois = isset( $_GET['mois'] ) ? sanitize_text_field( wp_unslash( $_GET['mois'] ) ) : '';
	return preg_match( '/^\d{4}-(0[1-9]|1[0-2])$/', $mois ) ? $mois : '';
}

/**
 * Mois qui comptent au moins un événement publié à venir ou en cours (pour la liste déroulante).
 * Un événement de plusieurs jours compte dans chaque mois qu'il couvre (à partir du mois en cours).
 */
function sv_filtres_mois_disponibles(): array {
	global $wpdb;
	$aujourdhui = current_time( 'Y-m-d' );
	// Requête en lecture seule, uniquement sur la page Agenda : pas de cache nécessaire.
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$lignes = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT d.meta_value AS debut, f.meta_value AS fin FROM {$wpdb->postmeta} d
			INNER JOIN {$wpdb->posts} p ON p.ID = d.post_id
			LEFT JOIN {$wpdb->postmeta} f ON f.post_id = d.post_id AND f.meta_key = 'sv_date_fin'
			WHERE d.meta_key = 'sv_date' AND d.meta_value <> ''
			AND ( d.meta_value >= %s OR f.meta_value >= %s )
			AND p.post_type = 'sv_evenement' AND p.post_status = 'publish'",
			$aujourdhui,
			$aujourdhui
		)
	);

	$mois = [];
	foreach ( $lignes as $ligne ) {
		$debut = substr( max( (string) $ligne->debut, $aujourdhui ), 0, 7 );
		$fin   = substr( max( (string) $ligne->debut, (string) $ligne->fin ), 0, 7 );
		// Mois par mois, du premier au dernier (borné à 24 mois par sécurité).
		for ( $i = 0, $courant = $debut; $courant <= $fin && $i < 24; $i++ ) {
			$mois[ $courant ] = true;
			$courant          = gmdate( 'Y-m', strtotime( $courant . '-01 +1 month' ) );
		}
	}
	ksort( $mois );
	return array_keys( $mois );
}

/**
 * Libellé d'un mois (« novembre 2026 »).
 */
function sv_filtres_libelle_mois( string $mois ): string {
	return wp_date( 'F Y', strtotime( $mois . '-01 12:00:00' ) );
}

/**
 * Critères des filtres, ajoutés à une requête d'événements (déjà limitée aux événements à venir).
 */
function sv_filtres_appliquer( array $vars ): array {
	$agendas = sv_filtres_agendas_choisis();
	if ( $agendas ) {
		$vars['tax_query'] = [
			[
				'taxonomy' => 'sv_pole',
				'field'    => 'slug',
				'terms'    => $agendas,
			],
		];
	}
	$mois = sv_filtres_mois_choisi();
	if ( $mois ) {
		// Événement qui touche le mois : commence avant sa fin ET (commence ou finit après son début).
		$premier              = $mois . '-01';
		$vars['meta_query']   = $vars['meta_query'] ?? [];
		$vars['meta_query'][] = [
			'key'     => 'sv_date',
			// Dernier jour réel du mois (« 2027-02-31 » serait une date invalide pour MySQL).
			'value'   => gmdate( 'Y-m-t', strtotime( $premier ) ),
			'compare' => '<=',
			'type'    => 'DATE',
		];
		$vars['meta_query'][] = [
			'relation' => 'OR',
			[
				'key'     => 'sv_date',
				'value'   => $premier,
				'compare' => '>=',
				'type'    => 'DATE',
			],
			[
				'key'     => 'sv_date_fin',
				'value'   => $premier,
				'compare' => '>=',
				'type'    => 'DATE',
			],
		];
	}
	return $vars;
}

/**
 * Boucle filtrable (requête "svFiltres":true) : priorité 20, après le filtre « à venir » de contenus.php.
 */
add_filter(
	'query_loop_block_query_vars',
	function ( $vars, $bloc ) {
		if ( empty( $bloc->context['query']['svFiltres'] ) ) {
			return $vars;
		}
		return sv_filtres_appliquer( $vars );
	},
	20,
	2
);

add_shortcode(
	'sv_filtres_agenda',
	function () {
		$termes = get_terms(
			[
				'taxonomy'   => 'sv_pole',
				'hide_empty' => false,
				'orderby'    => 'term_id',
			]
		);
		if ( ! is_array( $termes ) ) {
			$termes = [];
		}
		$agendas = sv_filtres_agendas_choisis();
		$mois    = sv_filtres_mois_choisi();
		$action  = is_singular() ? get_permalink() : home_url( '/agenda/' );

		// Nombre de résultats, avec les mêmes critères que la boucle (requête légère : identifiants seulement).
		$compte = new WP_Query(
			sv_filtres_appliquer(
				sv_requete_evenements_a_venir(
					[
						'post_type'      => 'sv_evenement',
						'post_status'    => 'publish',
						'posts_per_page' => 1,
						'fields'         => 'ids',
					]
				)
			)
		);
		$total  = (int) $compte->found_posts;

		// Résumé des filtres actifs, repris dans le titre des résultats.
		$noms = [];
		foreach ( $termes as $terme ) {
			if ( in_array( $terme->slug, $agendas, true ) ) {
				$noms[] = $terme->name;
			}
		}
		$precisions = '';
		if ( $noms ) {
			/* translators: %s : noms des agendas choisis, séparés par des virgules. */
			$precisions .= ' ' . sprintf( __( 'pour %s', 'savoirs-vivants' ), implode( ', ', $noms ) );
		}
		if ( $mois ) {
			/* translators: %s : mois choisi (ex. « novembre 2026 »). */
			$precisions .= ' ' . sprintf( __( 'en %s', 'savoirs-vivants' ), sv_filtres_libelle_mois( $mois ) );
		}
		if ( $total ) {
			/* translators: %d : nombre d'événements à venir. */
			$titre = sprintf( _n( '%d rendez-vous à venir', '%d rendez-vous à venir', $total, 'savoirs-vivants' ), $total );
		} else {
			$titre = __( 'Aucun rendez-vous à venir', 'savoirs-vivants' );
		}

		ob_start();
		?>
<form class="sv-filtres-agenda" method="get" action="<?php echo esc_url( $action . '#resultats' ); ?>">
	<fieldset class="sv-filtres-agenda__agendas">
		<legend><?php esc_html_e( 'Agendas', 'savoirs-vivants' ); ?></legend>
		<div class="sv-filtres-agenda__choix">
		<?php foreach ( $termes as $terme ) : ?>
			<label><input type="checkbox" name="agendas[]" value="<?php echo esc_attr( $terme->slug ); ?>"<?php checked( in_array( $terme->slug, $agendas, true ) ); ?>> <?php echo esc_html( $terme->name ); ?></label>
		<?php endforeach; ?>
		</div>
		<p class="sv-filtres-agenda__aide"><?php esc_html_e( 'Aucune case cochée : tous les agendas.', 'savoirs-vivants' ); ?></p>
	</fieldset>
	<p class="sv-filtres-agenda__mois">
		<label for="sv-filtre-mois"><?php esc_html_e( 'Mois', 'savoirs-vivants' ); ?></label>
		<select id="sv-filtre-mois" name="mois">
			<option value=""><?php esc_html_e( 'Tous les mois', 'savoirs-vivants' ); ?></option>
			<?php foreach ( sv_filtres_mois_disponibles() as $valeur ) : ?>
			<option value="<?php echo esc_attr( $valeur ); ?>"<?php selected( $valeur, $mois ); ?>><?php echo esc_html( sv_filtres_libelle_mois( $valeur ) ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<p class="sv-filtres-agenda__actions">
		<button type="submit" class="wp-element-button"><?php esc_html_e( 'Afficher les rendez-vous', 'savoirs-vivants' ); ?></button>
		<?php if ( $agendas || $mois ) : ?>
		<a href="<?php echo esc_url( $action ); ?>"><?php esc_html_e( 'Effacer les filtres', 'savoirs-vivants' ); ?></a>
		<?php endif; ?>
	</p>
</form>
<h2 id="resultats" class="sv-filtres-agenda__resultats" tabindex="-1"><?php echo esc_html( $titre . $precisions ); ?></h2>
		<?php
		return ob_get_clean();
	}
);
