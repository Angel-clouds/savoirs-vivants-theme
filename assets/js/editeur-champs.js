/**
 * Éditeur : panneau « Informations » dans la barre latérale (onglet Page/Article)
 * pour saisir les champs des événements et du matériel sans passer par les
 * « Champs personnalisés ». Aucun build nécessaire (wp.element.createElement).
 */
( function ( wp ) {
	if ( ! wp || ! wp.plugins ) {
		return;
	}

	var el = wp.element.createElement;
	var Panel = ( wp.editor && wp.editor.PluginDocumentSettingPanel ) || ( wp.editPost && wp.editPost.PluginDocumentSettingPanel );
	var TextControl = wp.components.TextControl;
	var useSelect = wp.data.useSelect;
	var useEntityProp = wp.coreData.useEntityProp;
	var config = window.svChampsEditeur || {};

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
} )( window.wp );
