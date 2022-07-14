<?php
/**
 * Organize Media Library by Folders
 *
 * @package    Organize Media Library
 * @subpackage OrganizeMediaLibraryAdmin Main & Management screen
/*
	Copyright (c) 2013- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
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

$organizemedialibraryadmin = new OrganizeMediaLibraryAdmin();

/** ==================================================
 * Management screen
 */
class OrganizeMediaLibraryAdmin {

	/** ==================================================
	 * Construct
	 *
	 * @since 6.20
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'register_settings' ), 10 );
		add_filter( 'plugin_action_links', array( $this, 'settings_link' ), 10, 2 );
		add_action( 'admin_menu', array( $this, 'add_pages' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_custom_wp_admin_style' ) );
		add_filter( 'manage_media_columns', array( $this, 'muc_column' ) );
		add_action( 'manage_media_custom_column', array( $this, 'muc_value' ), 12, 2 );
		add_action( 'restrict_manage_posts', array( $this, 'add_folder_filter' ), 13 );
		add_action( 'admin_footer', array( $this, 'custom_bulk_admin_footer' ) );
		add_action( 'load-upload.php', array( $this, 'custom_bulk_action' ) );
		add_action( 'admin_notices', array( $this, 'notices' ) );
		add_action( 'admin_notices', array( $this, 'custom_bulk_admin_notices' ) );
		add_action( 'admin_notices', array( $this, 'update_notice' ) );
		add_action( 'wp_enqueue_media', array( $this, 'insert_media_custom_filter' ) );

	}

	/** ==================================================
	 * Add a "Settings" link to the plugins page
	 *
	 * @param  array  $links  links array.
	 * @param  string $file   file.
	 * @return array  $links  links array.
	 * @since 1.00
	 */
	public function settings_link( $links, $file ) {
		static $this_plugin;
		if ( empty( $this_plugin ) ) {
			$this_plugin = 'organize-media-library/organizemedialibrary.php';
		}
		if ( $file == $this_plugin ) {
			$links[] = '<a href="' . admin_url( 'upload.php?page=organizemedialibrary-settings' ) . '">' . __( 'Settings' ) . '</a>';
		}
			return $links;
	}

	/** ==================================================
	 * Settings page
	 *
	 * @since 1.00
	 */
	public function add_pages() {
		add_media_page(
			__( 'Folder', 'organize-media-library' ) . ' ' . __( 'Settings' ),
			__( 'Folder', 'organize-media-library' ) . ' ' . __( 'Settings' ),
			'upload_files',
			'organizemedialibrary-settings',
			array( $this, 'settings_page' )
		);

	}

