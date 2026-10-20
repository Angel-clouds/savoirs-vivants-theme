<?php
/**
 * Mini-sites pédagogiques (HTML, CSS, JS) importés en .zip et présentés sur la page Ressourcerie.
 *
 * Administration : menu Ressourcerie → Mini-sites (administrateurs uniquement). Le .zip déposé
 * dans le cadre « Fichiers du mini-site » est vérifié, extrait fichier par fichier dans
 * wp-content/uploads/mini-sites/<id>-<jeton aléatoire>/, puis supprimé.
 * Affichage : code court [sv_mini_sites pole="creabot"] (composition « Ressourcerie – Mini-sites »).
 *
 * Sécurité :
 * - Le code d'un mini-site n'est jamais servi depuis l'origine du site : chaque fichier passe par
 *   /mini-sites/<slug>/… avec l'en-tête « Content-Security-Policy: sandbox ». Le navigateur l'isole
 *   comme un site étranger : pas d'accès aux cookies, à l'administration ni à l'API REST, même
 *   ouvert par un administrateur connecté.
 * - Pas d'appel à des services tiers (RGPD, règle 4 du projet) : seules les ressources du
 *   mini-site lui-même sont autorisées, les bibliothèques doivent donc être incluses dans le .zip.
 * - Le dossier d'extraction porte un nom imprévisible et n'est jamais affiché. Accès direct
 *   interdit par .htaccess (Apache) ; sous nginx, ajouter à la configuration du serveur :
 *   location ^~ /wp-content/uploads/mini-sites/ { deny all; }
 */
defined('ABSPATH') || exit;

/* -------------------------------------------------------------------------
 * Réglages (modifiables par filtre)
 * ---------------------------------------------------------------------- */

function sv_minisites_limites(): array
{
    return apply_filters(
        'sv_minisites_limites', [
        'taille_zip'      => 50 * MB_IN_BYTES,  // poids du .zip envoyé
        'taille_totale'   => 200 * MB_IN_BYTES, // poids total une fois décompressé
        'nombre_fichiers' => 2000,
        'ratio'           => 100,               // décompressé / compressé : au-delà, « bombe zip »
        'profondeur'      => 20,                // sous-dossiers imbriqués
        ] 
    );
}

/**
 * Extensions acceptées → type MIME envoyé au navigateur. Tout le reste est ignoré à l'import.
 */
function sv_minisites_types(): array
{
    return apply_filters(
        'sv_minisites_types', [
        'html'  => 'text/html', // encodage : celui déclaré par la page (<meta charset>)
        'htm'   => 'text/html',
        'css'   => 'text/css; charset=utf-8',
        'js'    => 'text/javascript; charset=utf-8',
        'mjs'   => 'text/javascript; charset=utf-8',
        'json'  => 'application/json; charset=utf-8',
        'txt'   => 'text/plain; charset=utf-8',
        'csv'   => 'text/csv; charset=utf-8',
        'xml'   => 'application/xml; charset=utf-8',
        'vtt'   => 'text/vtt; charset=utf-8',
        'svg'   => 'image/svg+xml',
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'gif'   => 'image/gif',
        'webp'  => 'image/webp',
        'avif'  => 'image/avif',
        'ico'   => 'image/x-icon',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'   => 'font/ttf',
        'otf'   => 'font/otf',
        'mp3'   => 'audio/mpeg',
        'ogg'   => 'audio/ogg',
        'wav'   => 'audio/wav',
        'mp4'   => 'video/mp4',
        'webm'  => 'video/webm',
        'pdf'   => 'application/pdf',
        'wasm'  => 'application/wasm',
        ] 
    );
}

/**
 * Extensions exécutables par un serveur : leur présence fait refuser tout le .zip
 * (y compris en double extension : « image.php.png »).
 */
const SV_MINISITES_INTERDITS = '/\.(php\d?|phtml|phar|pht|phps|shtml?|cgi|pl|asp|aspx|jsp|htaccess|htpasswd|user\.ini)(\.|$)/i';

/**
 * Suffixe ajouté à chaque fichier extrait : en accès direct (hors /mini-sites/…), le serveur l'envoie
 * comme un fichier inconnu (téléchargement), jamais comme une page HTML exécutée sur le site.
 * Le bac à sable de /mini-sites/<slug>/ reste ainsi le seul moyen d'afficher un mini-site.
 */
const SV_MINISITES_SUFFIXE = '.sv-data';

function sv_minisites_base(): string
{
    return wp_upload_dir(null, false)['basedir'] . '/mini-sites';
}

/**
 * Seuls les administrateurs autorisés à publier du code (unfiltered_html) peuvent importer :
 * avec DISALLOW_UNFILTERED_HTML ou en multisite, personne ne peut, ce qui est voulu.
 */
function sv_minisites_peut_importer(): bool
{
    return current_user_can('manage_options') && current_user_can('unfiltered_html');
}

/* -------------------------------------------------------------------------
 * Type de contenu
 * ---------------------------------------------------------------------- */

add_action(
    'init', function () {
        $capacites = array_fill_keys(
            [ 'edit_post', 'read_post', 'delete_post', 'edit_posts', 'edit_others_posts', 'edit_published_posts', 'publish_posts', 'read_private_posts', 'delete_posts', 'delete_others_posts', 'delete_published_posts', 'create_posts' ],
            'manage_options'
        );

        register_post_type(
            'sv_minisite', [
            'labels'              => [
            'name'          => 'Mini-sites',
            'singular_name' => 'Mini-site',
            'menu_name'     => 'Mini-sites',
            'all_items'     => 'Mini-sites',
            'add_new'       => 'Ajouter un mini-site',
            'add_new_item'  => 'Ajouter un mini-site',
            'edit_item'     => 'Modifier le mini-site',
            'search_items'  => 'Rechercher un mini-site',
            'not_found'     => 'Aucun mini-site trouvé',
            ],
            'public'              => false, // pas de page WordPress : le mini-site est servi par /mini-sites/<slug>/
            'show_ui'             => true,
            'show_in_menu'        => 'edit.php?post_type=sv_materiel', // sous-menu de « Ressourcerie »
            'show_in_rest'        => false, // éditeur classique : nécessaire pour envoyer un fichier avec le formulaire
            'exclude_from_search' => true,
            'rewrite'             => false,
            'query_var'           => false,
            'capabilities'        => $capacites, // administrateurs uniquement
            'map_meta_cap'        => false,
            'supports'            => [ 'title', 'excerpt', 'thumbnail' ],
            ] 
        );
    }, 5 
);

