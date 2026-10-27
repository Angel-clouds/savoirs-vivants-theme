<?php
/**
 * Plugin Name:       Savoirs Vivants – Fonctionnalités
 * Description:       Agenda (événements, filtres, synchronisation Google), Ressourcerie (matériel, mini-sites), formulaire de contact, redirections, SMTP et sécurité du site savoirsvivants.fr. Indépendant du thème : les contenus et leurs adresses survivent à un changement de thème.
 * Version:           0.3.0
 * Requires at least: 6.7
 * Requires PHP:      8.2
 * Author:            Savoirs Vivants
 * License:           GPL-2.0-or-later
 * Text Domain:       savoirs-vivants-fonctionnalites
 * Domain Path:       /languages
 *
 * Le thème « Savoirs Vivants » affiche ces contenus (modèles, compositions, CSS) ; cette
 * extension les fait exister. Elle définit SV_FONCTIONNALITES : le thème v0.3 ne charge alors
 * plus sa copie de secours du même code (voir functions.php du thème).
 *
 * @package savoirs-vivants-fonctionnalites
 */

defined( 'ABSPATH' ) || exit;

define( 'SV_FONCTIONNALITES', '0.3.0' );
define( 'SVF_DIR', __DIR__ );
define( 'SVF_URI', plugins_url( '', __FILE__ ) );

// Traductions de l'extension (languages/savoirs-vivants-fonctionnalites-de_DE.mo…).
// Sur « init » : depuis WordPress 6.7, charger un domaine plus tôt déclenche un avertissement.
add_action(
	'init',
	function () {
		load_plugin_textdomain( 'savoirs-vivants-fonctionnalites', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	},
	0
);

/*
 * Requête d'activation : WordPress charge l'extension APRÈS le thème, qui a déjà chargé sa copie
 * de secours du même code (SV_FONCTIONNALITES n'était pas encore défini). La recharger
 * provoquerait « Cannot redeclare sv_… ». Aux requêtes suivantes, l'extension passe avant le thème.
 */
if ( ! function_exists( 'sv_champs_editeur' ) ) {
	require_once SVF_DIR . '/inc/contenus.php';           // Événements, matériel, champs, bindings, aperçu de carte
	require_once SVF_DIR . '/inc/agenda-filtres.php';     // Agenda général : filtres par agenda et par mois
	require_once SVF_DIR . '/inc/sync-agenda.php';        // Import horaire de l'agenda Google (.ics) en brouillons
	require_once SVF_DIR . '/inc/mini-sites.php';         // Mini-sites .zip de la Ressourcerie (bac à sable)
	require_once SVF_DIR . '/inc/formulaire-contact.php'; // Formulaire de contact accessible
	require_once SVF_DIR . '/inc/redirections.php';       // Redirections 301 depuis l'ancien site
	require_once SVF_DIR . '/inc/securite.php';           // Comptes non exposés, commentaires fermés
	require_once SVF_DIR . '/inc/smtp.php';               // Envoi SMTP (identifiants dans wp-config.php)
}

/*
 * Activation : les règles d'adresse (/agenda/…, /ressourcerie/…, /mini-sites/…) sont régénérées
 * à la requête suivante, une fois les types de contenus déclarés sur « init » (contenus.php,
 * option sv_regles_adresses). Ni les contenus ni les réglages ne sont touchés.
 */
register_activation_hook(
	__FILE__,
	function (): void {
		/*
		 * Garde-fou : un thème Savoirs Vivants antérieur à la v0.3 charge le même code sans condition.
		 * Activée avec lui, l'extension ferait planter tout le site dès la requête suivante (écran blanc).
		 * On refuse donc l'activation tant que le thème ne connaît pas SV_FONCTIONNALITES.
		 */
		$fonctions = get_template_directory() . '/functions.php';
		if ( 'savoirs-vivants' === get_template() && is_readable( $fonctions )
			&& ! str_contains( (string) file_get_contents( $fonctions ), 'SV_FONCTIONNALITES' ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- lecture locale du thème.
			wp_die(
				esc_html__( 'Mettez d’abord à jour le thème Savoirs Vivants (version 0.3 ou plus récente) : le thème actuel charge encore sa propre copie de ce code, et l’activation rendrait le site inaccessible.', 'savoirs-vivants-fonctionnalites' ),
				esc_html__( 'Activation impossible', 'savoirs-vivants-fonctionnalites' ),
				[ 'back_link' => true ]
			);
		}
		delete_option( 'sv_regles_adresses' );
	}
);

/*
 * Désactivation : tâche horaire de l'import Google arrêtée, règles d'adresse effacées
 * (WordPress les reconstruit sans les types de l'extension). Les contenus restent en base
 * et réapparaissent à la réactivation.
 */
register_deactivation_hook(
	__FILE__,
	function (): void {
		wp_clear_scheduled_hook( SV_GCAL_CRON );
		delete_option( 'rewrite_rules' );
		delete_option( 'sv_regles_adresses' );
	}
);
