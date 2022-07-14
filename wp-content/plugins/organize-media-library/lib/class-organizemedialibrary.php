<?php
/**
 * Organize Media Library by Folders
 *
 * @package    Organize Media Library
 * @subpackage OrganizeMediaLibrary Main Functions
/*
	Copyright (c) 2015- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
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

$organizemedialibrary = new OrganizeMediaLibrary();

/** ==================================================
 * Main Functions
 */
class OrganizeMediaLibrary {

	/** ==================================================
	 * Path
	 *
	 * @var $upload_dir  upload_dir.
	 */
	public $upload_dir;

	/** ==================================================
	 * Path
	 *
	 * @var $upload_url  upload_url.
	 */
	public $upload_url;

	/** ==================================================
	 * Path
	 *
	 * @var $upload_path  upload_path.
	 */
	public $upload_path;

	/** ==================================================
	 * Construct
	 *
	 * @since 6.20
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'oml_folders_taxonomies' ), 10 );
		$action = 'oml_notice';
		add_action( 'wp_ajax_' . $action, array( $this, 'oml_notice_callback' ) );

		/* original hook */
		add_action( 'oml_dirs_tree', array( $this, 'oml_dirs' ), 10, 2 );
		add_action( 'oml_folders_term_create', array( $this, 'oml_folders_term' ) );
		add_filter( 'oml_folder_move_regist', array( $this, 'regist' ), 10, 4 );
		add_filter( 'oml_mb_encode_multibyte', array( $this, 'mb_encode_multibyte' ), 10, 2 );
		add_filter( 'oml_mb_utf8', array( $this, 'mb_utf8' ), 10, 2 );
		add_filter( 'oml_dir_selectbox', array( $this, 'dir_selectbox' ), 20, 3 );

