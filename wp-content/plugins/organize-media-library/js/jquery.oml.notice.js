/**
 * Organize Media Library by Folders
 *
 * @package    Organize Media Library
 * @subpackage jquery.oml.notice.js
/*
	Copyright (c) 2020- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; version 2 of the License.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

jQuery(
	function($){
		$( document ).on(
			'click',
			'#Notice_Dismiss',
			function () {
				var update_version  = $( '[name="notice_update_version"]' ).val();
				$( '.notice' ).remove();
				$.ajax(
					{
						type: 'POST',
						dataType: 'json',
						url: oml_nt.ajax_url,
						data: {
							'action': oml_nt.action,
							'nonce': oml_nt.nonce,
							'uid': oml_nt.uid,
							'version': update_version
						},
						async: true
					}
				).done(
					function(callback){
						/* console.log(callback); */
						/* console.log(callback[0]); */
					}
				).fail(
					function(XMLHttpRequest, textStatus, errorThrown){
						/* console.log( XMLHttpRequest.status ); */
						/* console.log( textStatus ); */
						/* console.log( errorThrown.message ); */
					}
				);
			}
		);
	}
);
