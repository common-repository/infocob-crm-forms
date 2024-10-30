import { registerBlockType } from '@wordpress/blocks';
import { withSelect } from '@wordpress/data';
import { SelectControl } from '@wordpress/components';

registerBlockType( 'infocob-crm-forms/liste-formulaires', {
    title: 'Infocob CRM Forms',
    icon: {
        background: '#e85a0c',
        foreground: '#ffffff',
        src: 'list-view'
    },
    category: 'infocob-gutenberg-category',

    /**
     * Object with all binding elements between the view HTML and the functions
     * It lets you bind data from DOM elements and storage attributes
     */
    attributes: {
        // Number 1
        // It doesn't use source attribute, so it doesn't come from save() rendered DOM
        // They'll be saved on the block's source code as a JSON
        form_id: {
            type: 'text'
        },
    },

    edit: withSelect( ( select, props ) => {
        return {
            posts: select( 'core' ).getEntityRecords( 'postType', 'ifb_crm_forms', {per_page: -1} ),
        };
    } )( (  props  ) => {

        function onChangeId(value) {
            props.setAttributes({form_id: value})
        }

        if ( ! props || !props.posts ) {
            return 'Loading...';
        }

        if ( props.posts && props.posts.length === 0 ) {
            return 'No posts';
        }

        var posts = props.posts;

        var options = [];
        options.push({ label: '', value: null })
        posts.forEach((post) => {
            options.push({label: post.title.rendered, value: post.id});
        });

        return <SelectControl label="Formulaire " value={ props.attributes.form_id } options={ options } onChange={onChangeId} />;
    } ),
    save: ( (props) => {
        return props.attributes.form_id;
    })
} );
