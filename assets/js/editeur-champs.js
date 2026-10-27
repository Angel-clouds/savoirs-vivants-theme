/**
 * Éditeur : panneau « Informations » dans la barre latérale (onglet Page/Article)
 * pour saisir les champs des événements et du matériel sans passer par les
 * « Champs personnalisés ». Aucun build nécessaire (wp.element.createElement).
 *
 * Événements : cases « Agendas » (termes sv_pole) en tête du panneau. L'enregistrement est
 * bloqué tant qu'aucun agenda n'est coché, et un avertissement signale une date absente ou passée
 * (l'événement n'apparaîtrait alors dans aucun agenda), ou une date de fin qui n'est pas après le début. La boîte « Pôles » d'origine est masquée.
 *
 * Événements : « Image d'illustration » (image mise en avant) choisie dans le même panneau, et bouton
 * « Aperçu de la carte » : fenêtre avec la vraie carte de l'agenda (ordinateur / mobile), rendue par
 * le site à partir des valeurs en cours de saisie (inc/contenus.php, sv_apercu_carte()).
 * La boîte « Image mise en avant » d'origine est masquée.
 */
( function ( wp ) {
	if ( ! wp || ! wp.plugins ) {
		return;
	}

	var el = wp.element.createElement;
	var Panel = ( wp.editor && wp.editor.PluginDocumentSettingPanel ) || ( wp.editPost && wp.editPost.PluginDocumentSettingPanel );
	var TextControl = wp.components.TextControl;
	var CheckboxControl = wp.components.CheckboxControl;
	var Notice = wp.components.Notice;
	var useEffect = wp.element.useEffect;
	var useSelect = wp.data.useSelect;
	var useDispatch = wp.data.useDispatch;
	var useEntityProp = wp.coreData.useEntityProp;
	var Button = wp.components.Button;
	var Modal = wp.components.Modal;
	var useState = wp.element.useState;
	var useRef = wp.element.useRef;
	var config = window.svChampsEditeur || {};
	var apercu = window.svApercuCarte || null;
	var VERROU = 'sv-agendas';

	function aujourdhui() {
		var d = new Date();
		return [ d.getFullYear(), ( '0' + ( d.getMonth() + 1 ) ).slice( -2 ), ( '0' + d.getDate() ).slice( -2 ) ].join( '-' );
	}

	function Agendas( props ) {
		var textes = props.textes;
		var termes = useSelect( function ( select ) {
			return select( 'core' ).getEntityRecords( 'taxonomy', 'sv_pole', { per_page: -1, orderby: 'id', order: 'asc', context: 'view' } );
		}, [] );
		var entite = useEntityProp( 'postType', 'sv_evenement', 'sv_pole' );
		var choisis = entite[ 0 ] || [];
		var setChoisis = entite[ 1 ];
		var editeur = useDispatch( 'core/editor' );

		useEffect( function () {
			if ( choisis.length ) {
				editeur.unlockPostSaving( VERROU );
			} else {
				editeur.lockPostSaving( VERROU );
			}
			return function () {
				editeur.unlockPostSaving( VERROU );
			};
		}, [ choisis.length ] );

		var avertissements = [];
		if ( ! choisis.length ) {
			avertissements.push( el( Notice, { key: 'agenda', status: 'error', isDismissible: false }, textes.manquant ) );
		}
		if ( ! props.date ) {
			avertissements.push( el( Notice, { key: 'date', status: 'warning', isDismissible: false }, textes.date_manquante ) );
		} else if ( ( props.dateFin > props.date ? props.dateFin : props.date ) < aujourdhui() ) {
			avertissements.push( el( Notice, { key: 'date', status: 'warning', isDismissible: false }, textes.date_passee ) );
		}
		if ( props.dateFin && props.date && props.dateFin <= props.date ) {
			avertissements.push( el( Notice, { key: 'fin', status: 'warning', isDismissible: false }, textes.fin_invalide ) );
		}

		// fieldset + legend : les cases sont annoncées comme un groupe (RGAA 11.5).
		return el(
			'div',
			{ className: 'sv-agendas' },
			el(
				'fieldset',
				null,
				el( 'legend', { className: 'components-base-control__label', style: { marginBottom: '8px' } }, textes.legende ),
				el( 'div', { style: { display: 'grid', gap: '12px' } }, ( termes || [] ).map( function ( terme ) {
					return el( CheckboxControl, {
						key: terme.id,
						label: terme.name,
						checked: choisis.indexOf( terme.id ) !== -1,
						__nextHasNoMarginBottom: true,
						onChange: function ( coche ) {
							setChoisis( coche ? choisis.concat( [ terme.id ] ) : choisis.filter( function ( id ) {
								return id !== terme.id;
							} ) );
						},
					} );
				} ) ),
				el( 'p', { className: 'components-base-control__help', style: { marginTop: '8px' } }, textes.aide )
			),
			avertissements
		);
	}


	/**
	 * Image d'illustration = image mise en avant (featured_media), choisie dans la médiathèque.
	 */
	function Illustration() {
		var textes = apercu.textes;
		var MediaUpload = wp.blockEditor && wp.blockEditor.MediaUpload;
		var MediaUploadCheck = wp.blockEditor && wp.blockEditor.MediaUploadCheck;
		var imageId = useSelect( function ( select ) {
			return select( 'core/editor' ).getEditedPostAttribute( 'featured_media' ) || 0;
		}, [] );
		var image = useSelect( function ( select ) {
			return imageId ? select( 'core' ).getMedia( imageId, { context: 'view' } ) : null;
		}, [ imageId ] );
		var editeur = useDispatch( 'core/editor' );
		var miniature = image && image.media_details && image.media_details.sizes && image.media_details.sizes.medium;
		var source = miniature ? miniature.source_url : ( image ? image.source_url : '' );

		if ( ! MediaUpload ) {
			return null;
		}

		function choisir( media ) {
			editeur.editPost( { featured_media: media.id } );
		}

		return el(
			'div',
			{ className: 'sv-illustration', style: { marginBottom: '16px' } },
			el( 'p', { className: 'components-base-control__label', style: { marginBottom: '8px' } }, textes.image ),
			source ? el( 'img', { src: source, alt: '', style: { display: 'block', width: '100%', maxHeight: '180px', objectFit: 'cover', objectPosition: 'top', marginBottom: '8px' } } ) : null,
			el(
				MediaUploadCheck,
				null,
				el( MediaUpload, {
					onSelect: choisir,
					allowedTypes: [ 'image' ],
					value: imageId,
					title: textes.image,
					render: function ( ouvrir ) {
						return el(
							'div',
							{ style: { display: 'flex', gap: '8px', flexWrap: 'wrap' } },
							el( Button, { variant: imageId ? 'secondary' : 'primary', onClick: ouvrir.open, __next40pxDefaultSize: true }, imageId ? textes.remplacer : textes.choisir ),
							imageId ? el( Button, { variant: 'tertiary', isDestructive: true, __next40pxDefaultSize: true, onClick: function () {
								editeur.editPost( { featured_media: 0 } );
							} }, textes.retirer ) : null
						);
					},
				} )
			),
			el( 'p', { className: 'components-base-control__help', style: { marginTop: '8px' } }, textes.image_aide )
		);
	}

	/**
	 * Envoie les valeurs en cours de saisie à la page d'aperçu (POST dans l'iframe nommée).
	 */
	function envoyerApercu( cible, valeurs ) {
		var formulaire = document.createElement( 'form' );
		formulaire.method = 'post';
		formulaire.action = apercu.url;
		formulaire.target = cible;
		formulaire.style.display = 'none';
		function champ( nom, valeur ) {
			var input = document.createElement( 'input' );
			input.type = 'hidden';
			input.name = nom;
			input.value = valeur == null ? '' : String( valeur );
			formulaire.appendChild( input );
		}
		Object.keys( valeurs ).forEach( function ( nom ) {
			if ( Array.isArray( valeurs[ nom ] ) ) {
				valeurs[ nom ].forEach( function ( v ) {
					champ( nom + '[]', v );
				} );
			} else {
				champ( nom, valeurs[ nom ] );
			}
		} );
		document.body.appendChild( formulaire );
		formulaire.submit();
		formulaire.remove();
	}

	/**
	 * Bouton « Aperçu de la carte » et fenêtre ordinateur / mobile.
	 */
	function ApercuCarte( props ) {
		var textes = apercu.textes;
		var ouvert = useState( false );
		var format = useState( 'ordinateur' );
		var hauteur = useState( 240 );
		var iframe = useRef( null );
		var donnees = useSelect( function ( select ) {
			var ed = select( 'core/editor' );
			return {
				id: ed.getCurrentPostId(),
				titre: ed.getEditedPostAttribute( 'title' ) || '',
				image: ed.getEditedPostAttribute( 'featured_media' ) || 0,
				poles: ed.getEditedPostAttribute( 'sv_pole' ) || [],
			};
		}, [] );
		var nom = 'sv-apercu-carte-' + donnees.id;

		useEffect( function () {
			if ( ! ouvert[ 0 ] ) {
				return;
			}
			var meta = props.meta;
			envoyerApercu( nom, {
				sv_apercu_carte: donnees.id,
				_wpnonce: apercu.nonce,
				titre: donnees.titre || textes.titre_manquant,
				image: donnees.image,
				poles: donnees.poles,
				sv_date: meta.sv_date,
				sv_date_fin: meta.sv_date_fin,
				sv_heure_debut: meta.sv_heure_debut,
				sv_heure_fin: meta.sv_heure_fin,
				sv_lieu: meta.sv_lieu,
			} );
		}, [ ouvert[ 0 ], format[ 0 ] ] );

		// Hauteur de l'iframe = hauteur de la carte, envoyée par la page d'aperçu (postMessage) :
		// l'éditeur ne peut pas toujours lire le document de l'iframe (isolation de l'éditeur).
		useEffect( function () {
			if ( ! ouvert[ 0 ] ) {
				return;
			}
			function recevoir( evenement ) {
				var donnee = evenement.data;
				if ( iframe.current && evenement.source === iframe.current.contentWindow && donnee && typeof donnee.svApercuHauteur === 'number' ) {
					hauteur[ 1 ]( Math.min( Math.max( donnee.svApercuHauteur, 120 ), 2000 ) );
				}
			}
			window.addEventListener( 'message', recevoir );
			return function () {
				window.removeEventListener( 'message', recevoir );
			};
		}, [ ouvert[ 0 ] ] );

		return el(
			'div',
			{ style: { marginTop: '16px' } },
			el( Button, { variant: 'secondary', onClick: function () {
				ouvert[ 1 ]( true );
			}, __next40pxDefaultSize: true }, textes.apercu ),
			ouvert[ 0 ] ? el(
				Modal,
				{ title: textes.apercu, size: 'fill', onRequestClose: function () {
					ouvert[ 1 ]( false );
				} },
				el( 'p', { style: { marginTop: 0 } }, textes.apercu_aide ),
				el(
					'div',
					{ role: 'group', 'aria-label': textes.apercu, style: { display: 'flex', gap: '8px', marginBottom: '16px' } },
					[ 'ordinateur', 'mobile' ].map( function ( f ) {
						return el( Button, {
							key: f,
							variant: format[ 0 ] === f ? 'primary' : 'secondary',
							'aria-pressed': format[ 0 ] === f,
							__next40pxDefaultSize: true,
							onClick: function () {
								format[ 1 ]( f );
							},
						}, textes[ f ] );
					} )
				),
				el(
					'div',
					{ style: { background: '#f0f0f0', padding: '16px', overflow: 'auto' } },
					el( 'iframe', {
						ref: iframe,
						name: nom,
						title: textes.titre_iframe,
						style: { display: 'block', margin: '0 auto', border: 0, background: '#fff', width: format[ 0 ] === 'mobile' ? '375px' : '100%', height: hauteur[ 0 ] + 'px' },
					} )
				)
			) : null
		);
	}

	function Champs() {
		var postType = useSelect( function ( select ) {
			return select( 'core/editor' ).getCurrentPostType();
		}, [] );
		var entite = useEntityProp( 'postType', postType, 'meta' );
		var meta = entite[ 0 ];
		var setMeta = entite[ 1 ];
		var reglages = config[ postType ];
		var avecImage = 'sv_evenement' === postType && !! apercu;

		// L'image d'illustration se choisit dans le panneau : la boîte « Image mise en avant » ferait doublon.
		useEffect( function () {
			if ( avecImage && wp.data.dispatch( 'core/editor' ).removeEditorPanel ) {
				wp.data.dispatch( 'core/editor' ).removeEditorPanel( 'featured-image' );
			}
		}, [ avecImage ] );

		if ( ! Panel || ! reglages || ! meta ) {
			return null;
		}

		return el(
			Panel,
			{ name: 'sv-champs', title: reglages.titre, initialOpen: true },
			reglages.agendas ? el( Agendas, { textes: reglages.agendas, date: meta.sv_date || '', dateFin: meta.sv_date_fin || '' } ) : null,
			avecImage ? el( Illustration ) : null,
			reglages.champs.map( function ( champ ) {
				return el( TextControl, {
					key: champ.cle,
					label: champ.label,
					help: champ.aide || '',
					type: champ.type || 'text',
					value: meta[ champ.cle ] || '',
					__nextHasNoMarginBottom: true,
					__next40pxDefaultSize: true,
					onChange: function ( valeur ) {
						var maj = {};
						maj[ champ.cle ] = valeur;
						setMeta( Object.assign( {}, meta, maj ) );
					},
				} );
			} ),
			avecImage ? el( ApercuCarte, { meta: meta } ) : null
		);
	}

	wp.plugins.registerPlugin( 'sv-champs', { render: Champs } );

	// Les agendas se choisissent dans le panneau ci-dessus : la boîte « Pôles » ferait doublon.
	if ( window.svChampsEditeur && window.svChampsEditeur.sv_evenement && wp.data.dispatch( 'core/editor' ).removeEditorPanel ) {
		wp.data.dispatch( 'core/editor' ).removeEditorPanel( 'taxonomy-panel-sv_pole' );
	}
} )( window.wp );