		list( $this->upload_dir, $this->upload_url, $this->upload_path ) = $this->upload_dir_url_path();

	}

	/** ==================================================
	 * Regist
	 *
	 * @param int    $re_id_attache  re_id_attache.
	 * @param string $target_folder  target_folder.
	 * @param string $character_code  character_code.
	 * @param int    $uid  user ID.
	 * @since 1.00
	 */
	public function regist( $re_id_attache, $target_folder, $character_code, $uid ) {

		$target_folder = urldecode( $target_folder );
		if ( '/' <> $target_folder ) {
			$target_folder = trailingslashit( $target_folder );
		}
		$file = get_post_meta( $re_id_attache, '_wp_attached_file', true );
		$filename = wp_basename( $file );
		$current_folder = '/' . str_replace( $filename, '', $file );
		$exts = explode( '.', $filename );
		$ext = end( $exts );

		if ( $target_folder === $current_folder ) {
			/* translators: Error message */
			return sprintf( __( '%1$s cannot be moved. The folder name is the same.', 'organize-media-library' ), $filename );
		}

		$re_attache = get_post( $re_id_attache );
		$new_attach_title = $re_attache->post_title;

		$current_file = $this->mb_encode_multibyte( $this->upload_dir . $current_folder . $filename, $character_code );
		$target_file = $this->mb_encode_multibyte( $this->upload_dir . $target_folder . $filename, $character_code );
		if ( file_exists( $current_file ) ) {
			$err_copy1 = @copy( $current_file, $target_file );
			if ( ! $err_copy1 ) {
				/* translators: Error message */
				return sprintf( __( '%1$s: Failed a copy from %2$s to %3$s.', 'organize-media-library' ), $new_attach_title, wp_normalize_path( $this->mb_utf8( $current_file, $character_code ) ), wp_normalize_path( $this->mb_utf8( $target_file, $character_code ) ) );
			}
			unlink( $current_file );
		}

		update_post_meta( $re_id_attache, '_wp_attached_file', ltrim( $target_folder . $filename, '/' ) );

		if ( 'image' === wp_ext2type( $ext ) || 'pdf' === strtolower( $ext ) ) {

			$metadata = wp_get_attachment_metadata( $re_id_attache );

			if ( ! empty( $metadata ) ) {
				foreach ( (array) $metadata as $key1 => $key2 ) {
					if ( 'sizes' === $key1 ) {
						foreach ( $metadata[ $key1 ] as $key2 => $key3 ) {
							$current_thumb_file = $this->mb_encode_multibyte( $this->upload_dir . $current_folder . $metadata['sizes'][ $key2 ]['file'], $character_code );
							$target_thumb_file = $this->mb_encode_multibyte( $this->upload_dir . $target_folder . $metadata['sizes'][ $key2 ]['file'], $character_code );
							if ( file_exists( $current_thumb_file ) ) {
								$err_copy2 = @copy( $current_thumb_file, $target_thumb_file );
								if ( ! $err_copy2 ) {
									/* translators: Error message */
									return sprintf( __( '%1$s: Failed a copy from %2$s to %3$s.', 'organize-media-library' ), $new_attach_title, wp_normalize_path( $this->mb_utf8( $current_thumb_file, $character_code ) ), wp_normalize_path( $this->mb_utf8( $target_thumb_file, $character_code ) ) );
								}
								unlink( $current_thumb_file );
							}
						}
					}
				}
				$metadata['file'] = ltrim( $target_folder . $filename, '/' );
				update_post_meta( $re_id_attache, '_wp_attachment_metadata', $metadata );
				if ( ! empty( $metadata['original_image'] ) ) {
					$current_org_file = $this->mb_encode_multibyte( $this->upload_dir . $current_folder . $metadata['original_image'], $character_code );
					$target_org_file = $this->mb_encode_multibyte( $this->upload_dir . $target_folder . $metadata['original_image'], $character_code );
					if ( file_exists( $current_org_file ) ) {
						$err_copy3 = @copy( $current_org_file, $target_org_file );
						if ( ! $err_copy3 ) {
							/* translators: Error message */
							return sprintf( __( '%1$s: Failed a copy from %2$s to %3$s.', 'organize-media-library' ), $new_attach_title, wp_normalize_path( $this->mb_utf8( $current_org_file, $character_code ) ), wp_normalize_path( $this->mb_utf8( $target_org_file, $character_code ) ) );
						}
						unlink( $current_org_file );
					}
				}
			}
		}

		$url_attach = $this->upload_url . $current_folder . $filename;
		$new_url_attach = $this->upload_url . $target_folder . $filename;

		global $wpdb;
		/* Change DB contents */
		$search_url = str_replace( '.' . $ext, '', $url_attach );
		$replace_url = str_replace( '.' . $ext, '', $new_url_attach );

		/* Replace */
		$wpdb->query(
			$wpdb->prepare(
				"
				UPDATE {$wpdb->prefix}posts
				SET post_content = replace( post_content, %s, %s )
				",
				$search_url,
				$replace_url
			)
		);

		/* Change DB Attachement post guid */
		$update_array = array(
			'guid' => $new_url_attach,
		);
		$id_array = array(
			'ID' => $re_id_attache,
		);
		$wpdb->show_errors();
		$wpdb->update( $wpdb->prefix . 'posts', $update_array, $id_array, array( '%s' ), array( '%d' ) );
		if ( '' !== $wpdb->last_error ) {
			$message = $wpdb->print_error();
		} else {
			$message = 'success';
		}

		return $message;

	}

	/** ==================================================
	 * Scan directory
	 *
	 * @param string $dir  dir.
	 * @param int    $uid  uid.
	 * @return array $dirlist
	 * @since 1.00
	 */
	private function scan_dir( $dir, $uid ) {

		$organizemedialibrary_settings = get_user_option( 'organizemedialibrary', $uid );

		$excludedir = '/^(?!.*(media-from-ftp-tmp|bulk-media-register-tmp'; /* tmp dir for Media from FTP and Bulk Media Register */
		global $blog_id;
		if ( is_multisite() && is_main_site( $blog_id ) ) {
			$excludedir .= '|\/sites\/';
		}
		if ( ! empty( $organizemedialibrary_settings['exclude_folders'] ) ) {
			$excludes = explode( '|', $organizemedialibrary_settings['exclude_folders'] );
			foreach ( $excludes as $value ) {
				$excludedir .= '|' . str_replace( '/', '\/', $value );
			}
		}
		$excludedir .= ')).*$/';

		$iterator = new RecursiveDirectoryIterator(
			$dir,
			FilesystemIterator::CURRENT_AS_FILEINFO |
			FilesystemIterator::KEY_AS_PATHNAME |
			FilesystemIterator::SKIP_DOTS
		);
		$iterator = new RecursiveIteratorIterator(
			$iterator,
			RecursiveIteratorIterator::SELF_FIRST
		);

		$iterator = new RegexIterator( $iterator, $excludedir, RecursiveRegexIterator::MATCH );

		$wordpress_path = wp_normalize_path( ABSPATH );
		$character_code = $organizemedialibrary_settings['character_code'];
		$list = array();
		if ( ! empty( $iterator ) ) {
			$count = 0;
			foreach ( $iterator as $fileinfo ) {
				if ( $fileinfo->isDir() ) {
					$dir = $fileinfo->getPathname();
					if ( strstr( $dir, $wordpress_path ) ) {
						$direnc = $this->mb_utf8( str_replace( $wordpress_path, '', $dir ), $character_code );
						$direnc = str_replace( $this->upload_path, '', $direnc );
					} else {
						$direnc = $this->mb_utf8( str_replace( $this->upload_dir, '', $dir ), $character_code );
					}
					++$count;
					$slug = 'oml-' . $count;
					$list[ $slug ] = array(
						'name' => $direnc,
					);
				}
			}
		}

		asort( $list );
		$last_list['terms'] = $list;

		return $last_list;

	}

	/** ==================================================
	 * Directory set
	 *
	 * @param int  $uid  uid.
	 * @param bool $forced  forced.
	 * @since 7.00
	 */
	public function oml_dirs( $uid, $forced ) {

		if ( $forced ) {
			$dirs = wp_json_encode( $this->scan_dir( $this->upload_dir, $uid ), JSON_UNESCAPED_UNICODE );
			update_option( 'oml_dirs', $dirs );
		} else {
			if ( ! get_option( 'oml_dirs' ) ) {
				$dirs = wp_json_encode( $this->scan_dir( $this->upload_dir, $uid ), JSON_UNESCAPED_UNICODE );
				update_option( 'oml_dirs', $dirs );
			}
		}

	}

	/** ==================================================
	 * Register Taxonomy
	 *
	 * @since 6.00
	 */
	public function oml_folders_taxonomies() {

		$args = array(
			'hierarchical'          => false,
			'label'                 => __( 'Folder', 'organize-media-library' ),
			'show_ui'               => false,
			'show_admin_column'     => false,
			'update_count_callback' => '_update_generic_term_count',
			'query_var'             => true,
			'rewrite'               => true,
		);

		register_taxonomy( 'oml_folders', 'attachment', $args );

	}

	/** ==================================================
	 * Register Media Folder Term
	 *
	 * @since 6.00
	 */
	public function oml_folders_term() {

		$dirs = get_option( 'oml_dirs' );
		$oml_dirs = json_decode( $dirs, true );
		if ( ! $oml_dirs ) {
			return;
		}

		/* term insert or update */
		foreach ( $oml_dirs['terms'] as $key => $value ) {
			$insert_term_args = array(
				'slug' => $key,
			);
			$term = get_term_by( 'slug', $key, 'oml_folders' );
			$folder_name = wp_normalize_path( $value['name'] );
			if ( ! term_exists( $key, 'oml_folders' ) ) {
				$insert_term_args = array(
					'slug' => $key,
				);
				wp_insert_term( $folder_name, 'oml_folders', $insert_term_args );
			} else {
				$update_term_args = array(
					'slug' => $key,
					'name' => $folder_name,
				);
				wp_update_term( $term->term_id, 'oml_folders', $update_term_args );
			}
		}
		$term = get_term_by( 'slug', 'oml', 'oml_folders' );
		if ( ! term_exists( 'oml', 'oml_folders' ) ) {
			$insert_term_args = array(
				'slug' => 'oml',
			);
			wp_insert_term( wp_normalize_path( '/' ), 'oml_folders', $insert_term_args );
		} else {
			$update_term_args = array(
				'slug' => 'oml',
				'name' => wp_normalize_path( '/' ),
			);
			wp_update_term( $term->term_id, 'oml_folders', $update_term_args );
		}

		/* Delete terms that are not related to folders */
		$args = array(
			'taxonomy'   => 'oml_folders',
			'hide_empty' => false,
			'search'     => 'oml-',
		);
		$terms = get_terms( $args );
		if ( ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				if ( ! array_key_exists( $term->slug, $oml_dirs['terms'] ) ) {
					wp_delete_term( $term->term_id, 'oml_folders' );
				}
			}
		}

		/* term relationships for post id */
		global $wpdb;
		$attachments_meta = $wpdb->get_results(
			"
				SELECT	post_id, meta_value
				FROM	{$wpdb->prefix}postmeta
				WHERE	meta_key = '_wp_attached_file'
			"
		);
		foreach ( $attachments_meta as $attachment ) {
			$filename = wp_basename( $attachment->meta_value );
			$foldername = '/' . untrailingslashit( str_replace( $filename, '', $attachment->meta_value ) );
			$terms = get_term_by( 'name', $foldername, 'oml_folders' );
			if ( $terms ) {
				$term_taxonomy_ids = wp_set_object_terms( $attachment->post_id, $terms->term_id, 'oml_folders' );
				if ( is_wp_error( $term_taxonomy_ids ) ) {
					$error = $attachment->meta_value;
				}
			} else {
				wp_delete_object_term_relationships( $attachment->post_id, 'oml_folders' );
			}
		}

	}

	/** ==================================================
	 * Directory select box
	 *
	 * @param string $searchdir  searchdir.
	 * @param string $character_code  character_code.
	 * @param int    $uid  uid.
	 * @return string $linkselectbox
	 * @since 3.00
	 */
	public function dir_selectbox( $searchdir, $character_code, $uid ) {

		do_action( 'oml_dirs_tree', $uid, false );
		$dirs = get_option( 'oml_dirs' );
		$oml_dirs = json_decode( $dirs, true );
		if ( ! $oml_dirs ) {
			return;
		}

		$linkselectbox = null;
		foreach ( $oml_dirs['terms'] as $key => $value ) {
			$term_name = get_term_by( 'slug', $key, 'oml_folders' )->name;
			$folder_name = wp_normalize_path( $value['name'] );
			if ( $searchdir === $folder_name ) {
				$linkdirs = '<option value="' . $key . '" selected>' . $folder_name . '</option>';
			} else {
				$linkdirs = '<option value="' . $key . '">' . $folder_name . '</option>';
			}
			$linkselectbox = $linkselectbox . $linkdirs;
		}
		if ( '/' === $searchdir ) {
			$linkdirs = '<option value="oml" selected>/</option>';
		} else {
			$linkdirs = '<option value="oml">/</option>';
		}
		$linkselectbox = $linkselectbox . $linkdirs;

		return $linkselectbox;

	}

	/** ==================================================
	 * Real Url
	 *
	 * @param  string $base  base.
	 * @param  string $relationalpath relationalpath.
	 * @return string $realurl realurl.
	 * @since  1.00
	 */
	private function realurl( $base, $relationalpath ) {

		$parse = array(
			'scheme'   => null,
			'user'     => null,
			'pass'     => null,
			'host'     => null,
			'port'     => null,
			'query'    => null,
			'fragment' => null,
		);
		$parse = wp_parse_url( $base );

		if ( strpos( $parse['path'], '/', ( strlen( $parse['path'] ) - 1 ) ) !== false ) {
			$parse['path'] .= '.';
		}

		if ( preg_match( '#^https?://#', $relationalpath ) ) {
			return $relationalpath;
		} elseif ( preg_match( '#^/.*$#', $relationalpath ) ) {
			return $parse['scheme'] . '://' . $parse['host'] . $relationalpath;
		} else {
			$base_path = explode( '/', dirname( $parse['path'] ) );
			$rel_path  = explode( '/', $relationalpath );
			foreach ( $rel_path as $rel_dir_name ) {
				if ( '.' === $rel_dir_name ) {
					array_shift( $base_path );
					array_unshift( $base_path, '' );
				} elseif ( '..' === $rel_dir_name ) {
					array_pop( $base_path );
					if ( count( $base_path ) === 0 ) {
						$base_path = array( '' );
					}
				} else {
					array_push( $base_path, $rel_dir_name );
				}
			}
			$path = implode( '/', $base_path );
			return $parse['scheme'] . '://' . $parse['host'] . $path;
		}

	}

	/** ==================================================
	 * Upload Path
	 *
	 * @return array $upload_dir,$upload_url,$upload_path  uploadpath.
	 * @since 1.00
	 */
	private function upload_dir_url_path() {

		$wp_uploads = wp_upload_dir();

		$relation_path_true = strpos( $wp_uploads['baseurl'], '../' );
		if ( $relation_path_true > 0 ) {
			$relationalpath = substr( $wp_uploads['baseurl'], $relation_path_true );
			$basepath       = substr( $wp_uploads['baseurl'], 0, $relation_path_true );
			$upload_url     = $this->realurl( $basepath, $relationalpath );
			$upload_dir     = wp_normalize_path( realpath( $wp_uploads['basedir'] ) );
		} else {
			$upload_url = $wp_uploads['baseurl'];
			$upload_dir = wp_normalize_path( $wp_uploads['basedir'] );
		}

		if ( is_ssl() ) {
			$upload_url = str_replace( 'http:', 'https:', $upload_url );
		}

		if ( $relation_path_true > 0 ) {
			$upload_path = $relationalpath;
		} else {
			$upload_path = str_replace( site_url( '/' ), '', $upload_url );
		}

		$upload_dir  = untrailingslashit( $upload_dir );
		$upload_url  = untrailingslashit( $upload_url );
		$upload_path = untrailingslashit( $upload_path );

		return array( $upload_dir, $upload_url, $upload_path );

	}

	/** ==================================================
	 * Multibyte Convert
	 *
	 * @param string $str  str.
	 * @param string $character_code  character_code.
	 * @return string $str
	 * @since 5.10
	 */
	public function mb_encode_multibyte( $str, $character_code ) {

		if ( function_exists( 'mb_language' ) && 'none' <> $character_code ) {
			$encoding = implode( ',', mb_list_encodings() );
			$str = mb_convert_encoding( $str, $character_code, $encoding );
		}

		return $str;

	}

	/** ==================================================
	 * Multibyte UTF-8
	 *
	 * @param string $str  str.
	 * @param string $character_code  character_code.
	 * @return string $str
	 * @since 5.10
	 */
	public function mb_utf8( $str, $character_code ) {

		if ( function_exists( 'mb_convert_encoding' ) && 'none' <> $character_code ) {
			$encoding = implode( ',', mb_list_encodings() );
			$str = mb_convert_encoding( $str, 'UTF-8', $encoding );
		}

		return $str;

	}

	/** ==================================================
	 * Notice Callback
	 *
	 * @since 7.10
	 */
	public function oml_notice_callback() {

		$action = 'oml_notice';
		if ( check_ajax_referer( $action, 'nonce', false ) ) {
			if ( ! empty( $_POST['version'] ) && ! empty( $_POST['uid'] ) ) {
				$uid = absint( $_POST['uid'] );
				do_action( 'oml_dirs_tree', $uid, true );
				do_action( 'oml_folders_term_create' );
				$version = sanitize_text_field( wp_unslash( $_POST['version'] ) );
				update_user_option( $uid, 'oml_notice', $version );
			}
		}

		wp_die();

	}

}


