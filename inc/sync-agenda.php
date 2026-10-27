<?php
/**
 * Synchronisation Google Agenda → événements (sv_evenement).
 *
 * Toutes les heures (WP-Cron), le flux public .ics de l'agenda Google est lu et chaque
 * événement est créé ou mis à jour dans l'Agenda du site :
 * - clé d'identification : l'UID Google, enregistré dans la métadonnée cachée « _sv_gcal_uid » ;
 * - nouvel événement : créé en brouillon, une personne valide toujours avant publication ;
 * - événement connu : titre, description, date, heures et lieu écrasés par ceux de Google,
 *   seulement si Google les a modifiés (empreinte « _sv_gcal_empreinte »), sans changer son statut ;
 * - événement mis à la corbeille sur le site : jamais recréé (refus volontaire de l'équipe).
 *
 * Correspondance des champs (inc/contenus.php) :
 *   SUMMARY → titre, DESCRIPTION → contenu, LOCATION → sv_lieu,
 *   DTSTART → sv_date (Y-m-d) + sv_heure_debut (H:i), DTEND → sv_heure_fin (H:i, même jour seulement)
 *   ou sv_date_fin (Y-m-d, événement de plusieurs jours ; DTEND d'une journée entière = lendemain du dernier jour).
 *
 * Limites : les événements récurrents (RRULE) ne sont importés qu'à leur première date
 * (les occurrences modifiées une à une dans Google, RECURRENCE-ID, sont importées à part) ;
 * les événements passés et annulés ne sont pas importés.
 *
 * Lancer une synchronisation à la main : wp cron event run sv_sync_agenda
 */

defined( 'ABSPATH' ) || exit;

/*
 * Adresse publique du flux .ics (Google Agenda → Paramètres de l'agenda → « Adresse publique au format iCal »).
 * Le dépôt est public : définir de préférence SV_GCAL_ICS_URL dans wp-config.php, surtout s'il s'agit
 * de l'« adresse secrète » d'un agenda privé. Tant que l'adresse factice ci-dessous est en place,
 * la synchronisation ne fait rien.
 */
if ( ! defined( 'SV_GCAL_ICS_URL' ) ) {
	define( 'SV_GCAL_ICS_URL', 'https://calendar.google.com/calendar/ical/VOTRE_IDENTIFIANT%40group.calendar.google.com/public/basic.ics' );
}

/** Nom de la tâche planifiée. */
const SV_GCAL_CRON = 'sv_sync_agenda';

/* ---------- Planification (WP-Cron) ---------- */

/**
 * Tâche horaire programmée une seule fois (wp_next_scheduled évite les doublons).
 * Un thème n'a pas de crochet d'activation fiable : la vérification sur « init » est peu
 * coûteuse (l'option « cron » est déjà en mémoire) et reprogramme la tâche si elle a disparu.
 */
add_action(
	'init',
	function (): void {
		if ( ! wp_next_scheduled( SV_GCAL_CRON ) ) {
			wp_schedule_event( time(), 'hourly', SV_GCAL_CRON );
		}
	}
);

/**
 * Désactivation : un thème n'a pas de register_deactivation_hook(). « switch_theme » est
 * déclenché quand on change de thème, pendant que le code de l'ancien thème est encore chargé.
 */
add_action(
	'switch_theme',
	function (): void {
		wp_clear_scheduled_hook( SV_GCAL_CRON );
	}
);

add_action( SV_GCAL_CRON, 'sv_gcal_synchroniser' );

/* ---------- Moteur de synchronisation ---------- */

/**
 * Récupère le flux, puis crée ou met à jour chaque événement.
 *
 * @return array{crees:int, mis_a_jour:int, inchanges:int, ignores:int}|null Bilan, ou null si rien n'a été fait.
 */
function sv_gcal_synchroniser(): ?array {
	if ( '' === SV_GCAL_ICS_URL || str_contains( SV_GCAL_ICS_URL, 'VOTRE_IDENTIFIANT' ) ) {
		return null; // adresse non configurée
	}

	// Verrou : deux exécutions simultanées (cron lancé par deux visites) créeraient des doublons.
	if ( get_transient( 'sv_gcal_verrou' ) ) {
		return null;
	}
	set_transient( 'sv_gcal_verrou', 1, 10 * MINUTE_IN_SECONDS );

	try {
		$ics = sv_gcal_recuperer( SV_GCAL_ICS_URL );
		if ( null === $ics ) {
			return null;
		}

		$bilan = [
			'crees'      => 0,
			'mis_a_jour' => 0,
			'inchanges'  => 0,
			'ignores'    => 0,
		];
		foreach ( sv_gcal_parser( $ics ) as $evenement ) {
			$resultat = sv_gcal_enregistrer( $evenement );
			++$bilan[ $resultat ];
		}

		if ( $bilan['crees'] || $bilan['mis_a_jour'] ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- journal voulu (tâche planifiée sans écran).
			error_log( sprintf( '[Savoirs Vivants] Agenda Google : %d créé(s) en brouillon, %d mis à jour.', $bilan['crees'], $bilan['mis_a_jour'] ) );
		}
		return $bilan;
	} finally {
		delete_transient( 'sv_gcal_verrou' );
	}
}

