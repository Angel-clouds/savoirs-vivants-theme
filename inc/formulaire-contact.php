<?php
/**
 * Formulaire de contact accessible (RGAA 11) sans extension.
 *
 * Utilisation : bloc « Code court » contenant [sv_formulaire_contact]
 * Destinataire : l'adresse e-mail d'administration (Réglages → Général),
 * modifiable avec le filtre 'sv_contact_destinataire'.
 *
 * Accessibilité :
 * - étiquettes <label> liées à chaque champ, mention « tous les champs sont obligatoires » (11.10) ;
 * - erreurs : récapitulatif en tête avec liens vers les champs, message sous chaque étiquette,
 *   aria-invalid + aria-describedby, préfixe « Erreur : » dans le titre de la page (11.10, 8.6) ;
 * - format attendu indiqué pour l'e-mail, autocomplétion (11.13) ;
 * - message de confirmation annoncé (role="status", 7.5).
 */
defined( 'ABSPATH' ) || exit;

function sv_contact_objets(): array {
	return [
		'renseignement' => __( 'Demande de renseignement', 'savoirs-vivants' ),
		'adhesion'      => __( 'Adhésion à l’association', 'savoirs-vivants' ),
		'ressourcerie'  => __( 'Emprunt de matériel (Ressourcerie)', 'savoirs-vivants' ),
		'creabot'       => __( 'Clubs, stages ou interventions CréaBot', 'savoirs-vivants' ),
		'schlouk'       => __( 'Schlouk de Sciences (conférence, bénévolat)', 'savoirs-vivants' ),
		'recherche'     => __( 'Recherche participative', 'savoirs-vivants' ),
		'autre'         => __( 'Autre demande', 'savoirs-vivants' ),
	];
}

/**
 * État du formulaire pendant l'affichage de la page : erreurs et valeurs saisies.
 * Remplace une variable globale : seul ce fichier peut lire ou écrire cet état.
 *
 * @param array|null $etat Nouvel état ([ 'erreurs' => [], 'valeurs' => [] ]) ou null pour le lire.
 * @return array État courant (tableau vide si le formulaire n'a pas été envoyé).
 */
function sv_contact_etat( ?array $etat = null ): array {
	static $memoire = [];
	if ( null !== $etat ) {
		$memoire = $etat;
	}
	return $memoire;
}

/**
 * Traitement de l'envoi (avant tout affichage).
 */
add_action(
	'template_redirect',
	function () {
		$methode = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ?? '' ) );
		if ( 'POST' !== $methode || empty( $_POST['sv_contact'] ) ) {
			return;
		}

		$referent = wp_get_referer();
		$retour   = remove_query_arg( 'envoi', $referent ? $referent : home_url( '/contact/' ) );

		// Robot détecté (champ piège rempli) : on simule un succès sans rien envoyer.
		if ( ! empty( $_POST['sv_site_web'] ) ) {
			wp_safe_redirect( add_query_arg( 'envoi', 'ok', $retour ) . '#formulaire-contact' );
			exit;
		}

		$valeurs = [
			'prenom'  => sanitize_text_field( wp_unslash( $_POST['sv_prenom'] ?? '' ) ),
			'nom'     => sanitize_text_field( wp_unslash( $_POST['sv_nom'] ?? '' ) ),
			'email'   => sanitize_email( wp_unslash( $_POST['sv_email'] ?? '' ) ),
			'objet'   => sanitize_key( wp_unslash( $_POST['sv_objet'] ?? '' ) ),
			'message' => sanitize_textarea_field( wp_unslash( $_POST['sv_message'] ?? '' ) ),
		];

		$erreurs = [];
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['sv_contact_nonce'] ?? '' ) ), 'sv_contact' ) ) {
			$erreurs['general'] = __( 'La session a expiré. Rechargez la page puis envoyez de nouveau votre message.', 'savoirs-vivants' );
		}
		if ( '' === $valeurs['prenom'] ) {
			$erreurs['prenom'] = __( 'Indiquez votre prénom.', 'savoirs-vivants' );
		}
		if ( '' === $valeurs['nom'] ) {
			$erreurs['nom'] = __( 'Indiquez votre nom.', 'savoirs-vivants' );
		}
		if ( ! is_email( $valeurs['email'] ) ) {
			$erreurs['email'] = __( 'Indiquez une adresse e-mail valide, au format nom@exemple.fr.', 'savoirs-vivants' );
		}
		if ( ! array_key_exists( $valeurs['objet'], sv_contact_objets() ) ) {
			$erreurs['objet'] = __( 'Choisissez l’objet de votre demande.', 'savoirs-vivants' );
		}
		if ( '' === $valeurs['message'] ) {
			$erreurs['message'] = __( 'Rédigez votre message.', 'savoirs-vivants' );
		}

		if ( $erreurs ) {
			sv_contact_etat(
				[
					'erreurs' => $erreurs,
					'valeurs' => $valeurs,
				]
			);
			return; // la page s'affiche avec les erreurs et les valeurs saisies
		}

		// Limite anti-abus : 5 envois par heure et par connexion (empreinte de l'IP, jamais l'IP en clair).
		$ip     = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? '' ) );
		$limite = 'sv_contact_' . wp_hash( $ip );
		$envois = (int) get_transient( $limite );
		if ( $envois >= 5 ) {
			sv_contact_etat(
				[
					'erreurs' => [ 'general' => __( 'Vous avez envoyé plusieurs messages en peu de temps. Réessayez dans une heure ou écrivez-nous à contact@savoirsvivants.fr.', 'savoirs-vivants' ) ],
					'valeurs' => $valeurs,
				]
			);
			return;
		}
		set_transient( $limite, $envois + 1, HOUR_IN_SECONDS );

		// E-mail destiné à l'équipe : gabarit volontairement en français (pas de gettext).
		$objet_libelle = sv_contact_objets()[ $valeurs['objet'] ];
		$envoye        = wp_mail(
			apply_filters( 'sv_contact_destinataire', get_option( 'admin_email' ) ),
			sprintf( '[Site Savoirs Vivants] %s – %s %s', $objet_libelle, $valeurs['prenom'], $valeurs['nom'] ),
			sprintf(
				"Objet : %s\nDe : %s %s <%s>\n\n%s",
				$objet_libelle,
				$valeurs['prenom'],
				$valeurs['nom'],
				$valeurs['email'],
				$valeurs['message']
			),
			// Adresse validée seule : un nom contenant « , autre@adresse » ne peut pas ajouter de destinataire.
			[ 'Reply-To: ' . $valeurs['email'] ]
		);

		wp_safe_redirect( add_query_arg( 'envoi', $envoye ? 'ok' : 'erreur', $retour ) . '#formulaire-contact' );
		exit;
	}
);

