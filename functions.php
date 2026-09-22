<?php
/**
 * Savoirs Vivants — chargeur du thème.
 * Chaque fonctionnalité vit dans son propre fichier de inc/.
 */
defined( 'ABSPATH' ) || exit;

define( 'SV_DIR', get_template_directory() );
define( 'SV_URI', get_template_directory_uri() );

require_once SV_DIR . '/inc/assets.php';             // CSS, JS, tarteaucitron
require_once SV_DIR . '/inc/blocks.php';             // Styles de blocs + catégories de compositions
require_once SV_DIR . '/inc/contenus.php';           // Événements, matériel, champs, bindings
require_once SV_DIR . '/inc/formulaire-contact.php'; // Formulaire de contact accessible
require_once SV_DIR . '/inc/redirections.php';       // Redirections 301 depuis l'ancien site
