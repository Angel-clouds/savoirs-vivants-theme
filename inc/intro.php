<?php
/**
 * Animation d'entrée : logo plein écran pendant 1,2 s, puis fondu vers la page.
 *
 * - Logo Savoirs Vivants : première page vue pendant la visite (session du navigateur).
 * - Logo CréaBot / Schlouk de Sciences : à la première entrée dans l'univers du pôle pendant la visite
 *   (une seule fois par logo et par session, quel que soit le chemin suivi ensuite).
 * - Jamais au rechargement ni au retour arrière, ni sans JavaScript.
 *
 * Accessibilité (RGAA) :
 * - décorative : conteneur aria-hidden, images alt="" (1.2) ;
 * - 1,2 s, sous le seuil de 5 s au-delà duquel une pause est exigée (13.8), sans clignotement (13.7) ;
 * - pointer-events: none : les clics et le clavier atteignent la page dès le premier instant ;
 * - « réduire les animations » demandé (prefers-reduced-motion) : simple fondu, sans zoom.
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

		// sessionStorage « sv-intro-vus » : animations déjà vues pendant la visite (sv, creabot, schlouk).
		$script = '(function(){try{'
			. 'var s=sessionStorage,u=' . wp_json_encode( $univers ) . ',vus=(s.getItem("sv-intro-vus")||"").split(","),'
			. 'nav=performance.getEntriesByType&&performance.getEntriesByType("navigation")[0],intro="";'
			. 'if(!nav||(nav.type!=="reload"&&nav.type!=="back_forward")){'
			. 'if(u&&vus.indexOf(u)<0)intro=u;else if(vus.indexOf("sv")<0)intro="sv";}'
			. 'if(intro)vus.push(intro);vus.push("sv");'
			. 's.setItem("sv-intro-vus",vus.filter(function(v,i){return v&&vus.indexOf(v)===i;}).join(","));'
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
		<?php elseif ( 'schlouk' === $univers ) : ?>
	<img class="sv-intro__logo sv-intro__logo--schlouk" src="<?php echo esc_url( SV_URI . '/assets/img/schlouk-logo.webp' ); ?>" alt="" width="698" height="697" loading="lazy" decoding="async">
		<?php endif; ?>
</div>
		<?php
	}
);
