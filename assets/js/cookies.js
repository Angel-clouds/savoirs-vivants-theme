/**
 * Savoirs Vivants — gestionnaire de cookies chargé à la demande (JavaScript natif, sans dépendance).
 *
 * Tant qu'aucun service tiers n'est déclaré (filtre PHP « sv_services_tiers »), le site ne dépose
 * aucun traceur : tarteaucitron.js (≈ 87 Ko + langue + services + CSS) n'est donc chargé qu'au clic
 * sur « Gestion des cookies » (pied de page) ou à l'arrivée sur une adresse en #tarteaucitron,
 * puis son panneau s'ouvre. Le jour où un service est déclaré, inc/assets.php recharge
 * tarteaucitron sur toutes les pages (nécessaire pour bloquer le service avant consentement)
 * et ce script n'est plus appelé.
 *
 * Réglages (adresse, langue, configuration) : window.svCookies, imprimé par inc/assets.php.
 */
( function () {
	'use strict';

	var reglages = window.svCookies;
	var etat = 'attente'; // attente → chargement → pret

	if ( ! reglages ) {
		return;
	}

	function ouvrirPanneau() {
		window.tarteaucitron.userInterface.openPanel();
	}

	function charger() {
		if ( 'attente' !== etat ) {
			return;
		}
		etat = 'chargement';
		window.tarteaucitronForceLanguage = reglages.langue;
		// Le panneau ne peut s'ouvrir qu'une fois sa structure construite par tarteaucitron.
		window.addEventListener(
			'tac.root_available',
			function () {
				etat = 'pret';
				ouvrirPanneau();
			},
			{ once: true }
		);
		var script = document.createElement( 'script' );
		script.src = reglages.src;
		script.onload = function () {
			window.tarteaucitron.init( reglages.config );
		};
		document.head.appendChild( script );
	}

	// Délégation : un seul écouteur pour tous les boutons, même ajoutés plus tard.
	// Une fois tarteaucitron chargé, ses propres écouteurs ouvrent le panneau.
	document.addEventListener( 'click', function ( evenement ) {
		if ( 'pret' === etat || ! evenement.target.closest ) {
			return;
		}
		if ( evenement.target.closest( '.tarteaucitronOpenPanel' ) ) {
			evenement.preventDefault();
			charger();
		}
	} );

	if ( '#tarteaucitron' === window.location.hash ) {
		charger();
	}
} )();
