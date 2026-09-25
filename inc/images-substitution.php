<?php
/**
 * Image de substitution : s'affiche à la place des blocs Image restés vides,
 * en attendant les photos définitives. Dès qu'une image est importée dans le bloc,
 * la substitution disparaît d'elle-même.
 *
 * Pour désactiver après la mise en ligne : commenter la ligne correspondante
 * dans functions.php.
 */
defined( 'ABSPATH' ) || exit;

add_filter( 'render_block_core/image', function ( $html, $bloc ) {
	// Le bloc a déjà une image choisie dans la médiathèque : on ne touche à rien.
	if ( ! empty( $bloc['attrs']['id'] ) || ! empty( $bloc['attrs']['url'] ) ) {
		return $html;
	}

	$balises = new WP_HTML_Tag_Processor( $html );
	if ( ! $balises->next_tag( 'img' ) ) {
		return $html;
	}
	if ( $balises->get_attribute( 'src' ) ) {
		return $html;
	}

	$balises->set_attribute( 'src', SV_URI . '/assets/svg/substitution.svg' );
	$balises->set_attribute( 'alt', '' );          // décorative : rien à annoncer
	$balises->set_attribute( 'aria-hidden', 'true' );
	$balises->set_attribute( 'data-sv-substitution', 'true' );

	return $balises->get_updated_html();
}, 10, 2 );