/**
 * Télécharge le fichier .ics. Renvoie son contenu, ou null en cas d'échec (consigné dans le journal).
 */
function sv_gcal_recuperer( string $url ): ?string {
	$reponse = wp_remote_get(
		$url,
		[
			'timeout'             => 20,
			'limit_response_size' => 5 * MB_IN_BYTES, // garde-fou mémoire
		]
	);

	if ( is_wp_error( $reponse ) ) {
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- journal voulu (tâche planifiée sans écran).
		error_log( '[Savoirs Vivants] Agenda Google : échec de la requête (' . $reponse->get_error_message() . ').' );
		return null;
	}

	$code = (int) wp_remote_retrieve_response_code( $reponse );
	if ( 200 !== $code ) {
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- journal voulu (tâche planifiée sans écran).
		error_log( '[Savoirs Vivants] Agenda Google : réponse HTTP ' . $code . ' au lieu de 200.' );
		return null;
	}

	$corps = (string) wp_remote_retrieve_body( $reponse );
	if ( ! str_contains( $corps, 'BEGIN:VCALENDAR' ) ) {
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- journal voulu (tâche planifiée sans écran).
		error_log( '[Savoirs Vivants] Agenda Google : la réponse n\'est pas un fichier iCalendar.' );
		return null;
	}
	return $corps;
}

/* ---------- Lecture du format iCalendar (RFC 5545) ---------- */

/**
 * Extrait les blocs VEVENT du fichier .ics.
 *
 * @return list<array{uid:string, titre:string, description:string, lieu:string, date:string, heure_debut:string, heure_fin:string, date_fin?:string}>
 */
function sv_gcal_parser( string $ics ): array {
	// Lignes repliées : une ligne qui commence par une espace ou une tabulation prolonge la précédente.
	$ics    = preg_replace( "/\r?\n[ \t]/", '', $ics );
	$lignes = preg_split( "/\r?\n/", (string) $ics );

	$evenements = [];
	$courant    = null;
	$profondeur = 0; // blocs imbriqués dans un VEVENT (VALARM…) : leurs propriétés sont ignorées

	foreach ( $lignes as $ligne ) {
		if ( 'BEGIN:VEVENT' === $ligne ) {
			$courant    = [];
			$profondeur = 0;
			continue;
		}
		if ( null === $courant ) {
			continue;
		}
		if ( 'END:VEVENT' === $ligne ) {
			$evenement = sv_gcal_convertir( $courant );
			if ( $evenement ) {
				$evenements[] = $evenement;
			}
			$courant = null;
			continue;
		}
		if ( str_starts_with( $ligne, 'BEGIN:' ) ) {
			++$profondeur;
			continue;
		}
		if ( str_starts_with( $ligne, 'END:' ) ) {
			--$profondeur;
			continue;
		}
		if ( $profondeur > 0 ) {
			continue;
		}

		$propriete = sv_gcal_lire_ligne( $ligne );
		// Première occurrence conservée (une propriété ne devrait pas être répétée).
		if ( $propriete && ! isset( $courant[ $propriete['nom'] ] ) ) {
			$courant[ $propriete['nom'] ] = $propriete;
		}
	}

	return $evenements;
}

/**
 * Découpe une ligne « NOM;PARAM=valeur:contenu » (ex. DTSTART;TZID=Europe/Paris:20261020T180000).
 *
 * @return array{nom:string, params:array<string,string>, valeur:string}|null
 */
function sv_gcal_lire_ligne( string $ligne ): ?array {
	// Le premier « : » hors guillemets sépare le nom (et ses paramètres) de la valeur.
	if ( ! preg_match( '/^([A-Za-z0-9-]+)((?:;[^:;"]+=(?:"[^"]*"|[^:;"]*))*):(.*)$/', $ligne, $m ) ) {
		return null;
	}

	$params = [];
	if ( '' !== $m[2] ) {
		preg_match_all( '/;([^=;]+)=("[^"]*"|[^;]*)/', $m[2], $paires, PREG_SET_ORDER );
		foreach ( $paires as $paire ) {
			$params[ strtoupper( $paire[1] ) ] = trim( $paire[2], '"' );
		}
	}

	return [
		'nom'    => strtoupper( $m[1] ),
		'params' => $params,
		'valeur' => $m[3],
	];
}

