/**
 * Savoirs Vivants – Fonctionnalités : formulaire de contact (JavaScript natif, sans dépendance).
 * Chargé par [sv_formulaire_contact], seulement sur la page qui affiche le formulaire.
 */
( function () {
	'use strict';

	/*
	 * Formulaire de contact : après un envoi ou une erreur, le focus est placé
	 * sur le message de retour pour qu'il soit lu immédiatement (RGAA 7.5, 11.10).
	 */
	var retour = document.getElementById( 'sv-contact-retour' );
	if ( retour ) {
		retour.focus();
	}
} )();