/* -------------------------------------------------------------------------
 * Catégories de mini-sites : arborescence propre (CréaBot → Arduino…), gérée dans
 * Ressourcerie → Catégories de mini-sites. Distincte des pôles de l'agenda (sv_pole) :
 * une sous-catégorie créée ici n'apparaît ni dans les événements ni dans /agenda/pole/.
 * ---------------------------------------------------------------------- */

add_action( 'init', function () {
	register_taxonomy( 'sv_minisite_cat', [ 'sv_minisite' ], [
		'labels'            => [
			'name'              => 'Catégories de mini-sites',
			'singular_name'     => 'Catégorie',
			'menu_name'         => 'Catégories de mini-sites',
			'all_items'         => 'Toutes les catégories',
			'parent_item'       => 'Catégorie parente',
			'parent_item_colon' => 'Catégorie parente :',
			'edit_item'         => 'Modifier la catégorie',
			'view_item'         => 'Voir la catégorie',
			'update_item'       => 'Mettre à jour la catégorie',
			'add_new_item'      => 'Ajouter une catégorie',
			'new_item_name'     => 'Nom de la nouvelle catégorie',
			'search_items'      => 'Rechercher une catégorie',
			'not_found'         => 'Aucune catégorie trouvée',
			'back_to_items'     => '← Retour aux catégories',
		],
		'hierarchical'      => true,  // sous-catégories, case « Catégorie parente »
		'public'            => false, // pas de page d'archive : les mini-sites sont listés par le code court
		'show_ui'           => true,
		'show_in_menu'      => false, // lien ajouté sous « Ressourcerie » (le type de contenu y est rangé)
		'show_admin_column' => true,  // colonne dans la liste des mini-sites
		'show_in_rest'      => true,
		'show_tagcloud'     => false,
		'query_var'         => false,
		'rewrite'           => false,
		'capabilities'      => array_fill_keys( [ 'manage_terms', 'edit_terms', 'delete_terms', 'assign_terms' ], 'manage_options' ), // administrateurs, comme les mini-sites
	] );
}, 5 );

add_action( 'admin_menu', function () {
	add_submenu_page(
		'edit.php?post_type=sv_materiel',
		'Catégories de mini-sites',
		'Catégories de mini-sites',
		'manage_options',
		'edit-tags.php?taxonomy=sv_minisite_cat&post_type=sv_minisite'
	);
} );

// Menu « Ressourcerie » déplié et sous-menu surligné sur l'écran des catégories.
add_filter( 'parent_file', function ( $parent ) {
	return 'sv_minisite_cat' === ( get_current_screen()->taxonomy ?? '' ) ? 'edit.php?post_type=sv_materiel' : $parent;
} );
add_filter( 'submenu_file', function ( $sous_menu ) {
	return 'sv_minisite_cat' === ( get_current_screen()->taxonomy ?? '' ) ? 'edit-tags.php?taxonomy=sv_minisite_cat&post_type=sv_minisite' : $sous_menu;
} );

/**
 * Migration unique depuis les pôles (sv_pole) : catégories principales créées avec les mêmes
 * identifiants (creabot…), pour que [sv_mini_sites pole="creabot"] reste valable, puis
 * rattachement de chaque mini-site existant. Une catégorie supprimée ensuite n'est pas recréée.
 */