/**
 * Texte iCalendar → texte brut : \n, \, \; et \\ sont des séquences d'échappement.
 */
function sv_gcal_texte( string $valeur ): string {
	return trim(
		strtr(
			$valeur,
			[
				'\\n'  => "\n",
				'\\N'  => "\n",
				'\\,'  => ',',
				'\\;'  => ';',
				'\\\\' => '\\',
			]
		)
	);
}

/**
 * Date iCalendar → DateTimeImmutable dans le fuseau du site (Réglages → Général).
 * Formats : 20261020 (journée entière), 20261020T180000Z (UTC),
 * 20261020T180000 avec TZID=Europe/Paris, ou sans fuseau (heure locale du site).
 *
 * @return array{0:DateTimeImmutable, 1:bool}|null Date, et vrai si l'événement dure toute la journée.
 */
function sv_gcal_date( array $propriete ): ?array {
	$valeur = trim( $propriete['valeur'] );
	$site   = wp_timezone();

	if ( preg_match( '/^\d{8}$/', $valeur ) ) {
		$date = DateTimeImmutable::createFromFormat( '!Ymd', $valeur, $site );
		return $date ? [ $date, true ] : null;
	}

	if ( ! preg_match( '/^(\d{8}T\d{6})(Z?)$/', $valeur, $m ) ) {
		return null;
	}

	$fuseau = $site;
	if ( 'Z' === $m[2] ) {
		$fuseau = new DateTimeZone( 'UTC' );
	} elseif ( ! empty( $propriete['params']['TZID'] ) ) {
		try {
			$fuseau = new DateTimeZone( $propriete['params']['TZID'] );
		} catch ( Exception $e ) {
			$fuseau = $site; // fuseau inconnu : heure considérée comme locale
		}
	}

	$date = DateTimeImmutable::createFromFormat( 'Ymd\THis', $m[1], $fuseau );
	return $date ? [ $date->setTimezone( $site ), false ] : null;
}

/**
 * Propriétés brutes d'un VEVENT → événement prêt à enregistrer, ou null s'il faut l'ignorer.
 *
 * @param array<string, array{nom:string, params:array<string,string>, valeur:string}> $proprietes
 */
function sv_gcal_convertir( array $proprietes ): ?array {
	$uid = trim( $proprietes['UID']['valeur'] ?? '' );
	if ( '' === $uid || empty( $proprietes['DTSTART'] ) ) {
		return null;
	}
	if ( 'CANCELLED' === strtoupper( trim( $proprietes['STATUS']['valeur'] ?? '' ) ) ) {
		return null;
	}

	$debut = sv_gcal_date( $proprietes['DTSTART'] );
	if ( ! $debut ) {
		return null;
	}
	[ $date_debut, $journee ] = $debut;

	// Occurrence modifiée d'un événement récurrent : même UID que la série, donc clé distincte.
	if ( ! empty( $proprietes['RECURRENCE-ID'] ) ) {
		$uid .= '|' . trim( $proprietes['RECURRENCE-ID']['valeur'] );
	}

	// Heure de fin : seulement pour un événement qui commence et finit le même jour.
	// Date de fin : pour un événement de plusieurs jours (DTEND d'une journée entière = lendemain, exclu).
	$heure_fin = '';
	$date_fin  = '';
	if ( ! empty( $proprietes['DTEND'] ) ) {
		$fin = sv_gcal_date( $proprietes['DTEND'] );
		if ( $fin ) {
			$dernier = $journee ? $fin[0]->modify( '-1 day' ) : $fin[0];
			if ( $dernier->format( 'Y-m-d' ) > $date_debut->format( 'Y-m-d' ) ) {
				$date_fin = $dernier->format( 'Y-m-d' );
			} elseif ( ! $journee && $fin[0]->format( 'Y-m-d' ) === $date_debut->format( 'Y-m-d' ) ) {
				$heure_fin = $fin[0]->format( 'H:i' );
			}
		}
	}

	$evenement = [
		'uid'         => $uid,
		'titre'       => sv_gcal_texte( $proprietes['SUMMARY']['valeur'] ?? '' ),
		'description' => sv_gcal_texte( $proprietes['DESCRIPTION']['valeur'] ?? '' ),
		'lieu'        => sv_gcal_texte( $proprietes['LOCATION']['valeur'] ?? '' ),
		'date'        => $date_debut->format( 'Y-m-d' ),
		'heure_debut' => $journee ? '' : $date_debut->format( 'H:i' ),
		'heure_fin'   => $heure_fin,
	];
	// Clé ajoutée seulement si besoin : l'empreinte des événements d'un jour ne change pas
	// (sinon la première synchronisation écraserait toutes les retouches faites sur le site).
	if ( '' !== $date_fin ) {
		$evenement['date_fin'] = $date_fin;
	}
	return $evenement;
}

