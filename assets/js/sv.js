/**
 * Savoirs Vivants — scripts du site (JavaScript natif, sans dépendance).
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