const SV_MINISITES_CATEGORIES = '1';
add_action( 'init', function () {
	if ( get_option( 'sv_minisites_categories' ) === SV_MINISITES_CATEGORIES ) {
		return;
	}
	foreach ( [ 'CréaBot' => 'creabot', 'Schlouk de Sciences' => 'schlouk-de-sciences', 'Sciences participatives' => 'sciences-participatives' ] as $nom => $slug ) {
		if ( ! term_exists( $slug, 'sv_minisite_cat' ) ) {
			wp_insert_term( $nom, 'sv_minisite_cat', [ 'slug' => $slug ] );
		}
	}
	$minisites = get_posts( [ 'post_type' => 'sv_minisite', 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids' ] );
	foreach ( $minisites as $id ) {
		$poles = wp_get_object_terms( $id, 'sv_pole', [ 'fields' => 'slugs' ] );
		$cats  = is_array( $poles ) ? array_filter( $poles, fn( $slug ) => term_exists( $slug, 'sv_minisite_cat' ) ) : [];
		if ( $cats ) {
			wp_set_object_terms( $id, array_values( $cats ), 'sv_minisite_cat', true );
		}
	}
	update_option( 'sv_minisites_categories', SV_MINISITES_CATEGORIES );
}, 20 );

add_filter('enter_title_here', fn( $texte, $post ) => 'sv_minisite' === $post->post_type ? 'Nom du mini-site' : $texte, 10, 2);

/* -------------------------------------------------------------------------
 * Cadre « Fichiers du mini-site »
 * ---------------------------------------------------------------------- */

add_action(
    'add_meta_boxes_sv_minisite', function () {
        add_meta_box('sv-minisite-fichiers', 'Fichiers du mini-site', 'sv_minisites_cadre', 'sv_minisite', 'normal', 'high');
        // L'extrait sert de description sur la carte.
        remove_meta_box('postexcerpt', 'sv_minisite', 'normal');
        add_meta_box('postexcerpt', 'Description courte (affichée sur la carte)', 'post_excerpt_meta_box', 'sv_minisite', 'normal', 'high');
    } 
);

// Le formulaire de l'éditeur classique doit pouvoir transporter un fichier.
add_action(
    'post_edit_form_tag', function ( $post ) {
        if ('sv_minisite' === $post->post_type ) {
            echo ' enctype="multipart/form-data"';
        }
    } 
);

function sv_minisites_cadre( WP_Post $post ): void
{
    $index  = get_post_meta($post->ID, '_sv_minisite_index', true);
    $infos  = get_post_meta($post->ID, '_sv_minisite_infos', true);
    $limite = min(wp_max_upload_size(), sv_minisites_limites()['taille_zip']);

    if ($index && 'publish' === $post->post_status ) {
        printf(
            '<p><strong>Mini-site en ligne</strong> : <a href="%1$s" target="_blank" rel="noopener">%1$s<span class="screen-reader-text"> (nouvel onglet)</span></a></p>',
            esc_url(sv_minisites_url($post))
        );
        if (is_array($infos) ) {
            printf(
                '<p>%d fichiers, %s, importés le %s.</p>',
                (int) $infos['fichiers'],
                esc_html(size_format($infos['poids'])),
                esc_html(wp_date('j F Y à H:i', $infos['date']))
            );
        }
    } elseif ($index ) {
        echo '<p>Fichiers importés. Le mini-site sera accessible une fois publié.</p>';
    } else {
        echo '<p>Aucun fichier importé : le mini-site n’apparaîtra pas sur le site.</p>';
    }

    if (! class_exists('ZipArchive') ) {
        echo '<p><strong>L’extension PHP « zip » n’est pas active sur le serveur : import impossible.</strong></p>';
        return;
    }
    if (! sv_minisites_peut_importer() ) {
        echo '<p>Votre compte ne permet pas d’importer du code (droit « unfiltered_html » requis).</p>';
        return;
    }

    wp_nonce_field('sv_minisite_import', 'sv_minisite_nonce');
    ?>
    <p>
        <label for="sv-minisite-zip"><strong><?php echo $index ? 'Remplacer par un nouveau fichier .zip' : 'Fichier .zip du mini-site'; ?></strong></label><br>
        <input type="file" id="sv-minisite-zip" name="sv_minisite_zip" accept=".zip,application/zip" aria-describedby="sv-minisite-aide">
    </p>
    <div id="sv-minisite-aide">
        <p>Le fichier est importé à l’enregistrement (« Publier » ou « Mettre à jour »), puis supprimé du serveur. Taille maximale : <?php echo esc_html(size_format($limite)); ?>.</p>
        <ul style="list-style:disc;padding-left:1.5em">
            <li>Une page <code>index.html</code> est obligatoire, à la racine du .zip ou dans un dossier unique.</li>
            <li>Fichiers acceptés : <?php echo esc_html(implode(', ', array_keys(sv_minisites_types()))); ?>. Les autres sont ignorés ; un fichier PHP ou exécutable fait refuser le .zip.</li>
            <li>Aucune ressource externe (CDN, Google Fonts, statistiques…) : incluez les bibliothèques dans le .zip, sinon elles seront bloquées.</li>
        </ul>
    </div>
    <?php
}

/* -------------------------------------------------------------------------
 * Import à l'enregistrement
 * ---------------------------------------------------------------------- */

add_action(
    'save_post_sv_minisite', function ( $post_id ) {
        if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id) ) {
            return;
        }
        if (! isset($_POST['sv_minisite_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['sv_minisite_nonce'])), 'sv_minisite_import') ) {
            return;
        }
        if (! current_user_can('edit_post', $post_id) || ! sv_minisites_peut_importer() ) {
            return;
        }
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- fichier vérifié dans sv_minisites_importer().
        $fichier = $_FILES['sv_minisite_zip'] ?? null;
        if (! is_array($fichier) || UPLOAD_ERR_NO_FILE === ( $fichier['error'] ?? UPLOAD_ERR_NO_FILE ) ) {
            return; // enregistrement sans nouveau fichier
        }

        $resultat = sv_minisites_importer($post_id, $fichier);

        if (is_wp_error($resultat) ) {
            $avis = [ 'error', 'Import refusé : ' . $resultat->get_error_message() ];
        } else {
            $message = sprintf('Mini-site importé : %d fichiers.', $resultat['fichiers']);
            if ($resultat['ignores'] ) {
                $message .= ' Fichiers ignorés (type non accepté) : ' . implode(', ', array_slice($resultat['ignores'], 0, 10)) . ( count($resultat['ignores']) > 10 ? '…' : '' );
            }
            $avis = [ $resultat['ignores'] ? 'warning' : 'success', $message ];
        }
        set_transient('sv_minisite_avis_' . get_current_user_id(), $avis, MINUTE_IN_SECONDS);
    } 
);

add_action(
    'admin_notices', function () {
        $cle  = 'sv_minisite_avis_' . get_current_user_id();
        $avis = get_transient($cle);
        if (! $avis || 'sv_minisite' !== ( get_current_screen()?->post_type ?? '' ) ) {
            return;
        }
        delete_transient($cle);
        printf('<div class="notice notice-%s is-dismissible"><p>%s</p></div>', esc_attr($avis[0]), esc_html($avis[1]));
    } 
);

/**
 * Vérifie le .zip envoyé, l'extrait dans un dossier temporaire puis remplace l'ancienne version.
 * Le .zip n'est jamais copié dans la médiathèque : il est supprimé dans tous les cas.
 *
 * @return array|WP_Error [ 'fichiers' => int, 'ignores' => string[] ]
 */
function sv_minisites_importer( int $post_id, array $fichier ): array|WP_Error
{
    $tmp = (string) ( $fichier['tmp_name'] ?? '' );

    try {
        $valide = sv_minisites_verifier_envoi($fichier);
        if (is_wp_error($valide) ) {
            return $valide;
        }

        $zip = new ZipArchive();
        if (true !== $zip->open($tmp, ZipArchive::CHECKCONS | ( defined('ZipArchive::RDONLY') ? ZipArchive::RDONLY : 0 )) ) {
            return new WP_Error('zip', 'archive .zip illisible ou corrompue.');
        }

        $inventaire = sv_minisites_inventaire($zip);
        if (is_wp_error($inventaire) ) {
            $zip->close();
            return $inventaire;
        }

        $base = sv_minisites_preparer_base();
        if (is_wp_error($base) ) {
            $zip->close();
            return $base;
        }

        // Extraction dans un dossier temporaire : la version en ligne reste intacte en cas d'échec.
        $nom_dossier = $post_id . '-' . bin2hex(random_bytes(16));
        $temporaire  = $base . '/.import-' . $nom_dossier;
        $extraction  = sv_minisites_extraire($zip, $inventaire['fichiers'], $temporaire);
        $zip->close();

        if (is_wp_error($extraction) ) {
            sv_minisites_supprimer_dossier($temporaire);
            return $extraction;
        }
        if (! rename($temporaire, $base . '/' . $nom_dossier) ) {
            sv_minisites_supprimer_dossier($temporaire);
            return new WP_Error('ecriture', 'impossible de finaliser l’import (droits d’écriture du dossier uploads).');
        }

        $ancien = get_post_meta($post_id, '_sv_minisite_dossier', true);
        update_post_meta($post_id, '_sv_minisite_dossier', $nom_dossier);
        update_post_meta($post_id, '_sv_minisite_index', $inventaire['index']);
        update_post_meta(
            $post_id, '_sv_minisite_infos', [
            'fichiers' => count($inventaire['fichiers']),
            'poids'    => $extraction,
            'date'     => time(),
            ] 
        );
        if ($ancien ) {
            sv_minisites_supprimer_dossier($base . '/' . $ancien);
        }

        return [ 'fichiers' => count($inventaire['fichiers']), 'ignores' => $inventaire['ignores'] ];
    } finally {
        // Suppression du .zip, quel que soit le résultat.
        if ($tmp && is_file($tmp) ) {
            wp_delete_file($tmp);
        }
    }
}

/**
 * Contrôles du fichier envoyé : erreur d'envoi, origine, poids, extension, type MIME réel, signature.
 */
function sv_minisites_verifier_envoi( array $fichier ): bool|WP_Error
{
    $erreurs = [
    UPLOAD_ERR_INI_SIZE   => 'fichier trop lourd pour le serveur (upload_max_filesize).',
    UPLOAD_ERR_FORM_SIZE  => 'fichier trop lourd.',
    UPLOAD_ERR_PARTIAL    => 'envoi interrompu, réessayez.',
    UPLOAD_ERR_NO_TMP_DIR => 'dossier temporaire du serveur manquant.',
    UPLOAD_ERR_CANT_WRITE => 'écriture impossible sur le serveur.',
    UPLOAD_ERR_EXTENSION  => 'envoi bloqué par une extension PHP.',
    ];
    $code = (int) ( $fichier['error'] ?? UPLOAD_ERR_NO_FILE );
    if (UPLOAD_ERR_OK !== $code ) {
        return new WP_Error('envoi', $erreurs[ $code ] ?? 'échec de l’envoi.');
    }

    $tmp = (string) $fichier['tmp_name'];
    if (! is_uploaded_file($tmp) ) {
        return new WP_Error('envoi', 'fichier non reçu par le formulaire.');
    }

    $limite = min(wp_max_upload_size(), sv_minisites_limites()['taille_zip']);
    $poids  = (int) filesize($tmp);
    if ($poids <= 0 || $poids > $limite ) {
        return new WP_Error('poids', sprintf('le fichier doit peser au plus %s.', size_format($limite)));
    }

    if ('zip' !== strtolower(pathinfo((string) $fichier['name'], PATHINFO_EXTENSION)) ) {
        return new WP_Error('type', 'le fichier doit porter l’extension .zip.');
    }

    // Type réel lu dans le contenu (le type annoncé par le navigateur n'est pas fiable).
    // Objet finfo libéré automatiquement (finfo_close() est dépréciée depuis PHP 8.5).
    $mime = ( new finfo(FILEINFO_MIME_TYPE) )->file($tmp) ?: '';
    if (! in_array($mime, [ 'application/zip', 'application/x-zip-compressed', 'application/x-zip' ], true) ) {
        return new WP_Error('type', 'le contenu du fichier n’est pas une archive .zip.');
    }

    // Signature d'une archive zip non vide : « PK\x03\x04 ».
    $debut = (string) file_get_contents($tmp, false, null, 0, 4);
    if ("PK\x03\x04" !== $debut ) {
        return new WP_Error('type', 'archive .zip vide ou non valide.');
    }

    return true;
}

/**
 * Chemin relatif propre (séparateurs « / ») ou WP_Error si le nom est dangereux.
 */
function sv_minisites_chemin( string $nom ): string|WP_Error
{
    $nom = str_replace('\\', '/', $nom); // archives créées sous Windows
    if ('' === $nom || str_contains($nom, "\0") || ! mb_check_encoding($nom, 'UTF-8') ) {
        return new WP_Error('nom', 'nom de fichier illisible (encodage). Recréez le .zip avec des noms sans accents.');
    }
    if (preg_match('/[\x00-\x1F\x7F<>:"|?*%]/u', $nom) || str_starts_with($nom, '/') || preg_match('/^[a-z]:/i', $nom) ) {
        return new WP_Error('nom', sprintf('nom de fichier interdit : %s', $nom));
    }

    $segments = explode('/', preg_replace('#^(\./)+#', '', $nom));
    if ('' === end($segments) ) {
        array_pop($segments); // dossier : « images/ »
    }
    foreach ( $segments as $segment ) {
        if ('' === $segment || '.' === $segment || '..' === $segment || strlen($segment) > 255 ) {
            return new WP_Error('nom', sprintf('chemin interdit : %s', $nom));
        }
    }
    if (count($segments) > sv_minisites_limites()['profondeur'] ) {
        return new WP_Error('nom', sprintf('trop de sous-dossiers : %s', $nom));
    }

    return implode('/', $segments);
}

/**
 * Inventaire de l'archive avant toute écriture : noms, liens symboliques, extensions, volumes, page d'accueil.
 *
 * @return array|WP_Error [ 'fichiers' => [ index zip => chemin relatif ], 'ignores' => string[], 'index' => string ]
 */
function sv_minisites_inventaire( ZipArchive $zip ): array|WP_Error
{
    $limites = sv_minisites_limites();
    $types   = sv_minisites_types();

    if ($zip->numFiles > $limites['nombre_fichiers'] ) {
        return new WP_Error('volume', sprintf('plus de %d fichiers dans l’archive.', $limites['nombre_fichiers']));
    }

    $fichiers = [];
    $ignores  = [];
    $vus      = [];
    $total    = 0;

    for ( $i = 0; $i < $zip->numFiles; $i++ ) {
        $stat = $zip->statIndex($i);
        if (false === $stat ) {
            return new WP_Error('zip', 'archive corrompue.');
        }

        $chemin = sv_minisites_chemin($stat['name']);
        if (is_wp_error($chemin) ) {
            return $chemin;
        }

        // Lien symbolique : pourrait pointer hors du dossier.
        if ($zip->getExternalAttributesIndex($i, $systeme, $attributs) && ZipArchive::OPSYS_UNIX === $systeme && 0120000 === ( ( $attributs >> 16 ) & 0170000 ) ) {
            return new WP_Error('lien', sprintf('lien symbolique interdit : %s', $chemin));
        }

        if (preg_match(SV_MINISITES_INTERDITS, $chemin) ) {
            return new WP_Error('executable', sprintf('fichier exécutable côté serveur interdit : %s', $chemin));
        }

        if (str_ends_with(str_replace('\\', '/', $stat['name']), '/') ) {
            continue; // dossier : créé à l'écriture de ses fichiers
        }

        // Fichiers système et cachés (.DS_Store, __MACOSX, .git…) : ignorés sans avertir.
        $segments = explode('/', $chemin);
        if ('__MACOSX' === $segments[0] || preg_grep('/^\./', $segments) || in_array(strtolower(end($segments)), [ 'thumbs.db', 'desktop.ini' ], true) ) {
            continue;
        }

        if (! isset($types[ strtolower(pathinfo($chemin, PATHINFO_EXTENSION)) ]) ) {
            $ignores[] = $chemin;
            continue;
        }

        // Doublons (y compris à la casse près, pour les serveurs insensibles à la casse).
        $cle = mb_strtolower($chemin);
        if (isset($vus[ $cle ]) ) {
            return new WP_Error('doublon', sprintf('fichier présent deux fois : %s', $chemin));
        }
        $vus[ $cle ] = true;

        // Volumes annoncés : contrôlés à nouveau pendant l'extraction (ils peuvent mentir).
        $total += $stat['size'];
        if ($total > $limites['taille_totale'] ) {
            return new WP_Error('volume', sprintf('plus de %s une fois décompressé.', size_format($limites['taille_totale'])));
        }
        if ($stat['size'] > MB_IN_BYTES && $stat['size'] > $limites['ratio'] * max(1, $stat['comp_size']) ) {
            return new WP_Error('volume', sprintf('taux de compression anormal (archive piégée ?) : %s', $chemin));
        }

        $fichiers[ $i ] = $chemin;
    }

    // Dossier racine unique (« mon-site/index.html… ») : retiré pour que index.html soit à la racine.
    $racines = array_unique(array_map(fn( $c ) => str_contains($c, '/') ? strtok($c, '/') . '/' : '', $fichiers));
    if (1 === count($racines) && '' !== reset($racines) ) {
        $prefixe  = reset($racines);
        $fichiers = array_map(fn( $c ) => substr($c, strlen($prefixe)), $fichiers);
    }

    $index = array_values(array_intersect([ 'index.html', 'index.htm' ], array_map('strtolower', $fichiers)));
    if (! $index ) {
        return new WP_Error('index', 'aucune page index.html à la racine du mini-site.');
    }
    // Casse réelle du fichier trouvé (Index.html…).
    $index = current(array_filter($fichiers, fn( $c ) => strtolower($c) === $index[0]));

    return [ 'fichiers' => $fichiers, 'ignores' => $ignores, 'index' => $index ];
}

/**
 * Écrit chaque fichier retenu lui-même (jamais ZipArchive::extractTo) en limitant le volume réel lu.
 *
 * @return int|WP_Error Poids total écrit.
 */
function sv_minisites_extraire( ZipArchive $zip, array $fichiers, string $dossier ): int|WP_Error
{
    $limites = sv_minisites_limites();
    $total   = 0;

    if (! wp_mkdir_p($dossier) ) {
        return new WP_Error('ecriture', 'impossible de créer le dossier d’extraction.');
    }
    $racine = realpath($dossier);

    foreach ( $fichiers as $i => $chemin ) {
        $cible = $dossier . '/' . $chemin . SV_MINISITES_SUFFIXE;
        if (! wp_mkdir_p(dirname($cible)) ) {
            return new WP_Error('ecriture', sprintf('impossible de créer le dossier de %s.', $chemin));
        }
        // Défense en profondeur : la cible doit rester dans le dossier d'extraction.
        $parent = realpath(dirname($cible));
        if (false === $parent || ( $parent !== $racine && ! str_starts_with($parent, $racine . DIRECTORY_SEPARATOR) ) ) {
            return new WP_Error('nom', sprintf('chemin interdit : %s', $chemin));
        }

        $source = method_exists($zip, 'getStreamIndex') ? $zip->getStreamIndex($i) : $zip->getStream($zip->getNameIndex($i));
        $sortie = fopen($cible, 'xb'); // « x » : échoue si le fichier existe déjà
        if (! $source || ! $sortie ) {
            return new WP_Error('ecriture', sprintf('extraction impossible : %s', $chemin));
        }

        $annonce = (int) $zip->statIndex($i)['size'];
        $copie   = stream_copy_to_stream($source, $sortie, $annonce + 1);
        fclose($source);
        fclose($sortie);

        // Contenu plus long que la taille annoncée : archive falsifiée.
        if (false === $copie || $copie !== $annonce ) {
            return new WP_Error('zip', sprintf('contenu incohérent : %s', $chemin));
        }
        $total += $copie;
        if ($total > $limites['taille_totale'] ) {
            return new WP_Error('volume', sprintf('plus de %s une fois décompressé.', size_format($limites['taille_totale'])));
        }
        chmod($cible, defined('FS_CHMOD_FILE') ? FS_CHMOD_FILE : 0644);
    }

    return $total;
}

/**
 * Dossier wp-content/uploads/mini-sites/ protégé contre l'accès direct et le listage.
 */
function sv_minisites_preparer_base(): string|WP_Error
{
    $base = sv_minisites_base();
    if (! wp_mkdir_p($base) ) {
        return new WP_Error('ecriture', 'impossible de créer wp-content/uploads/mini-sites/.');
    }
    $protections = [
    'index.php' => "<?php\n// Silence.\n",
    '.htaccess' => "# Accès direct interdit : les mini-sites sont servis par WordPress (inc/mini-sites.php du thème).\n<IfModule mod_authz_core.c>\n\tRequire all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\n\tOrder allow,deny\n\tDeny from all\n</IfModule>\n",
    ];
    foreach ( $protections as $nom => $contenu ) {
        if (! is_file("$base/$nom") ) {
            file_put_contents("$base/$nom", $contenu);
        }
    }
    return $base;
}

/**
 * Suppression récursive, limitée aux sous-dossiers de uploads/mini-sites/.
 */
function sv_minisites_supprimer_dossier( string $dossier ): void
{
    $base   = realpath(sv_minisites_base());
    $chemin = realpath($dossier);
    if (! $base || ! $chemin || ! str_starts_with($chemin, $base . DIRECTORY_SEPARATOR) || ! is_dir($chemin) ) {
        return;
    }
    $elements = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($chemin, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ( $elements as $element ) {
        $element->isDir() && ! $element->isLink() ? rmdir($element->getPathname()) : unlink($element->getPathname());
    }
    rmdir($chemin);
}

// Mini-site supprimé définitivement (corbeille vidée) : ses fichiers aussi.
add_action(
    'before_delete_post', function ( $post_id ) {
        if ('sv_minisite' === get_post_type($post_id) ) {
            $dossier = get_post_meta($post_id, '_sv_minisite_dossier', true);
            if ($dossier ) {
                sv_minisites_supprimer_dossier(sv_minisites_base() . '/' . $dossier);
            }
        }
    } 
);

/* -------------------------------------------------------------------------
 * Diffusion : /mini-sites/<slug>/<fichier>, dans un bac à sable
 * ---------------------------------------------------------------------- */

function sv_minisites_url( WP_Post $post ): string
{
    return home_url('/mini-sites/' . $post->post_name . '/');
}

/**
 * Interception avant l'analyse de la requête par WordPress : aucune règle de réécriture nécessaire.
 * L'adresse est lue directement (et non via des variables de requête, qui transforment « + » et « ' »).
 */
add_filter(
    'do_parse_request', function ( $continuer ) {
        $chemin  = (string) wp_parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
        $prefixe = trailingslashit((string) wp_parse_url(home_url('/'), PHP_URL_PATH)) . 'mini-sites/';
        if (! str_starts_with($chemin, $prefixe) ) {
            return $continuer;
        }

        [ $slug, $fichier ] = array_pad(explode('/', substr($chemin, strlen($prefixe)), 2), 2, null);
        sv_minisites_servir(sanitize_title(rawurldecode($slug)), $fichier);
        return $continuer; // fichier introuvable : WordPress affiche sa page 404
    }, 1 
);

function sv_minisites_servir( string $slug, ?string $fichier ): void
{
    if ('' === $slug ) {
        return;
    }
    // Publiés uniquement : un brouillon ne pourrait pas être prévisualisé de toute façon, le bac à sable
    // empêchant l'envoi des cookies de connexion avec les fichiers CSS, JS et images.
    $post    = get_posts([ 'post_type' => 'sv_minisite', 'name' => $slug, 'post_status' => 'publish', 'numberposts' => 1 ])[0] ?? null;
    $dossier = $post ? (string) get_post_meta($post->ID, '_sv_minisite_dossier', true) : '';
    if (! $post || ! preg_match('/^\d+-[a-f0-9]{32}$/', $dossier) ) {
        return;
    }

    // « /mini-sites/slug » sans barre finale : les liens relatifs du mini-site seraient faux.
    if (null === $fichier ) {
        wp_safe_redirect(sv_minisites_url($post), 301);
        exit;
    }

    $relatif = '' === $fichier ? get_post_meta($post->ID, '_sv_minisite_index', true) : rawurldecode($fichier);
    if (str_ends_with($relatif, '/') ) {
        $relatif .= 'index.html';
    }
    $relatif = sv_minisites_chemin($relatif);
    $type    = is_wp_error($relatif) ? null : ( sv_minisites_types()[ strtolower(pathinfo($relatif, PATHINFO_EXTENSION)) ] ?? null );
    if (! $type ) {
        return;
    }

    $racine = realpath(sv_minisites_base() . '/' . $dossier);
    // Fichier suffixé ; à défaut, mini-site importé avant l'ajout du suffixe (à réimporter).
    $chemin = realpath($racine . '/' . $relatif . SV_MINISITES_SUFFIXE) ?: realpath($racine . '/' . $relatif);
    if (! $racine || ! $chemin || ! str_starts_with($chemin, $racine . DIRECTORY_SEPARATOR) || ! is_file($chemin) ) {
        return;
    }

    $etag = '"' . md5($chemin . filemtime($chemin) . filesize($chemin)) . '"';

    status_header(200);
    header('Content-Type: ' . $type);
    header('Content-Security-Policy: ' . sv_minisites_csp($post));
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: same-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=(), usb=()');
    // Bac à sable = origine « opaque » : polices, modules JS et fetch() du mini-site sont des requêtes CORS.
    header('Access-Control-Allow-Origin: *');
    header('Cache-Control: public, max-age=300');
    header('ETag: ' . $etag);

    if (trim(wp_unslash($_SERVER['HTTP_IF_NONE_MATCH'] ?? '')) === $etag ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
        status_header(304);
        exit;
    }
    header('Content-Length: ' . filesize($chemin));
    if ('HEAD' !== ( $_SERVER['REQUEST_METHOD'] ?? 'GET' ) ) {
        readfile($chemin); // phpcs:ignore WordPress.WP.AlternativeFunctions
    }
    exit;
}

/**
 * Politique de sécurité : bac à sable (origine opaque) et ressources limitées au mini-site.
 * L'adresse est écrite en toutes lettres (« 'self' » n'est pas fiable dans un document en bac à sable)
 * et se termine par « / » : la source couvre tout le mini-site et rien d'autre. Le mini-site ne peut
 * ni charger ni interroger le reste du site (API REST, administration…).
 */
function sv_minisites_csp( WP_Post $post ): string
{
    $ici = sv_minisites_url($post);

    return implode(
        '; ', apply_filters(
            'sv_minisites_csp', [
            'sandbox allow-scripts allow-forms allow-modals allow-popups allow-popups-to-escape-sandbox allow-downloads',
            "default-src $ici",
            "script-src $ici 'unsafe-inline' 'unsafe-eval' blob:",
            "style-src $ici 'unsafe-inline'",
            "img-src $ici data: blob:",
            "media-src $ici data: blob:",
            "font-src $ici data:",
            "connect-src $ici",
            "worker-src $ici blob:",
            "frame-src $ici",
            "form-action $ici",
            "base-uri $ici",
            "frame-ancestors 'self'",
            ], $post 
        ) 
    );
}

/* -------------------------------------------------------------------------
 * Affichage : [sv_mini_sites pole="creabot" niveau="4"] (cartes) ou format="liste" titre="CréaBot" (colonne compacte)
 * ---------------------------------------------------------------------- */

/**
 * Accessibilité :
 * - liste de cartes (<ul>) : le nombre de mini-sites est annoncé par les lecteurs d'écran ;
 * - niveau de titre réglable pour respecter la hiérarchie de la page (RGAA 9.1) ;
 * - ouverture dans un nouvel onglet signalée par une icône et un texte pour les lecteurs d'écran (RGAA 13.2) ;
 * - image d'illustration décorative (alt vide) : le titre suffit à identifier le mini-site (RGAA 1.2).
 */
add_shortcode( 'sv_mini_sites', function ( $atts ) {
	$atts   = shortcode_atts( [ 'pole' => '', 'categorie' => '', 'niveau' => '4', 'format' => 'cartes', 'titre' => '' ], $atts, 'sv_mini_sites' );
	// « categorie » ou, pour les pages existantes, « pole » : slug d'une catégorie de mini-sites.
	$categorie = sanitize_title( $atts['categorie'] ?: $atts['pole'] );
	$niveau = min( 6, max( 2, (int) $atts['niveau'] ) );

	$requete = [
		'post_type'      => 'sv_minisite',
		'post_status'    => 'publish',
		'posts_per_page' => 100,
		'orderby'        => 'title',
		'order'          => 'ASC',
		'no_found_rows'  => true,
		'meta_query'     => [ [ 'key' => '_sv_minisite_index', 'compare' => 'EXISTS' ] ],
	];
	if ( $categorie ) {
		// Sous-catégories incluses : les mini-sites de « CréaBot → Arduino » s'affichent avec CréaBot.
		$requete['tax_query'] = [ [ 'taxonomy' => 'sv_minisite_cat', 'field' => 'slug', 'terms' => $categorie, 'include_children' => true ] ];
	}
	// Une colonne par catégorie principale, générée depuis l'arborescence (aucun slug à écrire).
	if ( 'colonnes' === $atts['format'] ) {
		return sv_minisites_colonnes( $niveau );
	}

	$minisites = get_posts( $requete );

	if ( 'liste' === $atts['format'] ) {
		$terme = $categorie ? get_term_by( 'slug', $categorie, 'sv_minisite_cat' ) : null;
		return $terme
			? sv_minisites_colonne( $terme, $minisites, '' !== $atts['titre'] ? $atts['titre'] : $terme->name, $niveau )
			: sv_minisites_liste( $minisites, $atts['titre'], $niveau );
	}

        if (! $minisites ) {
            return '<p class="sv-minisites__vide">Aucun mini-site n’est publié pour le moment.</p>';
        }

        $html = '<ul class="sv-minisites">';
        foreach ( $minisites as $minisite ) {
            $html .= '<li class="is-style-carte-image sv-minisite">';
            if (has_post_thumbnail($minisite) ) {
                $html .= '<figure class="wp-block-post-featured-image">' . get_the_post_thumbnail($minisite, 'medium_large', [ 'alt' => '', 'loading' => 'lazy' ]) . '</figure>';
            }
            $html .= sprintf(
                '<h%1$d class="is-style-titre-carte"><a href="%2$s" target="_blank" rel="noopener">%3$s<span class="sv-minisite__icone" aria-hidden="true">↗</span><span class="sv-sr-only"> (nouvel onglet)</span></a></h%1$d>',
                $niveau,
                esc_url(sv_minisites_url($minisite)),
                esc_html(get_the_title($minisite))
            );
            if (has_excerpt($minisite) ) {
                $html .= '<p>' . esc_html(get_the_excerpt($minisite)) . '</p>';
            }
            $html .= '</li>';
        }
        return $html . '</ul>';
    } 
);

/**
 * Format « liste » : une colonne par pôle, lisible même avec de nombreux mini-sites
 * ([sv_mini_sites pole="creabot" format="liste" titre="CréaBot" niveau="3"]).
 *
 * Accessibilité :
 * - le titre de colonne (niveau réglable, RGAA 9.1) annonce aussi le nombre de mini-sites ;
 * - les mini-sites sont des liens dans une liste <ul> et non des titres : la navigation
 *   par titres reste courte, même avec des dizaines de ressources ;
 * - ouverture dans un nouvel onglet signalée (icône + texte pour les lecteurs d'écran, RGAA 13.2) ;
 * - une fiche sans titre reste identifiable (« Mini-site sans titre ») au lieu d'un lien vide (RGAA 6.2).
 */
function sv_minisites_liste( array $minisites, string $titre, int $niveau ): string
{
    $nombre = count($minisites);
    $html   = '<div class="sv-minisites-colonne">';

    if ('' !== $titre ) {
        // Compteur masqué à zéro : le message « Aucun mini-site » le dit déjà.
        $compteur = $nombre ? sprintf(1 === $nombre ? '%d mini-site' : '%d mini-sites', $nombre) : '';
        $html    .= sprintf(
            '<h%1$d class="sv-minisites-colonne__titre">%2$s%3$s</h%1$d>',
            $niveau,
            esc_html($titre),
            $compteur ? ' <span class="sv-minisites-colonne__nombre">' . esc_html($compteur) . '</span>' : ''
        );
    }

    if (! $minisites ) {
        return $html . '<p class="sv-minisites-colonne__vide">Aucun mini-site pour le moment.</p></div>';
    }

	return $html . sv_minisites_liens( $minisites ) . '</div>';
}

/**
 * Liste <ul> de liens vers des mini-sites (format « liste » et colonnes groupées).
 */
function sv_minisites_liens( array $minisites ): string {
	$html = '<ul class="sv-minisites-liste">';
	foreach ( $minisites as $minisite ) {
		$nom   = get_the_title( $minisite );
		$html .= sprintf(
			'<li><a href="%1$s" target="_blank" rel="noopener">%2$s<span class="sv-minisite__icone" aria-hidden="true">↗</span><span class="sv-sr-only"> (nouvel onglet)</span></a>',
			esc_url( sv_minisites_url( $minisite ) ),
			esc_html( '' !== $nom ? $nom : 'Mini-site sans titre' )
		);
		if ( has_excerpt( $minisite ) ) {
			$html .= '<p>' . esc_html( get_the_excerpt( $minisite ) ) . '</p>';
		}
		$html .= '</li>';
	}
	return $html . '</ul>';
}

/**
 * Format « colonnes » ([sv_mini_sites format="colonnes" niveau="3"]) : une carte par catégorie
 * principale, dans l'ordre alphabétique. Ajouter, renommer ou supprimer une catégorie dans
 * Ressourcerie → Catégories de mini-sites met la page à jour sans la modifier.
 */
function sv_minisites_colonnes( int $niveau ): string {
	$principales = get_terms( [
		'taxonomy'   => 'sv_minisite_cat',
		'parent'     => 0,
		'hide_empty' => false, // une catégorie sans mini-site garde sa colonne (« Aucun mini-site »)
		'orderby'    => 'name',
	] );
	if ( is_wp_error( $principales ) || ! $principales ) {
		return '<p class="sv-minisites__vide">Aucun mini-site n’est publié pour le moment.</p>';
	}

	$html = '<div class="sv-minisites-colonnes">';
	foreach ( $principales as $terme ) {
		$minisites = get_posts( [
			'post_type'      => 'sv_minisite',
			'post_status'    => 'publish',
			'posts_per_page' => 100,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'no_found_rows'  => true,
			'meta_query'     => [ [ 'key' => '_sv_minisite_index', 'compare' => 'EXISTS' ] ],
			'tax_query'      => [ [ 'taxonomy' => 'sv_minisite_cat', 'field' => 'term_id', 'terms' => $terme->term_id, 'include_children' => true ] ],
		] );
		$html .= '<div class="wp-block-group is-style-carte">' . sv_minisites_colonne( $terme, $minisites, $terme->name, $niveau ) . '</div>';
	}
	return $html . '</div>';
}

/**
 * Colonne d'une catégorie principale : titre + compteur, mini-sites rattachés directement
 * à la catégorie, puis un groupe par sous-catégorie (titre de niveau inférieur, RGAA 9.1).
 * Un mini-site rangé dans une sous-catégorie n'est listé que dans son groupe ; les
 * sous-sous-catégories sont réunies dans le groupe de leur sous-catégorie.
 * Structure titre + un seul conteneur : nécessaire à l'alignement des barres (subgrid, main.css).
 */
function sv_minisites_colonne( WP_Term $principale, array $minisites, string $titre, int $niveau ): string {
	$nombre   = count( $minisites );
	$compteur = $nombre ? sprintf( 1 === $nombre ? '%d mini-site' : '%d mini-sites', $nombre ) : '';
	$html     = sprintf(
		'<div class="sv-minisites-colonne"><h%1$d class="sv-minisites-colonne__titre">%2$s%3$s</h%1$d><div class="sv-minisites-colonne__contenu">',
		$niveau,
		esc_html( $titre ),
		$compteur ? ' <span class="sv-minisites-colonne__nombre">' . esc_html( $compteur ) . '</span>' : ''
	);

	if ( ! $minisites ) {
		return $html . '<p class="sv-minisites-colonne__vide">Aucun mini-site pour le moment.</p></div></div>';
	}

	$sous = get_terms( [ 'taxonomy' => 'sv_minisite_cat', 'parent' => $principale->term_id, 'hide_empty' => false, 'orderby' => 'name' ] );
	$sous = is_wp_error( $sous ) ? [] : $sous;

	// Sous-catégorie (enfant direct) de chaque terme de l'arborescence.
	$groupe_de = [];
	foreach ( $sous as $enfant ) {
		$groupe_de[ $enfant->term_id ] = $enfant->term_id;
		foreach ( get_term_children( $enfant->term_id, 'sv_minisite_cat' ) as $descendant ) {
			$groupe_de[ $descendant ] = $enfant->term_id;
		}
	}

	$directs = [];
	$groupes = array_fill_keys( wp_list_pluck( $sous, 'term_id' ), [] );
	foreach ( $minisites as $minisite ) {
		$termes = wp_list_pluck( get_the_terms( $minisite, 'sv_minisite_cat' ) ?: [], 'term_id' );
		$cibles = array_unique( array_intersect_key( $groupe_de, array_flip( $termes ) ) );
		if ( ! $cibles ) {
			$directs[] = $minisite;
		}
		foreach ( $cibles as $groupe ) {
			$groupes[ $groupe ][] = $minisite;
		}
	}

	if ( $directs ) {
		$html .= sv_minisites_liens( $directs );
	}
	foreach ( $sous as $enfant ) {
		if ( ! $groupes[ $enfant->term_id ] ) {
			continue; // sous-catégorie vide : pas de titre orphelin
		}
		$html .= sprintf(
			'<div class="sv-minisites-groupe"><h%1$d class="sv-minisites-groupe__titre">%2$s</h%1$d>%3$s</div>',
			min( 6, $niveau + 1 ),
			esc_html( $enfant->name ),
			sv_minisites_liens( $groupes[ $enfant->term_id ] )
		);
	}
	return $html . '</div></div>';
}