/**
 * « Erreur : » en tête du titre de la page quand le formulaire contient des erreurs.
 */
add_filter(
	'document_title_parts',
	function ( $parties ) {
		if ( ! empty( sv_contact_etat()['erreurs'] ) ) {
			/* translators: %s : titre de la page contenant le formulaire de contact en erreur. */
			$parties['title'] = sprintf( __( 'Erreur : %s', 'savoirs-vivants' ), $parties['title'] );
		}
		return $parties;
	}
);

/**
 * Affichage du formulaire.
 */
add_shortcode(
	'sv_formulaire_contact',
	function () {
		$etat    = sv_contact_etat();
		$erreurs = $etat['erreurs'] ?? [];
		$v       = $etat['valeurs'] ?? [];
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- simple message de retour (« ok » / « erreur »), aucune action.
		$envoi = sanitize_key( wp_unslash( $_GET['envoi'] ?? '' ) );

		$libelles = [
			'prenom'  => __( 'Prénom', 'savoirs-vivants' ),
			'nom'     => _x( 'Nom', 'nom de famille', 'savoirs-vivants' ),
			'email'   => __( 'Adresse e-mail', 'savoirs-vivants' ),
			'objet'   => __( 'Objet de la demande', 'savoirs-vivants' ),
			'message' => __( 'Votre message', 'savoirs-vivants' ),
		];

		// Attributs d'accessibilité d'un champ selon son état.
		$aria               = function ( string $champ, string $aide = '' ) use ( $erreurs ): string {
			$decrit = array_filter( [ $aide, isset( $erreurs[ $champ ] ) ? "sv-{$champ}-erreur" : '' ] );
			$html   = isset( $erreurs[ $champ ] ) ? ' aria-invalid="true"' : '';
			return $decrit ? $html . ' aria-describedby="' . esc_attr( implode( ' ', $decrit ) ) . '"' : $html;
		};
			$message_erreur = function ( string $champ ) use ( $erreurs ): string {
				return isset( $erreurs[ $champ ] )
				? '<span class="sv-champ__erreur" id="sv-' . esc_attr( $champ ) . '-erreur">' . esc_html(
					/* translators: %s : message d'erreur d'un champ du formulaire de contact. */
					sprintf( __( 'Erreur : %s', 'savoirs-vivants' ), $erreurs[ $champ ] )
				) . '</span>'
				: '';
			};
			$valeur         = fn( string $champ ): string => esc_attr( $v[ $champ ] ?? '' );

			ob_start();

		if ( 'ok' === $envoi ) {
			echo '<div class="sv-message sv-message--succes" id="sv-contact-retour" role="status" tabindex="-1"><p>' . esc_html__( 'Merci, votre message a bien été envoyé. Nous vous répondrons dans les meilleurs délais.', 'savoirs-vivants' ) . '</p></div>';
		} elseif ( 'erreur' === $envoi ) {
			echo '<div class="sv-message sv-message--erreur" id="sv-contact-retour" role="alert" tabindex="-1"><p>' . sprintf(
				/* translators: %s : lien vers l'adresse e-mail de l'association. */
				esc_html__( 'L’envoi du message a échoué. Écrivez-nous directement à %s.', 'savoirs-vivants' ),
				'<a href="mailto:contact@savoirsvivants.fr">contact@savoirsvivants.fr</a>'
			) . '</p></div>';
		}

		if ( $erreurs ) {
			$nombre = count( $erreurs );
			echo '<div class="sv-message sv-message--erreur" id="sv-contact-retour" role="alert" tabindex="-1">';
			echo '<p class="sv-message__titre">' . esc_html(
				sprintf(
					/* translators: %d : nombre d'erreurs dans le formulaire de contact. */
					_n( 'Le formulaire contient %d erreur :', 'Le formulaire contient %d erreurs :', $nombre, 'savoirs-vivants' ),
					$nombre
				)
			) . '</p><ul>';
			foreach ( $erreurs as $champ => $texte ) {
				$cible = 'general' === $champ ? '' : ' href="#sv-' . esc_attr( $champ ) . '"';
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $cible et $texte sont échappés ci-dessus et dans l'expression.
				echo '<li>' . ( $cible ? '<a' . $cible . '>' . esc_html( $texte ) . '</a>' : esc_html( $texte ) ) . '</li>';
			}
			echo '</ul></div>';
		}
		?>
	<form id="formulaire-contact" class="sv-formulaire" method="post" action="#formulaire-contact" novalidate>
		<p class="sv-formulaire__obligatoire"><?php esc_html_e( 'Tous les champs sont obligatoires.', 'savoirs-vivants' ); ?></p>

		<div class="sv-formulaire__ligne">
			<p class="sv-champ">
				<label for="sv-prenom"><?php echo esc_html( $libelles['prenom'] ); ?></label>
				<?php echo $message_erreur( 'prenom' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<input type="text" id="sv-prenom" name="sv_prenom" value="<?php echo $valeur( 'prenom' ); // phpcs:ignore ?>" autocomplete="given-name" required<?php echo $aria( 'prenom' ); // phpcs:ignore ?>>
			</p>
			<p class="sv-champ">
				<label for="sv-nom"><?php echo esc_html( $libelles['nom'] ); ?></label>
				<?php echo $message_erreur( 'nom' ); // phpcs:ignore ?>
				<input type="text" id="sv-nom" name="sv_nom" value="<?php echo $valeur( 'nom' ); // phpcs:ignore ?>" autocomplete="family-name" required<?php echo $aria( 'nom' ); // phpcs:ignore ?>>
			</p>
		</div>

		<p class="sv-champ">
			<label for="sv-email"><?php echo esc_html( $libelles['email'] ); ?></label>
			<span class="sv-champ__aide" id="sv-email-aide"><?php esc_html_e( 'Format attendu : nom@exemple.fr', 'savoirs-vivants' ); ?></span>
			<?php echo $message_erreur( 'email' ); // phpcs:ignore ?>
			<input type="email" id="sv-email" name="sv_email" value="<?php echo $valeur( 'email' ); // phpcs:ignore ?>" autocomplete="email" required<?php echo $aria( 'email', 'sv-email-aide' ); // phpcs:ignore ?>>
		</p>

		<p class="sv-champ">
			<label for="sv-objet"><?php echo esc_html( $libelles['objet'] ); ?></label>
			<?php echo $message_erreur( 'objet' ); // phpcs:ignore ?>
			<select id="sv-objet" name="sv_objet" required<?php echo $aria( 'objet' ); // phpcs:ignore ?>>
				<option value=""><?php esc_html_e( 'Choisir un objet', 'savoirs-vivants' ); ?></option>
				<?php foreach ( sv_contact_objets() as $cle => $libelle ) : ?>
					<option value="<?php echo esc_attr( $cle ); ?>"<?php selected( $v['objet'] ?? '', $cle ); ?>><?php echo esc_html( $libelle ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p class="sv-champ">
			<label for="sv-message"><?php echo esc_html( $libelles['message'] ); ?></label>
			<?php echo $message_erreur( 'message' ); // phpcs:ignore ?>
			<textarea id="sv-message" name="sv_message" rows="7" required<?php echo $aria( 'message' ); // phpcs:ignore ?>><?php echo esc_textarea( $v['message'] ?? '' ); ?></textarea>
		</p>

		<p class="sv-piege" aria-hidden="true">
			<label for="sv-site-web"><?php esc_html_e( 'Ne pas remplir ce champ', 'savoirs-vivants' ); ?></label>
			<input type="text" id="sv-site-web" name="sv_site_web" value="" tabindex="-1" autocomplete="off">
		</p>

		<p class="sv-formulaire__rgpd">
			<?php esc_html_e( 'Vos données servent uniquement à répondre à votre demande et ne sont jamais transmises à des tiers.', 'savoirs-vivants' ); ?>

			<a href="<?php echo esc_url( home_url( '/politique-de-confidentialite/' ) ); ?>"><?php esc_html_e( 'En savoir plus sur la gestion de vos données', 'savoirs-vivants' ); ?></a>
		</p>

		<input type="hidden" name="sv_contact" value="1">
		<?php wp_nonce_field( 'sv_contact', 'sv_contact_nonce' ); ?>

		<p class="sv-formulaire__envoi">
			<button type="submit" class="wp-element-button"><?php esc_html_e( 'Envoyer le message', 'savoirs-vivants' ); ?></button>
		</p>
	</form>
		<?php
		return ob_get_clean();
	}
);
