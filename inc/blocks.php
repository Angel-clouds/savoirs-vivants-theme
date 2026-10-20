<?php
/**
 * Styles de blocs et catégories de compositions.
 * Les styles apparaissent dans l'éditeur (panneau « Styles » du bloc) :
 * l'équipe les applique en un clic, sans toucher au code.
 */
defined('ABSPATH') || exit;

add_action(
    'init', function () {
        /*
        * Catégories de compositions (inserteur « + » → Compositions).
        * Chaque section de chaque page est une composition autonome, réutilisable partout.
        */
        $categories = [
        'savoirs-vivants'       => [ 'SV · Composants génériques', 'Sections vierges à remplir, conformes à la charte et au RGAA.' ],
        'savoirs-vivants-pages' => [ 'SV · Pages complètes', 'Pages entières construites d’après les maquettes.' ],
        'sv-accueil'            => [ 'SV · Accueil', 'Sections de la page d’accueil.' ],
        'sv-nos-projets'        => [ 'SV · Nos projets', 'Sections de la page Nos projets.' ],
        'sv-creabot'            => [ 'SV · CréaBot', 'Sections du pôle robotique éducative.' ],
        'sv-schlouk'            => [ 'SV · Schlouk de Sciences', 'Sections du pôle médiation scientifique.' ],
        'sv-sciences'           => [ 'SV · Sciences participatives', 'Sections du pôle recherche participative.' ],
        'sv-ressourcerie'       => [ 'SV · Ressourcerie', 'Sections de la Ressourcerie.' ],
        'sv-outils'             => [ 'SV · Outils numériques', 'Sections de la page USUEL.' ],
        'sv-a-propos'           => [ 'SV · À propos', 'Sections de la page À propos.' ],
        'sv-contact'            => [ 'SV · Contact', 'Sections de la page Contact.' ],
        ];
        foreach ( $categories as $slug => [ $label, $description ] ) {
            register_block_pattern_category($slug, [ 'label' => $label, 'description' => $description ]);
        }

        register_block_style('core/paragraph', [ 'name' => 'surtitre', 'label' => 'Surtitre' ]);
        register_block_style('core/heading', [ 'name' => 'titre-carte', 'label' => 'Titre de carte' ]);
        register_block_style('core/group', [ 'name' => 'carte', 'label' => 'Carte' ]);
        register_block_style('core/group', [ 'name' => 'carte-image', 'label' => 'Carte avec image' ]);
        register_block_style('core/details', [ 'name' => 'faq', 'label' => 'Question fréquente' ]);
    } 
);
