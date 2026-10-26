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

require_once SV_DIR . '/inc/assets.php';             // CSS, JS, tarteaucitron
require_once SV_DIR . '/inc/blocks.php';             // Styles de blocs + catégories de compositions
require_once SV_DIR . '/inc/contenus.php';           // Événements, matériel, champs, bindings
require_once SV_DIR . '/inc/agenda-filtres.php';     // Agenda général : filtres par agenda et par mois
require_once SV_DIR . '/inc/sync-agenda.php';        // Import horaire de l'agenda Google (.ics) en brouillons
require_once SV_DIR . '/inc/mini-sites.php';        // Mini-sites .zip de la Ressourcerie (bac à sable)
require_once SV_DIR . '/inc/formulaire-contact.php'; // Formulaire de contact accessible
require_once SV_DIR . '/inc/redirections.php';       // Redirections 301 depuis l'ancien site
require_once SV_DIR . '/inc/images-substitution.php'; // Image de substitution pour les blocs Image vides
require_once SV_DIR . '/inc/univers.php';           // Univers graphiques des pôles (CréaBot, Schlouk)
require_once SV_DIR . '/inc/intro.php';             // Animation d'entrée (logo SV, CréaBot, Schlouk)
require_once SV_DIR . '/inc/securite.php';          // Comptes non exposés, commentaires fermés
require_once SV_DIR . '/inc/smtp.php';              // Envoi SMTP (identifiants dans wp-config.php)
