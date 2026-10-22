<?php
/**
 * Envoi des e-mails du site par SMTP (formulaire de contact, e-mails de WordPress).
 *
 * Aucun identifiant n'est écrit dans le thème (dépôt public) : ils sont lus, dans l'ordre,
 * dans des constantes de wp-config.php, puis dans des variables d'environnement du serveur.
 *
 *   define( 'SV_SMTP_HOST', 'smtp.exemple.fr' );
 *   define( 'SV_SMTP_PORT', 587 );                        // 587 (STARTTLS) ou 465 (SSL)
 *   define( 'SV_SMTP_SECURE', 'tls' );                    // 'tls' pour 587, 'ssl' pour 465
 *   define( 'SV_SMTP_USER', 'contact@savoirsvivants.fr' );
 *   define( 'SV_SMTP_PASSWORD', '…' );
 *   define( 'SV_SMTP_FROM', 'contact@savoirsvivants.fr' ); // facultatif (par défaut : SV_SMTP_USER)
 *
 * Sans SV_SMTP_HOST, rien ne change : WordPress utilise l'envoi natif du serveur.
 */
defined( 'ABSPATH' ) || exit;

function sv_smtp_reglage( string $cle ): string {
	$nom = 'SV_SMTP_' . $cle;
	if ( defined( $nom ) ) {
		return (string) constant( $nom );
	}
	$valeur = getenv( $nom );
	return false === $valeur ? '' : $valeur;
}

add_action(
	'phpmailer_init',
	function ( $mailer ) {
		$hote = sv_smtp_reglage( 'HOST' );
		if ( '' === $hote ) {
			return;
		}

		$utilisateur = sv_smtp_reglage( 'USER' );
		$securite    = strtolower( sv_smtp_reglage( 'SECURE' ) ?: 'tls' );

		$mailer->isSMTP();
		$mailer->Host       = $hote;
		$mailer->Port       = (int) ( sv_smtp_reglage( 'PORT' ) ?: ( 'ssl' === $securite ? 465 : 587 ) );
		$mailer->SMTPSecure = 'ssl' === $securite ? 'ssl' : 'tls';
		$mailer->SMTPAuth   = '' !== $utilisateur;
		$mailer->Username   = $utilisateur;
		$mailer->Password   = sv_smtp_reglage( 'PASSWORD' );
		$mailer->Timeout    = 15;

		// Expéditeur = l'adresse du compte SMTP (sinon les serveurs refusent ou classent en spam).
		$expediteur = sv_smtp_reglage( 'FROM' ) ?: $utilisateur;
		if ( is_email( $expediteur ) ) {
			$mailer->setFrom( $expediteur, 'Savoirs Vivants', false );
		}
	}
);

/**
 * Échec d'envoi : trace dans le journal PHP (sans identifiants) quand WP_DEBUG_LOG est actif.
 */
add_action(
	'wp_mail_failed',
	function ( $erreur ) {
		if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
			error_log( 'Savoirs Vivants – échec d’envoi d’e-mail : ' . $erreur->get_error_message() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
		}
	}
);
