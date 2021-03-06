/**
 * Collection filter danielbachhuber/collection-filter.js https://gist.github.com/danielbachhuber/0e4ff7ad82ffc15ceacf
 *
 * @package Organize Media Library
 */

(function(){
	/**
	 * Create a new MediaLibraryTaxonomyFilter we later will instantiate
	 */
	var MediaLibraryOmlTaxonomyFilter = wp.media.view.AttachmentFilters.extend(
		{
			id: 'media-library-oml-taxonomy-filter',

			createFilters: function() {
				var filters = {};
				/* Formats the 'terms' we've included via wp_localize_script() */
				_.each(
					MediaLibraryOmlTaxonomyFilterData.terms || {},
					function( value, index ) {
						filters[ index ] = {
							text: value.name,
							props: {
								/* Change this: key needs to be the WP_Query var for the taxonomy */
								oml_folders: value.slug,
							}
						};
					}
				);
				filters.all = {
					/* Change this: use whatever default label you'd like */
					text: MediaLibraryOmlTaxonomyFilterDataText.all_folders,
					props: {
						/* Change this: key needs to be the WP_Query var for the taxonomy */
						oml_folders: ''
					},
					priority: 10
				};
				this.filters = filters;
			}
		}
	);
	/**
	 * Extend and override wp.media.view.AttachmentsBrowser to include our new filter
	 */
	var AttachmentsBrowser = wp.media.view.AttachmentsBrowser;
	wp.media.view.AttachmentsBrowser = wp.media.view.AttachmentsBrowser.extend(
		{
			createToolbar: function() {
				/* Make sure to load the original toolbar */
				AttachmentsBrowser.prototype.createToolbar.call( this );
				this.toolbar.set(
					'MediaLibraryOmlTaxonomyFilter',
					new MediaLibraryOmlTaxonomyFilter(
						{
							controller: this.controller,
							model:      this.collection.props,
							priority: -75
						}
					).render()
				);
			}
		}
	);
})()