/* ---------- Écriture dans WordPress ---------- */

/**
 * Description Google → paragraphes de blocs (éditables dans l'éditeur).
 * Google Agenda peut envoyer du HTML simple : seuls les liens et l'emphase sont gardés.
 */
function sv_gcal_contenu( string $description ): string {
	if ( '' === $description ) {
		return '';
	}

	$texte = preg_replace( '#<br\s*/?>|</p>#i', "\n", $description );
	$texte = wp_kses(
		(string) $texte,
		[
			'a'      => [ 'href' => true ],
			'strong' => [],
			'b'      => [],
			'em'     => [],
			'i'      => [],
		]
	);

	$blocs = [];
	foreach ( preg_split( "/\n\s*\n/", $texte ) as $paragraphe ) {
		$paragraphe = trim( $paragraphe );
		if ( '' !== $paragraphe ) {
			$blocs[] = "<!-- wp:paragraph -->\n<p>" . nl2br( $paragraphe, false ) . "</p>\n<!-- /wp:paragraph -->";
		}
	}
	return implode( "\n\n", $blocs );
}

/**
 * Identifiant de l'événement déjà importé avec cet UID (corbeille comprise), ou 0.
 */
function sv_gcal_trouver( string $uid ): int {
	$requete = new WP_Query(
		[
			'post_type'              => 'sv_evenement',
			'post_status'            => [ 'publish', 'future', 'draft', 'pending', 'private', 'trash' ],
			'meta_key'               => '_sv_gcal_uid', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'             => $uid,           // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'fields'                 => 'ids',
			'posts_per_page'         => 1,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'suppress_filters'       => true,
		]
	);
	return $requete->posts ? (int) $requete->posts[0] : 0;
}

/**
 * Crée ou met à jour un événement.
 *
 * @param array{uid:string, titre:string, description:string, lieu:string, date:string, heure_debut:string, heure_fin:string, date_fin?:string} $evenement
 * @return string « crees », « mis_a_jour », « inchanges » ou « ignores » (clé du bilan).
 */
function sv_gcal_enregistrer( array $evenement ): string {
	$id = sv_gcal_trouver( $evenement['uid'] );

	// Événement passé jamais importé : on ne remplit pas l'agenda avec l'historique Google.
	if ( ! $id && ( $evenement['date_fin'] ?? $evenement['date'] ) < current_time( 'Y-m-d' ) ) {
		return 'ignores';
	}
	// Mis à la corbeille par l'équipe : refus volontaire, on n'y touche plus.
	if ( $id && 'trash' === get_post_status( $id ) ) {
		return 'ignores';
	}

	$empreinte = md5( (string) wp_json_encode( $evenement ) );
	if ( $id && get_post_meta( $id, '_sv_gcal_empreinte', true ) === $empreinte ) {
		return 'inchanges'; // rien n'a changé dans Google : les retouches faites sur le site sont conservées
	}

	$metas = [
		'sv_date'            => $evenement['date'],
		'sv_date_fin'        => $evenement['date_fin'] ?? '',
		'sv_heure_debut'     => $evenement['heure_debut'],
		'sv_heure_fin'       => $evenement['heure_fin'],
		'sv_lieu'            => $evenement['lieu'],
		'_sv_gcal_uid'       => $evenement['uid'],
		'_sv_gcal_empreinte' => $empreinte,
	];

	// wp_insert_post() et update_post_meta() retirent les barres obliques inverses : wp_slash() les protège.
	$article = [
		'post_type'    => 'sv_evenement',
		'post_title'   => wp_slash( '' !== $evenement['titre'] ? $evenement['titre'] : 'Événement sans titre' ),
		'post_content' => wp_slash( sv_gcal_contenu( $evenement['description'] ) ),
	];

	if ( $id ) {
		$article['ID'] = $id; // le statut (publié, brouillon…) n'est pas modifié
		$resultat      = wp_update_post( $article, true );
	} else {
		$article['post_status'] = 'draft'; // une personne valide toujours avant publication
		$resultat               = wp_insert_post( $article, true );
	}

	if ( is_wp_error( $resultat ) ) {
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- journal voulu (tâche planifiée sans écran).
		error_log( '[Savoirs Vivants] Agenda Google : événement « ' . $evenement['uid'] . ' » non enregistré (' . $resultat->get_error_message() . ').' );
		return 'ignores';
	}

	foreach ( $metas as $cle => $valeur ) {
		update_post_meta( $resultat, $cle, wp_slash( $valeur ) );
	}

	return $id ? 'mis_a_jour' : 'crees';
}
