<?php
/**
 * Styles de blocs et catégories de compositions.
 * Les styles apparaissent dans l'éditeur (panneau « Styles » du bloc) :
 * l'équipe les applique en un clic, sans toucher au code.
 */
defined( 'ABSPATH' ) || exit;

add_action( 'init', function () {
	register_block_pattern_category( 'savoirs-vivants', [
		'label'       => 'Savoirs Vivants – Sections',
		'description' => 'Sections prêtes à l’emploi, conformes à la charte et au RGAA.',
	] );
	register_block_pattern_category( 'savoirs-vivants-pages', [
		'label'       => 'Savoirs Vivants – Pages complètes',
		'description' => 'Pages entières construites d’après les maquettes.',
	] );

	register_block_style( 'core/paragraph', [ 'name' => 'surtitre', 'label' => 'Surtitre' ] );
	register_block_style( 'core/heading', [ 'name' => 'titre-carte', 'label' => 'Titre de carte' ] );
	register_block_style( 'core/group', [ 'name' => 'carte', 'label' => 'Carte' ] );
	register_block_style( 'core/group', [ 'name' => 'carte-image', 'label' => 'Carte avec image' ] );
	register_block_style( 'core/details', [ 'name' => 'faq', 'label' => 'Question fréquente' ] );
} );
