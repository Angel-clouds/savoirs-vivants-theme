<?php
/**
 * Savoirs Vivants — chargeur du thème.
 * Chaque fonctionnalité vit dans son propre fichier de inc/.
 */
defined( 'ABSPATH' ) || exit;

define( 'SV_DIR', get_template_directory() );
define( 'SV_URI', get_template_directory_uri() );

// Traductions du thème (languages/de_DE.mo…). Sur « init » : depuis WordPress 6.7,
// charger un domaine de traduction plus tôt déclenche un avertissement.
add_action(
	'init',
	function () {
		load_theme_textdomain( 'savoirs-vivants', SV_DIR . '/languages' );
	},
	0
);

// Présentation : reste dans le thème.
require_once SV_DIR . '/inc/assets.php';             // CSS, JS, tarteaucitron
require_once SV_DIR . '/inc/blocks.php';             // Styles de blocs, catégories de compositions, H1 unique, menu
require_once SV_DIR . '/inc/images-substitution.php'; // Image de substitution pour les blocs Image vides
require_once SV_DIR . '/inc/univers.php';           // Univers graphiques des pôles (CréaBot, Schlouk)
require_once SV_DIR . '/inc/intro.php';             // Animation d'entrée (logo SV, CréaBot, Schlouk)

/*
 * Code métier (types de contenus, agenda, mini-sites, formulaire, redirections, sécurité, SMTP) :
 * il vit dans l'extension « Savoirs Vivants – Fonctionnalités » (dossier extension/ du dépôt),
 * pour survivre à un changement de thème. Les extensions sont chargées AVANT le thème : si elle
 * est active, SV_FONCTIONNALITES est défini et les copies ci-dessous ne sont pas chargées
 * (sinon : « Cannot redeclare sv_… », écran blanc).
 * Copies de secours conservées le temps de la transition (v0.3) : le site fonctionne avant
 * comme après l'activation de l'extension. À retirer en v0.4, une fois l'extension en ligne.
 */
if ( ! defined( 'SV_FONCTIONNALITES' ) ) {
	require_once SV_DIR . '/inc/contenus.php';           // Événements, matériel, champs, bindings
	require_once SV_DIR . '/inc/agenda-filtres.php';     // Agenda général : filtres par agenda et par mois
	require_once SV_DIR . '/inc/sync-agenda.php';        // Import horaire de l'agenda Google (.ics) en brouillons
	require_once SV_DIR . '/inc/mini-sites.php';         // Mini-sites .zip de la Ressourcerie (bac à sable)
	require_once SV_DIR . '/inc/formulaire-contact.php'; // Formulaire de contact accessible
	require_once SV_DIR . '/inc/redirections.php';       // Redirections 301 depuis l'ancien site
	require_once SV_DIR . '/inc/securite.php';           // Comptes non exposés, commentaires fermés
	require_once SV_DIR . '/inc/smtp.php';               // Envoi SMTP (identifiants dans wp-config.php)

	// Rappel aux administrateurs : la copie de secours du thème est en service.
	add_action(
		'admin_notices',
		function () {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}
			printf(
				'<div class="notice notice-warning"><p>%s</p></div>',
				esc_html__( 'L’extension « Savoirs Vivants – Fonctionnalités » n’est pas active : le thème utilise sa copie de secours de l’agenda, de la Ressourcerie et du formulaire. Installez et activez l’extension (Extensions → Ajouter).', 'savoirs-vivants' )
			);
		}
	);
}