	/** ==================================================
	 * Add Css and Script
	 *
	 * @since 2.23
	 */
	public function load_custom_wp_admin_style() {
		if ( $this->is_my_plugin_screen() ) {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'organizemedialibrary-js', plugin_dir_url( __DIR__ ) . 'js/jquery.organizemedialibrary.js', array( 'jquery' ), '1.00', false );
			$handle = 'oml-notice-js';
			$action = 'oml_notice';
			wp_enqueue_script( $handle, plugin_dir_url( __DIR__ ) . 'js/jquery.oml.notice.js', array( 'jquery' ), '1.00', false );
			wp_localize_script(
				$handle,
				'oml_nt',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'action'   => $action,
					'nonce'    => wp_create_nonce( $action ),
					'uid'      => get_current_user_id(),
				)
			);

		}
	}

	/** ==================================================
	 * For only admin style
	 *
	 * @since 4.30
	 */
	private function is_my_plugin_screen() {
		$screen = get_current_screen();
		if ( is_object( $screen ) && 'media_page_organizemedialibrary-settings' == $screen->id ) {
			return true;
		} else if ( is_object( $screen ) && 'upload' == $screen->id ) {
			return true;
		} else {
			return false;
		}
	}

	/** ==================================================
	 * Sub Menu
	 */
	public function settings_page() {

		if ( ! current_user_can( 'upload_files' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.' ) );
		}

		$this->options_updated();

		$organizemedialibrary_settings = get_user_option( 'organizemedialibrary', get_current_user_id() );
		$scriptname = admin_url( 'upload.php?page=organizemedialibrary-settings' );

		$plugin_datas = get_file_data( plugin_dir_path( __DIR__ ) . 'organizemedialibrary.php', array( 'version' => 'Version' ) );
		$plugin_version = __( 'Version:' ) . ' ' . $plugin_datas['version'];

		?>
		<div class="wrap">

		<h2>Organize Media Library by Folders</h2>

		<details>
		<summary><strong><?php esc_html_e( 'Various links of this plugin', 'organize-media-library' ); ?></strong></summary>
		<?php $this->credit(); ?>
		</details>

		<h3><?php esc_html_e( 'Folder', 'organize-media-library' ); ?> <?php esc_html_e( 'Settings' ); ?></h3>

			<form method="post" action="<?php echo esc_url( $scriptname ); ?>">
			<?php wp_nonce_field( 'oml_settings', 'organizemedialibrary_tabs' ); ?>

			<?php
			if ( function_exists( 'mb_check_encoding' ) ) {
				?>
				<details style="margin-bottom: 5px;">
				<summary style="cursor: pointer; padding: 10px; border: 1px solid #ddd; background: #f4f4f4; color: #000;"><strong><?php esc_html_e( 'Character Encodings for Server', 'organize-media-library' ); ?></strong></summary>
					<p><?php esc_html_e( 'It may receive an error if you are using a multi-byte name to the file or directory name. In that case, please change.', 'organize-media-library' ); ?></p>
					<select name="organizemedialibrary_character_code" style="width: 210px">
					<?php
					foreach ( mb_list_encodings() as $chrcode ) {
						if ( 'pass' <> $chrcode && 'auto' <> $chrcode ) {
							if ( $chrcode === $organizemedialibrary_settings['character_code'] ) {
								?>
									<option value="<?php echo esc_attr( $chrcode ); ?>" selected><?php echo esc_html( $chrcode ); ?></option>
									<?php
							} else {
								?>
									<option value="<?php echo esc_attr( $chrcode ); ?>"><?php echo esc_html( $chrcode ); ?></option>
									<?php
							}
						}
					}
					?>
					</select>
				</details>
				<?php
			}
			?>
			<details style="margin-bottom: 5px;">
			<summary style="cursor: pointer; padding: 10px; border: 1px solid #ddd; background: #f4f4f4; color: #000;"><strong><?php esc_html_e( 'Exclude folders', 'organize-media-library' ); ?></strong></summary>
				<p><?php esc_html_e( 'Exclude the folders that you do not want to be displayed.', 'organize-media-library' ); ?></p>
				<ol style="list-style-type: disc">
				<li><?php echo esc_html_e( 'Exclude leading and trailing slashes.', 'organize-media-library' ); ?></li>
				<li><?php echo esc_html( sprintf( __( 'For a single folder, specify the folder name.', 'organize-media-library' ), '|' ) ); ?>
					<ol style="list-style-type: disc">
					<li>
					<?php /* translators: %1$s folder */ ?>
					<?php echo esc_html( sprintf( __( 'Sample: Exclude %1$s.', 'organize-media-library' ), 'test/test2' ) ); ?> [<code>test/test2</code>]
					</li>
					</ol>
				</li>
				<li>
					<?php /* translators: sepalater */ ?>
					<?php echo esc_html( sprintf( __( 'If there are multiple folders, specify them by separating them with "%1$s".', 'organize-media-library' ), '|' ) ); ?>
					<ol style="list-style-type: disc">
					<li>
					<?php /* translators: %1$s %2$s folders */ ?>
					<?php echo esc_html( sprintf( __( 'Sample: Exclude %1$s and %2$s.', 'organize-media-library' ), 'test/test2', 'test3' ) ); ?> [<code>test/test2|test3</code>]
					</li>
					</ol>
				</li>
				</ol>
				<textarea name="exclude_folders" rows="3" style="width: 100%;"><?php echo esc_textarea( $organizemedialibrary_settings['exclude_folders'] ); ?></textarea>
				</details>
				<details style="margin-bottom: 5px;" open>
				<summary style="cursor: pointer; padding: 10px; border: 1px solid #ddd; background: #f4f4f4; color: #000;"><strong><?php esc_html_e( 'Make folder', 'organize-media-library' ); ?></strong></summary>
					<?php
					if ( class_exists( 'ExtendMediaUploadAdmin' ) && ! get_option( 'uploads_use_yearmonth_folders' ) ) {
						?>
						<input type="checkbox" name="emu_subdir_change" <?php checked( true, $organizemedialibrary_settings['emu_subdir_change'] ); ?>  value="1" > <?php esc_html_e( 'Make the created folder an upload folder', 'organize-media-library' ); ?>
						<?php
					}
					?>
					<p><?php esc_html_e( 'If you created or deleted a folder in another way, you can leave the field blank and press the following button to apply the changes.', 'organize-media-library' ); ?></p>
					<div style="display: block; padding:5px 5px;">
						<?php $organizemedialibrary = new OrganizeMediaLibrary(); ?>
						<code><?php echo esc_html( $organizemedialibrary->upload_path . '/' ); ?></code>
						<input type="text" name="newdir">
					</div>
				</details>
				<?php submit_button( __( 'Make folder', 'organize-media-library' ) . '&' . __( 'Save Changes' ), 'large', 'Submit', true ); ?>
				<p class="description">
				<?php esc_html_e( 'Note: This process takes a long time when there are a large number of files and folders, and the process may be interrupted by a timeout. In that case, increase the "max_execution_time" value specified in "php.ini".', 'organize-media-library' ); ?>
				</p>
			</form>

		</div>
		<?php

	}

	/** ==================================================
	 * Credit
	 *
	 * @since 1.00
	 */
	private function credit() {

		$plugin_name    = null;
		$plugin_ver_num = null;
		$plugin_path    = plugin_dir_path( __DIR__ );
		$plugin_dir     = untrailingslashit( $plugin_path );
		$slugs          = explode( '/', $plugin_dir );
		$slug           = end( $slugs );
		$files          = scandir( $plugin_dir );
		foreach ( $files as $file ) {
			if ( '.' === $file || '..' === $file || is_dir( $plugin_path . $file ) ) {
				continue;
			} else {
				$exts = explode( '.', $file );
				$ext  = strtolower( end( $exts ) );
				if ( 'php' === $ext ) {
					$plugin_datas = get_file_data(
						$plugin_path . $file,
						array(
							'name'    => 'Plugin Name',
							'version' => 'Version',
						)
					);
					if ( array_key_exists( 'name', $plugin_datas ) && ! empty( $plugin_datas['name'] ) && array_key_exists( 'version', $plugin_datas ) && ! empty( $plugin_datas['version'] ) ) {
						$plugin_name    = $plugin_datas['name'];
						$plugin_ver_num = $plugin_datas['version'];
						break;
					}
				}
			}
		}
		$plugin_version = __( 'Version:' ) . ' ' . $plugin_ver_num;
		/* translators: FAQ Link & Slug */
		$faq       = sprintf( esc_html__( 'https://wordpress.org/plugins/%s/faq', '%s' ), $slug );
		$support   = 'https://wordpress.org/support/plugin/' . $slug;
		$review    = 'https://wordpress.org/support/view/plugin-reviews/' . $slug;
		$translate = 'https://translate.wordpress.org/projects/wp-plugins/' . $slug;
		$facebook  = 'https://www.facebook.com/katsushikawamori/';
		$twitter   = 'https://twitter.com/dodesyo312';
		$youtube   = 'https://www.youtube.com/channel/UC5zTLeyROkvZm86OgNRcb_w';
		$donate    = sprintf( esc_html__( 'https://shop.riverforest-wp.info/donate/', '%s' ), $slug );

		?>
			<span style="font-weight: bold;">
			<div>
		<?php echo esc_html( $plugin_version ); ?> | 
			<a style="text-decoration: none;" href="<?php echo esc_url( $faq ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'FAQ' ); ?></a> | <a style="text-decoration: none;" href="<?php echo esc_url( $support ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Support Forums' ); ?></a> | <a style="text-decoration: none;" href="<?php echo esc_url( $review ); ?>" target="_blank" rel="noopener noreferrer"><?php sprintf( esc_html_e( 'Reviews', '%s' ), $slug ); ?></a>
			</div>
			<div>
			<a style="text-decoration: none;" href="<?php echo esc_url( $translate ); ?>" target="_blank" rel="noopener noreferrer">
			<?php
			/* translators: Plugin translation link */
			echo sprintf( esc_html__( 'Translations for %s' ), esc_html( $plugin_name ) );
			?>
			</a> | <a style="text-decoration: none;" href="<?php echo esc_url( $facebook ); ?>" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-facebook"></span></a> | <a style="text-decoration: none;" href="<?php echo esc_url( $twitter ); ?>" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-twitter"></span></a> | <a style="text-decoration: none;" href="<?php echo esc_url( $youtube ); ?>" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-video-alt3"></span></a>
			</div>
			</span>

			<div style="width: 250px; height: 180px; margin: 5px; padding: 5px; border: #CCC 2px solid;">
			<h3><?php sprintf( esc_html_e( 'Please make a donation if you like my work or would like to further the development of this plugin.', '%s' ), $slug ); ?></h3>
			<div style="text-align: right; margin: 5px; padding: 5px;"><span style="padding: 3px; color: #ffffff; background-color: #008000">Plugin Author</span> <span style="font-weight: bold;">Katsushi Kawamori</span></div>
			<button type="button" style="margin: 5px; padding: 5px;" onclick="window.open('<?php echo esc_url( $donate ); ?>')"><?php esc_html_e( 'Donate to this plugin &#187;' ); ?></button>
			</div>

			<?php

	}

	/** ==================================================
	 * Settings register
	 *
	 * @since 1.00
	 */
	public function register_settings() {

		/* Old option 6.49 -> New option 6.50 */
		if ( get_option( 'organizemedialibrary_settings_' . get_current_user_id() ) ) {
			update_user_option( get_current_user_id(), 'organizemedialibrary', get_option( 'organizemedialibrary_settings_' . get_current_user_id() ) );
			delete_option( 'organizemedialibrary_settings_' . get_current_user_id() );
		}

		/* Delete unnecessary option for Ver 7.21 */
		if ( get_option( 'testoml' ) ) {
			delete_option( 'testoml' );
		}

		if ( strtoupper( substr( PHP_OS, 0, 3 ) ) === 'WIN' && get_locale() === 'ja' ) { /* Japanese Windows */
			$character_code = 'CP932';
		} else {
			$character_code = 'UTF-8';
		}

		if ( ! get_user_option( 'organizemedialibrary', get_current_user_id() ) ) {
			$organizemedialibrary_tbl = array(
				'character_code' => $character_code,
				'exclude_folders' => null,
				'emu_subdir_change' => true,
			);
			update_user_option( get_current_user_id(), 'organizemedialibrary', $organizemedialibrary_tbl );
		} else { /* Before version 7.00 */
			$organizemedialibrary_settings = get_user_option( 'organizemedialibrary', get_current_user_id() );
			if ( array_key_exists( 'dirs', $organizemedialibrary_settings ) ) {
				unset( $organizemedialibrary_settings['dirs'] );
			}
			update_user_option( get_current_user_id(), 'organizemedialibrary', $organizemedialibrary_settings );
		}

		/* for notice */
		if ( ! get_user_option( 'oml_notice', get_current_user_id() ) ) {
			update_user_option( get_current_user_id(), 'oml_notice', 7.09 );
		}

		/* Delete term Old 6.52 -> New 7.00 */
		global $wpdb;
		$wp_terms = $wpdb->get_results(
			"
			SELECT term_id
			FROM {$wpdb->prefix}term_taxonomy
			WHERE taxonomy = 'media_folder'
			"
		);
		if ( ! empty( $wp_terms ) ) {
			foreach ( $wp_terms as $value ) {
				$where_format = array( '%d' );
				$delete_line = array( 'term_id' => $value->term_id );
				$delete_line_relationships = array( 'term_taxonomy_id' => $value->term_id );
				$wpdb->delete( $wpdb->prefix . 'terms', $delete_line, $where_format );
				$wpdb->delete( $wpdb->prefix . 'term_taxonomy', $delete_line, $where_format );
				$wpdb->delete( $wpdb->prefix . 'term_relationships', $delete_line_relationships, $where_format );
			}
		}

	}

	/** ==================================================
	 * Update wp_options table
	 *
	 * @since 1.00
	 */
	private function options_updated() {

		$post_nonce_field = 'organizemedialibrary_tabs';
		if ( isset( $_POST[ $post_nonce_field ] ) && ! empty( $_POST[ $post_nonce_field ] ) ) {
			if ( check_admin_referer( 'oml_settings', $post_nonce_field ) ) {
				$organizemedialibrary_settings = get_user_option( 'organizemedialibrary', get_current_user_id() );
				if ( ! empty( $_POST['organizemedialibrary_character_code'] ) ) {
					$organizemedialibrary_settings['character_code'] = sanitize_text_field( wp_unslash( $_POST['organizemedialibrary_character_code'] ) );
				}
				if ( ! empty( $_POST['exclude_folders'] ) ) {
					$organizemedialibrary_settings['exclude_folders'] = sanitize_text_field( wp_unslash( $_POST['exclude_folders'] ) );
				} else {
					$organizemedialibrary_settings['exclude_folders'] = null;
				}
				if ( ! empty( $_POST['emu_subdir_change'] ) ) {
					$organizemedialibrary_settings['emu_subdir_change'] = true;
				} else {
					$organizemedialibrary_settings['emu_subdir_change'] = false;
				}
				update_user_option( get_current_user_id(), 'organizemedialibrary', $organizemedialibrary_settings );
				echo '<div class="notice notice-success is-dismissible"><ul><li>' . esc_html( __( 'Settings' ) . ' --> ' . __( 'Changes saved.' ) ) . '</li></ul></div>';
				$newdir = null;
				if ( ! empty( $_POST['newdir'] ) ) {
					$newdir = sanitize_text_field( wp_unslash( $_POST['newdir'] ) );
					$organizemedialibrary = new OrganizeMediaLibrary();
					$new_realdir = wp_normalize_path( $organizemedialibrary->upload_dir ) . '/' . $newdir;
					$mkdir_new_realdir = apply_filters( 'oml_mb_encode_multibyte', $new_realdir, $organizemedialibrary_settings['character_code'] );
					if ( ! file_exists( $mkdir_new_realdir ) ) {
						$err_mkdir = @wp_mkdir_p( $mkdir_new_realdir );
						if ( ! $err_mkdir ) {
							/* translators: Error message */
							echo '<div class="notice notice-error is-dismissible"><ul><li>' . esc_html( sprintf( __( 'Unable to create folder[%1$s].', 'organize-media-library' ), wp_normalize_path( apply_filters( 'oml_mb_utf8', $mkdir_new_realdir, $organizemedialibrary_settings['character_code'] ) ) ) ) . '</li></ul></div>';
							return;
						} else {
							update_user_option( get_current_user_id(), 'organizemedialibrary', $organizemedialibrary_settings );
							do_action( 'oml_dirs_tree', get_current_user_id(), true );
							do_action( 'oml_folders_term_create' );
							/* translators: Error message */
							echo '<div class="notice notice-success is-dismissible"><ul><li>' . esc_html( sprintf( __( 'Created folder[%1$s].', 'organize-media-library' ), wp_normalize_path( apply_filters( 'oml_mb_utf8', $mkdir_new_realdir, $organizemedialibrary_settings['character_code'] ) ) ) ) . '</li></ul></div>';
							if ( class_exists( 'ExtendMediaUploadAdmin' ) && ! get_option( 'uploads_use_yearmonth_folders' ) && ! empty( $_POST['emu_subdir_change'] ) ) {
								$extendmediaupload_settings = get_user_option( 'extendmediaupload', get_current_user_id() );
								$extendmediaupload_settings['subdir'] = '/' . $newdir;
								update_user_option( get_current_user_id(), 'extendmediaupload', $extendmediaupload_settings );
								/* translators: Error message */
								echo '<div class="notice notice-success is-dismissible"><ul><li>' . esc_html( sprintf( __( 'The upload folder has been changed to %s.', 'organize-media-library' ), $extendmediaupload_settings['subdir'] ) ) . '</li></ul></div>';
							}
						}
					} else {
						/* translators: Error message */
						echo '<div class="notice notice-error is-dismissible"><ul><li>' . esc_html( sprintf( __( 'Folder[%1$s] already exists.', 'organize-media-library' ), wp_normalize_path( apply_filters( 'oml_mb_utf8', $mkdir_new_realdir, $organizemedialibrary_settings['character_code'] ) ) ) ) . '</li></ul></div>';
					}
				} else {
					do_action( 'oml_dirs_tree', get_current_user_id(), true );
					do_action( 'oml_folders_term_create' );
					echo '<div class="notice notice-success is-dismissible"><ul><li>' . esc_html__( 'Only the folder structure and taxonomy have been updated.', 'organize-media-library' ) . '</li></ul></div>';
				}
			}
		}

	}

	/** ==================================================
	 * Media Library Column
	 *
	 * @param array $cols  cols.
	 * @return array $cols
	 * @since 6.00
	 */
	public function muc_column( $cols ) {

		global $pagenow;
		if ( 'upload.php' == $pagenow ) {
			$cols['oml_folders'] = __( 'Folder', 'organize-media-library' );
		}

		return $cols;

	}

	/** ==================================================
	 * Media Library Column
	 *
	 * @param string $column_name  column_name.
	 * @param int    $id  id.
	 * @since 6.00
	 */
	public function muc_value( $column_name, $id ) {

		if ( 'oml_folders' == $column_name ) {

			$organizemedialibrary_settings = get_user_option( 'organizemedialibrary', get_current_user_id() );

			$attach_rel_dir = get_post_meta( $id, '_wp_attached_file', true );
			$attach_rel_dir = '/' . untrailingslashit( str_replace( wp_basename( $attach_rel_dir ), '', $attach_rel_dir ) );
			$html = '<select name="targetdirs[' . $id . ']" style="width: 100%; font-size: small; text-align: left;">';
			$html .= apply_filters( 'oml_dir_selectbox', $attach_rel_dir, $organizemedialibrary_settings['character_code'], get_current_user_id() );
			$html .= '</select>';

			$allowed_html = array(
				'select'  => array(
					'name'  => array(),
					'style'  => array(),
				),
				'option'  => array(
					'value'  => array(),
					'select'  => array(),
					'selected'  => array(),
				),
			);

			echo wp_kses( $html, $allowed_html );

		}

	}

	/** ==================================================
	 * Media Library Search Filter for folders
	 *
	 * @since 6.00
	 */
	public function add_folder_filter() {

		global $wp_list_table;

		if ( empty( $wp_list_table->screen->post_type ) &&
			isset( $wp_list_table->screen->parent_file ) &&
			'upload.php' == $wp_list_table->screen->parent_file ) {
			$wp_list_table->screen->post_type = 'attachment';
		}

		if ( is_object_in_taxonomy( $wp_list_table->screen->post_type, 'oml_folders' ) ) {
			$get_media_folder = null;
			if ( isset( $_REQUEST['oml_folders'] ) && ! empty( $_REQUEST['oml_folders'] ) ) {
				$get_media_folder = sanitize_text_field( wp_unslash( $_REQUEST['oml_folders'] ) ); }
			?>
			<select name="oml_folders">
				<option value="" 
				<?php
				if ( empty( $get_media_folder ) ) {
					echo 'selected="selected"';}
				?>
				><?php esc_html_e( 'All Folders', 'organize-media-library' ); ?></option>
				<?php
				$args = array(
					'taxonomy'   => 'oml_folders',
					'hide_empty' => true,
				);
				$terms = get_terms( $args );
				foreach ( $terms as $term ) {
					?>
					<option value="<?php echo esc_attr( $term->slug ); ?>" 
						<?php
						if ( $get_media_folder == $term->slug ) {
							echo 'selected="selected"';
						}
						?>
					>
					<?php echo esc_html( $term->name ); ?>
					</option>
					<?php
				}
				?>
			</select>
			<?php
		}

	}

	/** ==================================================
	 * Bulk Action Select
	 *
	 * @since 6.00
	 */
	public function custom_bulk_admin_footer() {

		global $pagenow;
		if ( 'upload.php' == $pagenow ) {

			$organizemedialibrary_settings = get_user_option( 'organizemedialibrary', get_current_user_id() );

			$html = '<select name="bulk_folder" style="width: 100%; font-size: small; text-align: left;">';
			$html .= '<option value="">' . __( 'Bulk Select', 'organize-media-library' ) . '</option>';
			$html .= apply_filters( 'oml_dir_selectbox', untrailingslashit( plugin_dir_path( __DIR__ ) ), $organizemedialibrary_settings['character_code'], get_current_user_id() );
			$html .= '</select>';

			$allowed_html = array(
				'select'  => array(
					'name'  => array(),
					'style'  => array(),
				),
				'option'  => array(
					'value'  => array(),
					'select'  => array(),
					'selected'  => array(),
				),
			);

			?>
			<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery('<option>').val('movefolder').text('<?php esc_html_e( 'Move to selected folder', 'organize-media-library' ); ?>').appendTo("select[name='action']");
					jQuery('<option>').val('movefolder').text('<?php esc_html_e( 'Move to selected folder', 'organize-media-library' ); ?>').appendTo("select[name='action2']");
				});
				jQuery('<?php echo wp_kses( $html, $allowed_html ); ?>').appendTo("#oml_folders");
			</script>
			<?php
		}

	}

	/** ==================================================
	 * Bulk Action
	 *
	 * @since 6.00
	 */
	public function custom_bulk_action() {

		if ( ! isset( $_REQUEST['detached'] ) ) {

			/* get the action */
			$wp_list_table = _get_list_table( 'WP_Media_List_Table' );
			$action = $wp_list_table->current_action();

			$allowed_actions = array( 'movefolder' );
			if ( ! in_array( $action, $allowed_actions ) ) {
				return;
			}

			check_admin_referer( 'bulk-media' );

			if ( isset( $_REQUEST['media'] ) ) {
				$post_ids = array_map( 'intval', $_REQUEST['media'] );
			}

			if ( empty( $post_ids ) ) {
				return;
			}

			$sendback = remove_query_arg( array( 'foldermoved', 'message', 'untrashed', 'deleted', 'ids' ), wp_get_referer() );
			if ( ! $sendback ) {
				$sendback = admin_url( "upload.php?post_type=$post_type" );
			}

			$pagenum = $wp_list_table->get_pagenum();
			$sendback = add_query_arg( 'paged', $pagenum, $sendback );

			switch ( $action ) {
				case 'movefolder':
					$foldermoved = 0;
					$target_terms = array();
					if ( ! empty( $_REQUEST['targetdirs'] ) ) {
						$target_terms = filter_var(
							wp_unslash( $_REQUEST['targetdirs'] ),
							FILTER_CALLBACK,
							array(
								'options' => function( $value ) {
									return sanitize_text_field( $value );
								},
							)
						);
					} else {
						return;
					}
					$messages = array();

					$organizemedialibrary_settings = get_user_option( 'organizemedialibrary', get_current_user_id() );
					$character_code = $organizemedialibrary_settings['character_code'];

					foreach ( $post_ids as $post_id ) {
						$target_folder = get_term_by( 'slug', $target_terms[ $post_id ], 'oml_folders' )->name;
						$message = apply_filters( 'oml_folder_move_regist', $post_id, $target_folder, $character_code, get_current_user_id() );
						if ( $message ) {
							$messages[ $foldermoved ] = $message;
							$foldermoved++;
						}
					}
					do_action( 'oml_folders_term_create' );

					$sendback = add_query_arg(
						array(
							'foldermoved' => $foldermoved,
							'ids' => join( ',', $post_ids ),
							'message' => join(
								',',
								$messages
							),
						),
						$sendback
					);
					break;
				default:
					return;
			}

			$sendback = remove_query_arg( array( 'action', 'action2', 'tags_input', 'post_author', 'comment_status', 'ping_status', '_status', 'post', 'bulk_edit', 'post_view' ), $sendback );
			wp_redirect( $sendback );
			exit();

		}

	}

	/** ==================================================
	 * Bulk Action Message
	 *
	 * @since 6.00
	 */
	public function custom_bulk_admin_notices() {

		global $post_type, $pagenow;

		if ( 'upload.php' == $pagenow && 'attachment' == $post_type && isset( $_REQUEST['foldermoved'] ) && 0 < intval( $_REQUEST['foldermoved'] ) && isset( $_REQUEST['message'] ) ) {
			$messages = explode( ',', urldecode( wp_strip_all_tags( wp_unslash( $_REQUEST['message'] ) ) ) );
			$success_count = 0;
			foreach ( $messages as $message ) {
				if ( 'success' === $message ) {
					++$success_count;
				} else {
					echo '<div class="notice notice-error is-dismissible"><ul><li>' . esc_html( $message ) . '</li></ul></div>';
				}
			}
			if ( 0 < $success_count ) {
				/* translators: Success count */
				echo '<div class="notice notice-success is-dismissible"><ul><li>' . esc_html( sprintf( __( '%1$d media files updated.', 'organize-media-library' ), $success_count ) ) . '</li></ul></div>';
			}
		}

	}

	/** ==================================================
	 * Insert Media Custom Filter enqueue
	 *
	 * @since 6.04
	 */
	public function insert_media_custom_filter() {
		wp_enqueue_script( 'media-library-oml-taxonomy-filter', plugin_dir_url( __DIR__ ) . 'js/collection-filter.js', array( 'media-editor', 'media-views' ), '1.00', false );
		$dirs = array();
		$args = array(
			'taxonomy'   => 'oml_folders',
			'hide_empty' => true,
		);
		$terms = get_terms( $args );
		foreach ( $terms as $term ) {
			$dirs['terms'][ $term->slug ] = array(
				'name' => $term->name,
				'slug' => $term->slug,
			);
		}
		wp_localize_script(
			'media-library-oml-taxonomy-filter',
			'MediaLibraryOmlTaxonomyFilterData',
			$dirs
		);
		wp_localize_script(
			'media-library-oml-taxonomy-filter',
			'MediaLibraryOmlTaxonomyFilterDataText',
			array(
				'all_folders' => __( 'All Folders', 'organize-media-library' ),
			)
		);
		add_action( 'admin_footer', array( $this, 'insert_media_custom_filter_styling' ) );
	}

	/** ==================================================
	 * Insert Media Custom Filter style
	 *
	 * @since 6.04
	 */
	public function insert_media_custom_filter_styling() {
		?>
		<style>
		.media-modal-content .media-frame select.attachment-filters {
			max-width: -webkit-calc(30% - 12px);
			max-width: calc(30% - 12px);
		}
		</style>
		<?php
	}

	/** ==================================================
	 * Notices
	 *
	 * @since 6.31
	 */
	public function notices() {

		if ( $this->is_my_plugin_screen() ) {
			if ( is_multisite() ) {
				$emu_install_url = network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=extend-media-upload' );
			} else {
				$emu_install_url = admin_url( 'plugin-install.php?tab=plugin-information&plugin=extend-media-upload' );
			}
			$emu_install_html = '<a href="' . $emu_install_url . '" target="_blank" style="text-decoration: none; word-break: break-all;">Extend Media Upload</a>';
			if ( ! class_exists( 'ExtendMediaUploadAdmin' ) ) {
				/* translators: Extend Media Upload install link*/
				echo '<div class="notice notice-warning is-dismissible"><ul><li>' . wp_kses_post( sprintf( __( 'If you want to add a folder designation function to the media uploader, Please use the %1$s.', 'organize-media-library' ), $emu_install_html ) ) . '</li></ul></div>';
			}
			if ( ! get_user_option( 'oml_dirs', get_current_user_id() ) ) {
				echo '<div class="notice notice-warning is-dismissible"><ul><li>Organize Media Library by Folders: ' . esc_html__( 'Folder', 'organize-media-library' ) . ' ' . esc_html__( 'Settings' ) . ' -> ' . esc_html__( 'Make folder', 'organize-media-library' ) . '&' . esc_html__( 'Save Changes' ) . ' -> ' . esc_html__( 'Press the button', 'organize-media-library' ) . '</li></ul></div>';
			}
		}

	}

	/** ==================================================
	 * Update notice
	 *
	 * @since 7.10
	 */
	public function update_notice() {

		if ( $this->is_my_plugin_screen() ) {
			$version = floatval( get_user_option( 'oml_notice', get_current_user_id() ) );
			if ( 7.20 > $version ) {
				?>
				<div class="notice notice-warning is-dismissible"><ul><li>
				<strong>Organize Media Library by Folders</strong> : 
				<?php
				/* translators: %1$s button %2$s settings page */
				echo esc_html( sprintf( __( 'Fixed the folder reordering method. Please press the "%1$s" button on the "%2$s".', 'organize-media-library' ), __( 'Make folder', 'organize-media-library' ) . '&' . __( 'Save Changes' ), __( 'Folder', 'organize-media-library' ) . ' ' . __( 'Settings' ) ) );
				?>
				&nbsp
				<button id="Notice_Dismiss" name="notice_update_version" value="8.00" class="button button-small"><?php esc_html_e( 'Dismiss' ); ?></button>
				</li></ul></div>
				<?php
			}
		}

		$screen = get_current_screen();
		if ( is_object( $screen ) && 'dashboard' == $screen->id ||
				$this->is_my_plugin_screen() ) {
			if ( class_exists( 'OrganizeMediaFolder' ) ) {
				$organizemediafolder_url = admin_url( 'upload.php?page=organizemediafolder' );
			} else {
				if ( is_multisite() ) {
					$organizemediafolder_url = network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=organize-media-folder' );
				} else {
					$organizemediafolder_url = admin_url( 'plugin-install.php?tab=plugin-information&plugin=organize-media-folder' );
				}
			}
			$organizemediafolder_link = '<strong><a style="text-decoration: none;" href="' . $organizemediafolder_url . '">Organize Media Folder</a></strong>';
			$omf_html = '<div>' . __( 'Organize Media Library by Folders. URL in the content, replace with the new URL.', 'organize-media-library' ) . ' : ' . $organizemediafolder_link . '</div>';
			$organizemedialibrary_url = admin_url( 'upload.php?page=organizemedialibrary-settings' );
			$organizemedialibrary_link = '<strong><a style="text-decoration: none;" href="' . $organizemedialibrary_url . '">Organize Media Library by Folders</a></strong>';
			/* translators: Plugin Link */
			$oml_html = '<div>' . sprintf( __( '%1$s will be closed eventually with no more maintenance. The following plugin is successor. Please switch.', 'organize-media-library' ), $organizemedialibrary_link ) . '</div>';
			?>
			<div class="notice notice-warning is-dismissible"><ul><li>
			<?php
			echo wp_kses_post( $oml_html );
			echo wp_kses_post( $omf_html );
			?>
			</li></ul></div>
			<?php
		}

	}

}


