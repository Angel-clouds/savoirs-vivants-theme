<?php
/**
 * Title: Logo Savoirs Vivants (en-tête)
 * Slug: savoirs-vivants/logo
 * Inserter: no
 * Description: Logo officiel complet (charte graphique 2023), lien vers l'accueil. Appelé par parts/header.html.
 *
 * Accessibilité : l'image est l'unique contenu du lien, son alternative en donne donc la destination (RGAA 6.1, 1.3).
 *
 * @package savoirs-vivants
 */

$sv_logo_alt = __( 'Savoirs Vivants, retour à l’accueil', 'savoirs-vivants' );
?>
<!-- wp:image {"width":"164px","sizeSlug":"full","linkDestination":"custom","className":"sv-logo-site"} -->
<figure class="wp-block-image size-full is-resized sv-logo-site"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/svg/logo-savoirs-vivants.svg' ); ?>" alt="<?php echo esc_attr( $sv_logo_alt ); ?>" style="width:164px"/></a></figure>
<!-- /wp:image -->
