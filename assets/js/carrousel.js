/**
 * Savoirs Vivants — carrousel accessible (JavaScript natif, sans dépendance).
 *
 * S'applique à toute « Boucle de requête » portant la classe CSS sv-carrousel.
 * Sans JavaScript, les articles restent affichés en grille : le carrousel n'est
 * qu'une amélioration progressive.
 *
 * Accessibilité (motif « Carousel » des WAI-ARIA Authoring Practices, RGAA 7 et 13.8) :
 * - aucun défilement automatique : l'utilisateur garde la main (13.8) ;
 * - vrais <button> « Précédent » / « Suivant », placés avant les articles, avec aria-controls ;
 * - articles masqués avec l'attribut hidden : ni lus, ni atteignables au clavier ;
 * - la liste reste une liste ; chaque article donne sa position « 2 sur 4 » (aria-posinset / aria-setsize) ;
 * - position visible et annoncée poliment après chaque clic (aria-live="polite", 7.5) ;
 * - aucune animation : l'affichage change instantanément (pas de gêne vestibulaire).
 *
 * Le nombre d'articles visibles suit le nombre de colonnes de la grille
 * (1 sur mobile, 2 ou 3 sur écran large), lu directement dans le CSS.
 */
( function () {
	'use strict';

	var compteur = 0;

	function initialiser( carrousel ) {
		var liste    = carrousel.querySelector( '.wp-block-post-template' );
		var articles = liste ? Array.prototype.slice.call( liste.children ) : [];
		if ( articles.length < 2 ) {
			return;
		}

		compteur++;
		var debut = 0;

		// Nom du carrousel : le titre de la section qui le contient.
		var section = carrousel.closest( '.wp-block-group' );
		var titre   = section ? section.querySelector( 'h2' ) : null;
		carrousel.setAttribute( 'role', 'region' );
		carrousel.setAttribute( 'aria-roledescription', 'carrousel' );
		if ( titre ) {
			titre.id = titre.id || 'sv-carrousel-titre-' + compteur;
			carrousel.setAttribute( 'aria-labelledby', titre.id );
		} else {
			carrousel.setAttribute( 'aria-label', 'Actualités' );
		}

		liste.id = liste.id || 'sv-carrousel-liste-' + compteur;
		// La liste <ul>/<li> est conservée (role="group" est interdit sur <li> en HTML) ;
		// la position réelle est donnée malgré les articles masqués : « 2 sur 4 ».
		articles.forEach( function ( article, i ) {
			article.setAttribute( 'aria-posinset', i + 1 );
			article.setAttribute( 'aria-setsize', articles.length );
		} );

		// Commandes : insérées avant la liste pour être atteintes avant les articles.
		var commandes = document.createElement( 'div' );
		commandes.className = 'sv-carrousel__commandes';
		commandes.innerHTML =
			'<button type="button" class="sv-carrousel__bouton" data-sens="-1" aria-controls="' + liste.id + '">' +
				'<span aria-hidden="true">←</span> Précédent</button>' +
			'<p class="sv-carrousel__etat" aria-live="polite"></p>' +
			'<button type="button" class="sv-carrousel__bouton" data-sens="1" aria-controls="' + liste.id + '">' +
				'Suivant <span aria-hidden="true">→</span></button>';
		liste.parentNode.insertBefore( commandes, liste );

		var etat     = commandes.querySelector( '.sv-carrousel__etat' );
		var annoncer = false; // pas d'annonce au chargement, seulement après une action

		function parVue() {
			// Pistes calculées par le navigateur (« 320px 320px 320px ») ; à défaut, 1 article par vue.
			var colonnes = getComputedStyle( liste ).gridTemplateColumns.split( ' ' ).filter( function ( piste ) {
				return /px$/.test( piste );
			} ).length;
			return Math.max( 1, Math.min( colonnes || 1, articles.length ) );
		}

		function afficher() {
			var n = parVue();
			debut = Math.floor( debut / n ) * n; // aligné sur le début d'une « page »

			articles.forEach( function ( article, i ) {
				article.hidden = i < debut || i >= debut + n;
			} );

			var fin = Math.min( debut + n, articles.length );
			commandes.hidden = n >= articles.length; // tout tient à l'écran : pas de commandes

			var texte = fin - debut === 1
				? 'Article ' + ( debut + 1 ) + ' sur ' + articles.length
				: 'Articles ' + ( debut + 1 ) + ' à ' + fin + ' sur ' + articles.length;
			// Le texte n'est écrit qu'en cas d'action : la région live ne parle pas au chargement.
			etat.setAttribute( 'aria-live', annoncer ? 'polite' : 'off' );
			etat.textContent = texte;
		}

		commandes.addEventListener( 'click', function ( evenement ) {
			var bouton = evenement.target.closest( 'button' );
			if ( ! bouton ) {
				return;
			}
			var n     = parVue();
			var pages = Math.ceil( articles.length / n );
			var page  = ( Math.floor( debut / n ) + Number( bouton.dataset.sens ) + pages ) % pages; // boucle
			debut     = page * n;
			annoncer  = true;
			afficher();
		} );

		// Rotation de l'écran, redimensionnement : le nombre de colonnes peut changer.
		if ( 'ResizeObserver' in window ) {
			var largeur = 0;
			new ResizeObserver( function ( entrees ) {
				var l = Math.round( entrees[ 0 ].contentRect.width );
				if ( l !== largeur ) {
					largeur  = l;
					annoncer = false;
					afficher();
				}
			} ).observe( carrousel );
		}

		carrousel.classList.add( 'is-carrousel-actif' );
		afficher();
	}

	document.querySelectorAll( '.sv-carrousel' ).forEach( initialiser );
} )();
