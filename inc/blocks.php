<?php
defined( 'ABSPATH' ) || exit;

add_action( 'init', function () {
	// Catégorie de compositions visible dans l'éditeur
	register_block_pattern_category( 'savoirs-vivants', [
		'label' => 'Savoirs Vivants',
	] );

	// Styles réutilisables, sélectionnables dans l'éditeur (panneau « Styles »)
	register_block_style( 'core/paragraph', [
		'name'  => 'surtitre',
		'label' => 'Surtitre',
	] );
	register_block_style( 'core/group', [
		'name'  => 'carte',
		'label' => 'Carte',
	] );
} );