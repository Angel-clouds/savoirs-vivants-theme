/**
 * Éditeur : panneau « Informations » dans la barre latérale (onglet Page/Article)
 * pour saisir les champs des événements et du matériel sans passer par les
 * « Champs personnalisés ». Aucun build nécessaire (wp.element.createElement).
 *
 * Événements : cases « Agendas » (termes sv_pole) en tête du panneau. L'enregistrement est
 * bloqué tant qu'aucun agenda n'est coché, et un avertissement signale une date absente ou passée
 * (l'événement n'apparaîtrait alors dans aucun agenda). La boîte « Pôles » d'origine est masquée.
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
	var config = window.svChampsEditeur || {};
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
		} else if ( props.date < aujourdhui() ) {
			avertissements.push( el( Notice, { key: 'date', status: 'warning', isDismissible: false }, textes.date_passee ) );
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

	function Champs() {
		var postType = useSelect( function ( select ) {
			return select( 'core/editor' ).getCurrentPostType();
		}, [] );
		var entite = useEntityProp( 'postType', postType, 'meta' );
		var meta = entite[ 0 ];
		var setMeta = entite[ 1 ];
		var reglages = config[ postType ];

		if ( ! Panel || ! reglages || ! meta ) {
			return null;
		}

		return el(
			Panel,
			{ name: 'sv-champs', title: reglages.titre, initialOpen: true },
			reglages.agendas ? el( Agendas, { textes: reglages.agendas, date: meta.sv_date || '' } ) : null,
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
			} )
		);
	}

	wp.plugins.registerPlugin( 'sv-champs', { render: Champs } );

	// Les agendas se choisissent dans le panneau ci-dessus : la boîte « Pôles » ferait doublon.
	if ( window.svChampsEditeur && window.svChampsEditeur.sv_evenement && wp.data.dispatch( 'core/editor' ).removeEditorPanel ) {
		wp.data.dispatch( 'core/editor' ).removeEditorPanel( 'taxonomy-panel-sv_pole' );
	}
} )( window.wp );
