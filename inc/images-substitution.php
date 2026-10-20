<?php
/**
 * Image de substitution : s'affiche à la place des blocs Image restés vides,
 * en attendant les photos définitives. Dès qu'une image est importée dans le bloc,
 * la substitution disparaît d'elle-même.
 *
 * On intervient AVANT le rendu (render_block_data) et non après (render_block_core/image) :
 * la fonction de rendu du cœur, render_block_core_image(), renvoie une chaîne vide
 * quand la balise <img> n'a pas de src. Un filtre placé après ne reçoit donc plus rien
 * à modifier. En ajoutant le src en amont, le cœur affiche le bloc normalement.
 *
 * Pour désactiver après la mise en ligne : commenter la ligne correspondante
 * dans functions.php.
 */
defined('ABSPATH') || exit;

add_filter(
    'render_block_data', function ( $bloc ) {
        if ('core/image' !== ( $bloc['blockName'] ?? '' ) ) {
            return $bloc;
        }
        // Le bloc a déjà une image choisie dans la médiathèque : on ne touche à rien.
        if (! empty($bloc['attrs']['id']) || ! empty($bloc['attrs']['url']) ) {
            return $bloc;
        }

        $balises = new WP_HTML_Tag_Processor($bloc['innerHTML'] ?? '');
        if (! $balises->next_tag('img') || $balises->get_attribute('src') ) {
            return $bloc;
        }

        // Déclinaison aux couleurs du pôle sur les pages CréaBot et Schlouk (inc/univers.php).
        $univers = function_exists('sv_univers_courant') ? sv_univers_courant() : '';
        $fichier = $univers ? 'substitution-' . $univers . '.svg' : 'substitution.svg';

        $balises->set_attribute('src', SV_URI . '/assets/svg/' . $fichier);
        $balises->set_attribute('alt', '');          // décorative : rien à annoncer
        $balises->set_attribute('aria-hidden', 'true');
        $balises->set_attribute('data-sv-substitution', 'true');

        // Le rendu du cœur lit innerContent : on met à jour les deux.
        $bloc['innerHTML']    = $balises->get_updated_html();
        $bloc['innerContent'] = array( $bloc['innerHTML'] );

        return $bloc;
    } 
);
