<?php
/**
 * Animation d'entrée : logo plein écran pendant 1,2 s, puis fondu vers la page.
 *
 * - Logo Savoirs Vivants : première page vue pendant la visite (session du navigateur).
 * - Logo CréaBot / Schlouk de Sciences : à l'entrée dans l'univers du pôle depuis une autre page
 *   (pas en passant d'une sous-page du pôle à une autre).
 * - Jamais au rechargement ni au retour arrière, ni sans JavaScript.
 *
 * Accessibilité (RGAA) :
 * - décorative : conteneur aria-hidden, images alt="" (1.2) ;
 * - 1,2 s, sous le seuil de 5 s au-delà duquel une pause est exigée (13.8), sans clignotement (13.7) ;
 * - pointer-events: none : les clics et le clavier atteignent la page dès le premier instant ;
 * - aucune animation si le visiteur a demandé à réduire les animations (prefers-reduced-motion).
 *
 * Le choix se fait dans un court script placé en tête de <head> (avant l'affichage, donc sans
 * « flash » de la page) qui ajoute la classe sv-intro-sv / sv-intro-creabot / sv-intro-schlouk à <html>.
 * Le CSS est dans la section 11 de src/css/main.css.
 *
 * @package savoirs-vivants
 */

defined( 'ABSPATH' ) || exit;

/**
 * Univers dont l'entrée est animée (pôles qui ont un logo d'entrée), sinon chaîne vide.
 */
function sv_intro_univers(): string {
	$univers = function_exists( 'sv_univers_courant' ) ? sv_univers_courant() : '';
	return in_array( $univers, [ 'creabot', 'schlouk' ], true ) ? $univers : '';
}

add_action(
	'wp_head',
	function () {
		if ( is_admin() || is_customize_preview() ) {
			return;
		}
		$univers = sv_intro_univers();

		// Police de l'accroche Schlouk : préchargée pour ne pas apparaître d'abord dans une police de secours.
		if ( 'schlouk' === $univers ) {
			printf(
				'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
				esc_url( SV_URI . '/assets/fonts/bebas-neue-latin-400-normal.woff2' )
			);
		}

		$script = '(function(){try{'
			. 'if(matchMedia("(prefers-reduced-motion: reduce)").matches)return;'
			. 'var s=sessionStorage,u=' . wp_json_encode( $univers ) . ',avant=s.getItem("sv-univers"),'
			. 'nav=performance.getEntriesByType&&performance.getEntriesByType("navigation")[0],intro="";'
			. 'if(!nav||(nav.type!=="reload"&&nav.type!=="back_forward")){'
			. 'if(u&&u!==avant)intro=u;else if(avant===null)intro="sv";}'
			. 's.setItem("sv-univers",u);'
			. 'if(intro)document.documentElement.classList.add("sv-intro-"+intro);'
			. '}catch(e){}})();';

		wp_print_inline_script_tag( $script, [ 'id' => 'sv-intro' ] );
	},
	1
);

add_action(
	'wp_body_open',
	function () {
		$univers = sv_intro_univers();
		$sv      = SV_URI . '/assets/svg/logo-savoirs-vivants.svg';
		?>
<div class="sv-intro" aria-hidden="true">
	<img class="sv-intro__logo sv-intro__logo--sv" src="<?php echo esc_url( $sv ); ?>" alt="" width="280" height="89" loading="lazy" decoding="async">
		<?php if ( 'creabot' === $univers ) : ?>
	<img class="sv-intro__logo sv-intro__logo--creabot" src="<?php echo esc_url( SV_URI . '/assets/svg/creabot-logo.svg' ); ?>" alt="" width="1016" height="913" loading="lazy" decoding="async">
		<?php elseif ( 'schlouk' === $univers ) : // Logo HD (cigogne) à demander au collectif : en attendant, nom et bulles du flacon. ?>
	<p class="sv-intro__logo sv-intro__logo--schlouk"><span class="sv-intro__nom">Schlouk</span> de Sciences<span class="sv-intro__bulle"></span><span class="sv-intro__bulle"></span><span class="sv-intro__bulle"></span></p>
		<?php endif; ?>
</div>
		<?php
	}
);
