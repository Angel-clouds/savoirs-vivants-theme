<?php
/**
 * Redirections 301 depuis les adresses de l'ancien site (Elementor).
 * Préserve le référencement et les liens déjà partagés.
 * Ne s'active que si l'adresse demandée n'existe pas (404) : aucun risque de boucle.
 */
defined('ABSPATH') || exit;

add_action(
    'template_redirect', function () {
        if (! is_404() ) {
            return;
        }

        $anciennes = apply_filters(
            'sv_redirections', [
            'nos-projets/creabot'        => '/creabot/',
            'schlouk-de-science'         => '/schlouk-de-sciences/',
            'recherches-participatives'  => '/sciences-participatives/',
            'association'                => '/a-propos/',
            'nos-partenaires'            => '/a-propos/',
            'codey-rocky'                => '/ressourcerie/codey-rocky/',
            'makey-makey'                => '/ressourcerie/makey-makey/',
            'poubelle-connectee'         => '/ressourcerie/poubelle-connectee/',
            'bras-robot'                 => '/ressourcerie/bras-robot/',
            ] 
        );

        $adresse = sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'] ?? ''));
        $chemin  = trim((string) wp_parse_url($adresse, PHP_URL_PATH), '/');

        if (isset($anciennes[ $chemin ]) ) {
            wp_safe_redirect(home_url($anciennes[ $chemin ]), 301);
            exit;
        }
    }, 1 
);
