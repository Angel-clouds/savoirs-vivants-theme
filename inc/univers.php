<?php
/**
 * Univers graphiques des pôles : CréaBot, Schlouk de Sciences et Sciences participatives ont leur propre habillage.
 *
 * On ajoute une classe au <body> (sv-univers-creabot, sv-univers-schlouk, sv-univers-sciences) sur la page du pôle,
 * ses sous-pages et l'archive de son pôle dans l'agenda. Le CSS (section 10 de src/css/main.css)
 * n'applique la charte du pôle qu'au contenu principal (<main>) : l'en-tête, le bloc « Suivre /
 * Soutenir » et le pied de page restent aux couleurs de Savoirs Vivants.
 *
 * Aucune modification des compositions n'est nécessaire : les pages déjà créées
 * prennent le nouvel habillage automatiquement.
 */
defined('ABSPATH') || exit;

/**
 * Identifiant de page (slug) ou terme de pôle → univers graphique.
 */
function sv_univers_pages(): array
{
    return [
    'creabot'                 => 'creabot',
    'schlouk-de-sciences'     => 'schlouk',
    'sciences-participatives' => 'sciences',
    ];
}

/**
 * Univers graphique de la page affichée : « creabot », « schlouk », « sciences » ou chaîne vide.
 */
function sv_univers_courant(): string
{
    static $courant = null;
    if (null !== $courant ) {
        return $courant;
    }
    // Requête principale pas encore connue : on ne mémorise rien.
    if (! did_action('wp') ) {
        return '';
    }

    $univers = sv_univers_pages();
    $slugs   = [];

    if (is_page() ) {
        $page = get_queried_object();
        // La page elle-même puis ses parents : les sous-pages héritent de l'univers.
        foreach ( array_merge([ $page->ID ], get_post_ancestors($page)) as $id ) {
            $slugs[] = get_post_field('post_name', $id);
        }
    } elseif (is_tax('sv_pole') ) {
        $slugs[] = get_queried_object()->slug;
    }

    $courant = '';
    foreach ( $slugs as $slug ) {
        if (isset($univers[ $slug ]) ) {
            $courant = $univers[ $slug ];
            break;
        }
    }

    return $courant;
}

add_filter(
    'body_class', function ( $classes ) {
        if (sv_univers_courant() ) {
            $classes[] = 'sv-univers-' . sv_univers_courant();
        }
        return $classes;
    } 
);
