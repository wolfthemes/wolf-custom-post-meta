/*!
 * %NAME% %VERSION%
 */
/* jshint -W062 */
/* global WolfCustomPostMetaJSParams */

var WolfCustomPostMeta = function( $ ) {

	'use strict';

	return {

		/**
		 * Init UI
		 */
		init : function () {
			this.views();
			this.likes();
		},

		/**
		 * Increment views meta count
		 */
		views : function() {

			if ( $( 'body' ).hasClass( 'single' ) ) {

				var $post = $( '[id^="post-"].post' ),
					postId = this.getPostId( $post ),
					data = {
					action: 'wolf_custom_post_meta_ajax_increment_views',
					postId : postId
				};

				$.post( WolfCustomPostMetaJSParams.ajaxUrl, data, function() {} );
			}
		},

		/**
		 * Increment likes meta count
		 */
		likes : function( postId ) {

			var _this = this,
				$item = $( '[id^="post-"].post' ),
				$this,
				postId;

			$item.each( function () {

				postId = _this.getPostId( $( this ) );

				if ( Cookies.get( WolfCustomPostMetaJSParams.themeSlug + '-w-likes-' + postId ) ) {
					$( this ).find( '.wolf-like-this' ).addClass( 'wolf-liked' );
				}
			} );

			$( document ).on( 'click', '.wolf-like-this', function( event ) {

				event.preventDefault();
				$this = $( this );
				postId = $( this ).data( 'post-id' );

				if ( $( this ).hasClass( 'wolf-liked' ) || Cookies.get( WolfCustomPostMetaJSParams.themeSlug + '-w-likes-' + postId ) ) {

					return; // post already liked by visitor

				} else {

					var data = {
						action: 'wolf_custom_post_meta_ajax_increment_likes',
						postId : postId
					};

					$.post( WolfCustomPostMetaJSParams.ajaxUrl , data, function( response ) {
						if ( response ) {

							Cookies.set( WolfCustomPostMetaJSParams.themeSlug + '-w-likes-' + postId, true )

							if ( $this.find( '.wolf-likes-count' ).length ) {
								$this.find( '.wolf-likes-count' ).html( response );
							}

							$this.addClass( 'wolf-liked' );
						}
					} );
				}
			} );
		},

		/**
		 * Get post ID
		 */
		getPostId : function ( $post ) {

			var postId;

			if ( $post.data( 'post-id' ) ) {

				postId = $post.data( 'post-id' );

			} else if ( $post.attr( 'id' ) ) {

				postId = $post.attr( 'id' ).replace( 'post-', '' );
			}

			return postId;
		}
	};

}( jQuery );

( function( $ ) {

	'use strict';

	$( document ).ready( function() {
		WolfCustomPostMeta.init();
	} );

} )( jQuery );