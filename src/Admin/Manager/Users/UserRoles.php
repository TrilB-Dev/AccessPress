<?php
/**
 * TrilB.Dev Plugin - User Roles Manager Admin Role Manager
 *
 * @package AccessPress
 * @subpackage Admin\UserRolesManager
 * @since 1.0.0
 */
namespace AccessPress\Admin\Manager\Users;

use AccessPress\Admin\Manager\UserManager;
use AccessPress\Includes\Functions\Helpers\FormFieldHelper;
use AccessPress\Includes\Functions\Helpers\AjaxHelper;
use AccessPress\Includes\Functions\Helpers\PermissionHelper;
use AccessPress\Includes\Functions\Helpers\RequestHelper;
use AccessPress\Includes\Functions\Helpers\SanitizationHelper;
use AccessPress\Includes\Functions\Helpers\UrlHelper;
use AccessPress\Includes\Core\Capabilities;
use AccessPress\Includes\Settings\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class UserRoles extends UserManager {
    /**
     * The slug for the Roles Manager admin page.
     */
	private const PAGE = 'accesspress-roles-manager';
	/**
	 * Registers post actions for the Roles Manager.
	 */
	public function register(): void {

		add_action( 'admin_post_accesspress_create_role', [ $this, 'create_role' ] );
		add_action( 'admin_post_accesspress_update_role', [ $this, 'update_role' ] );
		add_action( 'admin_post_accesspress_delete_role', [ $this, 'delete_role' ] );
	}
    /**
     * Renders the Roles Manager admin page.
     */
	public function render(): void {
		$this->authorize_view();

		$roles = wp_roles()->roles;
		$capability_groups = $this->capability_groups();

		$this->header( __( 'Roles Manager', 'accesspress' ) );
		?>
		<?php if ( PermissionHelper::can( 'accesspress_roles_create' ) ) : ?><div class="d-flex justify-content-end mb-4">
			<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#accesspress-add-role-modal"><?php esc_html_e( 'Add New', 'accesspress' ); ?></button>
		</div><?php endif; ?>
		<div class="row g-4">
			<?php foreach ( $roles as $slug => $role ) : ?>
				<div class="col-12 col-md-6 col-xl-4 d-flex">
					<article class="card accesspress-role-card shadow-sm h-100 w-100">
						<div class="card-body d-flex flex-column">
							<h2 class="h5 mb-2"><?php echo esc_html( translate_user_role( $role['name'] ) ); ?></h2>
							<p class="text-secondary mb-1"><code><?php echo esc_html( $slug ); ?></code></p>
							<p class="text-secondary mb-4"><?php /* translators: %d is the number of capabilities assigned to the role. */ echo esc_html( sprintf( _n( '%d permission', '%d permissions', count( $role['capabilities'] ), 'accesspress' ), count( $role['capabilities'] ) ) ); ?></p>
							<?php if ( PermissionHelper::can( 'accesspress_roles_edit' ) ) : ?><button type="button" class="btn btn-outline-primary mt-auto" data-bs-toggle="modal" data-bs-target="#accesspress-edit-role-<?php echo esc_attr( $slug ); ?>"><?php esc_html_e( 'Edit', 'accesspress' ); ?></button><?php endif; ?>
						</div>
					</article>
				</div>
			<?php endforeach; ?>
		</div>
		<?php if ( PermissionHelper::can( 'accesspress_roles_create' ) ) : $this->render_add_modal( $capability_groups ); endif; ?>
		<?php if ( PermissionHelper::can( 'accesspress_roles_edit' ) ) : foreach ( $roles as $slug => $role ) : $this->render_edit_modal( $slug, $role, $capability_groups ); endforeach; endif; ?>
		<?php
		$this->footer();
	}
    /**
     * Renders the header for the Roles Manager admin page.
     *
     * @param string $title The title of the page.
     */
	public function create_role(): void {

		$this->authorize_action( 'accesspress_create_role', 'accesspress_roles_create' );
		$display_name = $this->valid_role_name( RequestHelper::text( $_POST, 'role_display_name' ) );
		$slug = $this->valid_slug( RequestHelper::key( $_POST, 'role_slug' ) );
		if ( '' === $display_name || '' === $slug || wp_roles()->is_role( $slug ) || $this->role_name_exists( $display_name ) ) {
			$this->redirect( 'invalid' );
		}
		add_role( $slug, $display_name, $this->submitted_capabilities() );
		$this->redirect( 'created' );
	}
    /**
     * Updates an existing role with new display name and capabilities.
     */
	public function update_role(): void {

		$this->authorize_action( 'accesspress_update_role', 'accesspress_roles_edit' );
		$old_slug = RequestHelper::key( $_POST, 'old_role_slug' );
		$display_name = $this->valid_role_name( RequestHelper::text( $_POST, 'role_display_name' ) );
		$new_slug = $this->valid_slug( RequestHelper::key( $_POST, 'role_slug' ) );
		$roles = wp_roles();
		if ( '' === $old_slug || ! $roles->is_role( $old_slug ) || '' === $display_name || '' === $new_slug || ( $old_slug !== $new_slug && $roles->is_role( $new_slug ) ) || $this->role_name_exists( $display_name, $old_slug ) || ( $old_slug !== $new_slug && $this->role_has_users( $old_slug ) ) || ( 'administrator' === $old_slug && 'administrator' !== $new_slug ) ) {
			$this->redirect( 'invalid' );
		}
		$capabilities = $this->submitted_capabilities();
		if ( $old_slug !== $new_slug ) {
			add_role( $new_slug, $display_name, $capabilities );
			$roles->remove_role( $old_slug );
		} else {
			$role = $roles->get_role( $old_slug );
			$role->name = $display_name;
			foreach ( array_keys( $role->capabilities ) as $capability ) {
				$role->remove_cap( $capability );
			}
			foreach ( $capabilities as $capability ) {
				$role->add_cap( $capability );
			}
			$roles->roles[ $old_slug ]['name'] = $display_name;
			update_option( $roles->role_key, $roles->roles );
		}
		$this->redirect( 'updated' );
	}
    /**
     * Deletes an existing role if it has no users assigned to it.
     */
	public function delete_role(): void {
		$this->authorize_action( 'accesspress_delete_role', 'accesspress_roles_delete' );
		$slug = RequestHelper::key( $_POST, 'role_slug' );
		if ( 'administrator' === $slug || ! wp_roles()->is_role( $slug ) || $this->role_has_users( $slug ) ) {
			$this->redirect( 'invalid' );
		}
		remove_role( $slug );
		$this->redirect( 'deleted' );
	}
    /**
     * Checks if a role has any users assigned to it.
     *
     * @param string $slug The role slug.
     * @return bool True if the role has users, false otherwise.
     */
	private function render_add_modal( array $groups ): void {
		$this->render_modal_start( 'accesspress-add-role-modal', __( 'Add New Role', 'accesspress' ), 'accesspress_create_role' );
		$this->render_identity_step();
		$this->render_capability_step( $groups, [] );
		$this->render_modal_end( true );
	}
    /**
     * Renders the edit role modal for a specific role.
     *
     * @param string $slug The role slug.
     * @param array $role The role data.
     * @param array $groups The capability groups.
     */
	private function render_edit_modal( string $slug, array $role, array $groups ): void {
		$id = 'accesspress-edit-role-' . $slug;
		$this->render_modal_start( $id, __( 'Edit Role', 'accesspress' ), 'accesspress_update_role' );
		?>
		<?php echo FormFieldHelper::input( 'old_role_slug', $slug, [ 'type' => 'hidden' ] ); ?>
		<div class="row g-3 mb-4">
			<div class="col-md-6"><?php echo FormFieldHelper::label( $id . '-name', __( 'Role Display Name', 'accesspress' ) ); ?><?php echo FormFieldHelper::input( 'role_display_name', $role['name'], [ 'id' => $id . '-name', 'required' => true ] ); ?></div>
			<div class="col-md-6"><?php echo FormFieldHelper::label( $id . '-slug', __( 'Role Slug', 'accesspress' ) ); ?><?php echo FormFieldHelper::input( 'role_slug', $slug, [ 'id' => $id . '-slug', 'pattern' => '[a-z_]+', 'required' => true, 'disabled' => 'administrator' === $slug ] ); ?></div>
		</div>
		<?php $this->render_capability_step( $groups, $role['capabilities'] ); ?>
		<?php $this->render_modal_end( false, 'administrator' !== $slug, $slug ); ?>
		<?php
	}
    /**
     * Renders the start of a modal dialog.
     *
     * @param string $id The modal ID.
     * @param string $title The modal title.
     * @param string $action The form action.
     */
	private function render_modal_start( string $id, string $title, string $action ): void {
		?>
		<div class="modal fade" id="<?php echo esc_attr( $id ); ?>" tabindex="-1" aria-labelledby="<?php echo esc_attr( $id ); ?>-title" aria-hidden="true">
			<div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered"><form method="post" action="<?php echo esc_url( UrlHelper::admin_action( $action ) ); ?>" class="modal-content accesspress-role-form"<?php echo 'accesspress_create_role' === $action ? ' data-role-create' : ''; ?>>
				<div class="modal-header"><h2 class="modal-title h5" id="<?php echo esc_attr( $id ); ?>-title"><?php echo esc_html( $title ); ?></h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php esc_attr_e( 'Close', 'accesspress' ); ?>"></button></div>
				<div class="modal-body"><?php echo FormFieldHelper::input( 'action', $action, [ 'type' => 'hidden' ] ); ?><?php wp_nonce_field( $action ); ?>
		<?php
	}
    /**
     * Renders the identity step of the modal dialog.
     */
	private function render_identity_step(): void {
		$roles = wp_roles()->roles;
		$role_names = array_values( array_map( static fn( array $role ): string => strtolower( trim( (string) ( $role['name'] ?? '' ) ) ), $roles ) );
		$role_slugs = array_values( array_map( 'strtolower', array_keys( $roles ) ) );
		?>
		<div class="accesspress-role-step" data-role-step="identity">
			<div class="row g-3"><div class="col-md-6"><?php echo FormFieldHelper::label( 'accesspress-add-role-name', __( 'Role Display Name', 'accesspress' ) ); ?><?php echo FormFieldHelper::input( 'role_display_name', '', [ 'id' => 'accesspress-add-role-name', 'required' => true, 'pattern' => '[A-Za-z0-9 _-]+' ] ); ?><div class="invalid-feedback" data-role-name-feedback></div></div><div class="col-md-6"><?php echo FormFieldHelper::label( 'accesspress-add-role-slug', __( 'Role Slug', 'accesspress' ) ); ?><?php echo FormFieldHelper::input( 'role_slug', '', [ 'id' => 'accesspress-add-role-slug', 'required' => true, 'pattern' => '[a-z0-9_-]+' ] ); ?><div class="invalid-feedback" data-role-slug-feedback></div></div></div>
			<div class="d-none" data-role-existing-names="<?php echo esc_attr( wp_json_encode( $role_names ) ); ?>" data-role-existing-slugs="<?php echo esc_attr( wp_json_encode( $role_slugs ) ); ?>"></div>
		</div>
		<?php
	}
    /**
     * Renders the capability selection step of the modal dialog.
     *
     * @param array $groups The capability groups.
     * @param array $selected The selected capabilities.
     */
	private function render_capability_step( array $groups, array $selected ): void {
		$accordion_id = wp_unique_id( 'accesspress-capability-groups-' );
		$wordpress_descriptions = $this->wordpress_capability_descriptions();
		?>
		<div class="accesspress-role-step" data-role-step="capabilities">
			<h3 class="h6 mb-3"><?php esc_html_e( 'Capabilities', 'accesspress' ); ?></h3>
			<div class="accordion" id="<?php echo esc_attr( $accordion_id ); ?>">
				<?php $index = 0; foreach ( $groups as $label => $capabilities ) : $index++; $collapse_id = wp_unique_id( 'accesspress-capability-collapse-' ); $is_open = 1 === $index; ?>
					<div class="accordion-item">
						<h2 class="accordion-header">
							<button class="accordion-button<?php echo $is_open ? '' : ' collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo esc_attr( $collapse_id ); ?>" aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr( $collapse_id ); ?>">
								<?php echo esc_html( $label ); ?>
							</button>
						</h2>
						<div id="<?php echo esc_attr( $collapse_id ); ?>" class="accordion-collapse collapse<?php echo $is_open ? ' show' : ''; ?>" data-bs-parent="#<?php echo esc_attr( $accordion_id ); ?>">
							<div class="accordion-body">
								<div class="accesspress-capability-buttons" role="group" aria-label="<?php echo esc_attr( $label ); ?>">
								<?php
								foreach ( $capabilities as $capability => $description ) {
									$input_id = wp_unique_id( 'accesspress-capability-' );
									$is_selected = isset( $selected[ $capability ] );
									$wordpress_description = $wordpress_descriptions[ $capability ] ?? '';
									?>
									<div class="card accesspress-capability-card h-100">
										<div class="card-body d-flex flex-column align-items-center gap-3">
											<div class="accesspress-capability-title d-flex align-items-center justify-content-center gap-1">
												<h6 class="card-title mb-0 text-center"><?php echo esc_html( $description ?: $capability ); ?></h6>
												<?php if ( $wordpress_description ) : ?>
													<button type="button" class="btn btn-link p-0 align-baseline accesspress-capability-info" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo esc_attr( $wordpress_description ); ?>" aria-label="<?php esc_attr_e( 'Capability information', 'accesspress' ); ?>">
														<i class="fa-solid fa-circle-info" aria-hidden="true"></i>
													</button>
												<?php endif; ?>
											</div>
											<input class="btn-check" type="checkbox" name="capabilities[]" value="<?php echo esc_attr( $capability ); ?>" id="<?php echo esc_attr( $input_id ); ?>" autocomplete="off"<?php checked( $is_selected ); ?> />
											<label class="btn btn-<?php echo $is_selected ? 'primary' : 'outline-primary'; ?> accesspress-capability-toggle" for="<?php echo esc_attr( $input_id ); ?>" data-capability-toggle><?php echo $is_selected ? esc_html__( 'On', 'accesspress' ) : esc_html__( 'Off', 'accesspress' ); ?></label>
										</div>
									</div>
									<?php
								}
								?>
								</div>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
    /**
     * Renders the end of a modal dialog.
     *
     * @param bool $multi_step Whether the modal has multiple steps.
     * @param bool $allow_delete Whether to show the delete button.
     * @param string $slug The role slug (for delete action).
     */
	private function render_modal_end( bool $multi_step, bool $allow_delete = false, string $slug = '' ): void {
		?>
				</div><div class="modal-footer justify-content-between"><div><?php if ( $allow_delete ) : ?><button type="submit" class="btn btn-outline-danger" formaction="<?php echo esc_url( UrlHelper::admin_action( 'accesspress_delete_role' ) ); ?>" formmethod="post" name="action" value="accesspress_delete_role" data-role-slug="<?php echo esc_attr( $slug ); ?>" onclick="this.form.querySelector('[name=role_slug]').value = this.dataset.roleSlug; this.form.querySelector('[name=_wpnonce]').value = '<?php echo esc_js( wp_create_nonce( 'accesspress_delete_role' ) ); ?>'; return confirm('<?php echo esc_js( __( 'Delete this role?', 'accesspress' ) ); ?>');"><?php esc_html_e( 'Delete', 'accesspress' ); ?></button><?php endif; ?></div><div class="d-flex gap-2"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php esc_html_e( 'Cancel', 'accesspress' ); ?></button><button type="button" class="btn btn-outline-primary <?php echo $multi_step ? '' : 'd-none'; ?>" data-role-back><?php esc_html_e( 'Back', 'accesspress' ); ?></button><button type="button" class="btn btn-primary <?php echo $multi_step ? '' : 'd-none'; ?>" data-role-next disabled><?php esc_html_e( 'Next', 'accesspress' ); ?></button><button type="submit" class="btn btn-primary" data-role-save><?php esc_html_e( 'Save', 'accesspress' ); ?></button></div></div>
			</form></div>
		</div>
		<?php
	}
    /**
     * Redirects to the Roles Manager page with a status message.
     *
     * @param string $status The status message key.
     */
	private function capability_groups(): array {
		$groups = [
			'Multi Sites' => is_multisite() ? [
				'create_sites' => __( 'Create Sites', 'accesspress' ),
				'delete_sites' => __( 'Delete Sites', 'accesspress' ),
				'manage_network' => __( 'Manage Network', 'accesspress' ),
				'manage_sites' => __( 'Manage Sites', 'accesspress' ),
				'manage_network_users' => __( 'Manage Network Users', 'accesspress' ),
				'manage_network_plugins' => __( 'Manage Network Plugins', 'accesspress' ),
				'manage_network_themes' => __( 'Manage Network Themes', 'accesspress' ),
				'manage_network_options' => __( 'Manage Network Options', 'accesspress' ),
				'upgrade_network' => __( 'Upgrade Network', 'accesspress' ),
				'setup_network' => __( 'Setup Network', 'accesspress' ),
				'delete_site' => __( 'Delete Site', 'accesspress' )
			] : [],
			'Manage' => [
				'read' => __( 'Read', 'accesspress' ),
				'edit_dashboard' => __( 'Edit Dashboard', 'accesspress' ),
				'export' => __( 'Export', 'accesspress' ),
				'import' => __( 'Import', 'accesspress' ),
				'manage_options' => __( 'Manage Options', 'accesspress' ),
				'manage_links' => __( 'Manage Links', 'accesspress' ),
				'moderate_comments' => __( 'Moderate Comments', 'accesspress' ),
				'update_core' => __( 'Update Core', 'accesspress' )
			],
			'Content' => [
				'delete_others_pages' => __( 'Delete Others Pages', 'accesspress' ),
				'delete_others_posts' => __( 'Delete Others Posts', 'accesspress' ),
				'delete_pages' => __( 'Delete Pages', 'accesspress' ),
				'delete_posts' => __( 'Delete Posts', 'accesspress' ),
				'delete_private_pages' => __( 'Delete Private Pages', 'accesspress' ),
				'delete_private_posts' => __( 'Delete Private Posts', 'accesspress' ),
				'delete_published_pages' => __( 'Delete Published Pages', 'accesspress' ),
				'delete_published_posts' => __( 'Delete Published Posts', 'accesspress' ),
				'edit_others_pages' => __( 'Edit Others Pages', 'accesspress' ),
				'edit_others_posts' => __( 'Edit Others Posts', 'accesspress' ),
				'edit_pages' => __( 'Edit Pages', 'accesspress' ),
				'edit_posts' => __( 'Edit Posts', 'accesspress' ),
				'edit_private_pages' => __( 'Edit Private Pages', 'accesspress' ),
				'edit_private_posts' => __( 'Edit Private Posts', 'accesspress' ),
				'edit_published_pages' => __( 'Edit Published Pages', 'accesspress' ),
				'edit_published_posts' => __( 'Edit Published Posts', 'accesspress' ),
				'publish_pages' => __( 'Publish Pages', 'accesspress' ),
				'publish_posts' => __( 'Publish Posts', 'accesspress' ),
				'read_private_pages' => __( 'Read Private Pages', 'accesspress' ),
				'read_private_posts' => __( 'Read Private Posts', 'accesspress' ),
				'manage_categories' => __( 'Manage Categories', 'accesspress' ),
				'upload_files' => __( 'Upload Files', 'accesspress' ),
				'unfiltered_html' => __( 'Unfiltered HTML', 'accesspress' ),
			],
			'Users' => [
				'promote_users' => __( 'Promote Users', 'accesspress' ),
				'remove_users' => __( 'Remove Users', 'accesspress' ),
				'list_users' => __( 'List Users', 'accesspress' ),
				'edit_users' => __( 'Edit Users', 'accesspress' ),
				'add_users' => __( 'Add Users', 'accesspress' ),
				'create_users' => __( 'Create Users', 'accesspress' ),
				'delete_users' => __( 'Delete Users', 'accesspress' ),
			],
			'Plugins' => [
				'activate_plugins' => __( 'Activate Plugins', 'accesspress' ),
				'update_plugins' => __( 'Update Plugins', 'accesspress' ),
				'install_plugins' => __( 'Install Plugins', 'accesspress' ),
				'delete_plugins' => __( 'Delete Plugins', 'accesspress' ),
				'edit_plugins' => __( 'Edit Plugins', 'accesspress' ),
			],
			'Themes' => [
				'edit_theme_options' => __( 'Edit Theme Options', 'accesspress' ),
				'customize' => __( 'Customize', 'accesspress' ),
				'switch_themes' => __( 'Switch Themes', 'accesspress' ),
				'install_themes' => __( 'Install Themes', 'accesspress' ),
				'delete_themes' => __( 'Delete Themes', 'accesspress' ),
				'edit_themes' => __( 'Edit Themes', 'accesspress' ),
				'update_themes' => __( 'Update Themes', 'accesspress' ),
			],
			'AccessPress' => [
				'create_wikis' => __( 'Create Wikis', 'accesspress' ),
				'write_pages' => __( 'Write Wiki Pages', 'accesspress' ),
				'view_analytics' => __( 'View Analytics', 'accesspress' ),
				'manage_plugins' => __( 'Manage AccessPress Plugins', 'accesspress' ),
			],
		];
		$groups['AccessPress'] = [];
		foreach ( Capabilities::definitions() as $capability => $definition ) {
			$groups['AccessPress'][ $capability ] = $definition['label'];
		}
		$known = [];
		foreach ( $groups as $capabilities ) {
			$known = array_merge( $known, array_keys( $capabilities ) );
		}
		$plugin_capabilities = [];
		foreach ( wp_roles()->roles as $role ) {
			foreach ( array_keys( $role['capabilities'] ) as $capability ) {
				if ( ! in_array( $capability, $known, true ) && ! str_starts_with( $capability, 'level_' ) ) {
					$plugin_capabilities[ $capability ] = ucwords( str_replace( [ '_', '-' ], ' ', $capability ) );
				}
			}
		}
		ksort( $plugin_capabilities, SORT_NATURAL | SORT_FLAG_CASE );
        
		if ( $plugin_capabilities ) {

			$groups['WordPress Plugins'] = $plugin_capabilities;

		}

		return array_filter( $groups );
	}

	private function wordpress_capability_descriptions(): array {
		$descriptions = [
			'read' => __( 'Allows viewing the administration area and content.', 'accesspress' ),
			'edit_dashboard' => __( 'Allows editing dashboard widgets.', 'accesspress' ),
			'export' => __( 'Allows exporting content.', 'accesspress' ),
			'import' => __( 'Allows importing content.', 'accesspress' ),
			'manage_links' => __( 'Allows managing links.', 'accesspress' ),
			'moderate_comments' => __( 'Allows moderating comments.', 'accesspress' ),
			'update_core' => __( 'Allows updating WordPress.', 'accesspress' ),
			'delete_others_pages' => __( 'Allows deleting pages created by other users.', 'accesspress' ),
			'delete_others_posts' => __( 'Allows deleting posts created by other users.', 'accesspress' ),
			'edit_posts' => __( 'Allows editing posts.', 'accesspress' ),
			'edit_pages' => __( 'Allows editing pages.', 'accesspress' ),
			'delete_private_pages' => __( 'Allows deleting private pages.', 'accesspress' ),
			'delete_private_posts' => __( 'Allows deleting private posts.', 'accesspress' ),
			'delete_published_pages' => __( 'Allows deleting published pages.', 'accesspress' ),
			'delete_published_posts' => __( 'Allows deleting published posts.', 'accesspress' ),
			'edit_others_pages' => __( 'Allows editing pages created by other users.', 'accesspress' ),
			'edit_others_posts' => __( 'Allows editing posts created by other users.', 'accesspress' ),
			'edit_private_pages' => __( 'Allows editing private pages.', 'accesspress' ),
			'edit_private_posts' => __( 'Allows editing private posts.', 'accesspress' ),
			'edit_published_pages' => __( 'Allows editing published pages.', 'accesspress' ),
			'edit_published_posts' => __( 'Allows editing published posts.', 'accesspress' ),
			'publish_posts' => __( 'Allows publishing posts.', 'accesspress' ),
			'publish_pages' => __( 'Allows publishing pages.', 'accesspress' ),
			'delete_posts' => __( 'Allows deleting posts.', 'accesspress' ),
			'delete_pages' => __( 'Allows deleting pages.', 'accesspress' ),
			'read_private_pages' => __( 'Allows reading private pages.', 'accesspress' ),
			'read_private_posts' => __( 'Allows reading private posts.', 'accesspress' ),
			'upload_files' => __( 'Allows uploading files.', 'accesspress' ),
			'manage_categories' => __( 'Allows managing categories.', 'accesspress' ),
			'unfiltered_html' => __( 'Allows posting unfiltered HTML.', 'accesspress' ),
			'promote_users' => __( 'Allows promoting users.', 'accesspress' ),
			'add_users' => __( 'Allows adding existing users to a site.', 'accesspress' ),
			'manage_options' => __( 'Allows managing site options.', 'accesspress' ),
			'activate_plugins' => __( 'Allows activating, deactivating, and managing plugins.', 'accesspress' ),
			'update_plugins' => __( 'Allows updating plugins.', 'accesspress' ),
			'install_plugins' => __( 'Allows installing plugins.', 'accesspress' ),
			'delete_plugins' => __( 'Allows deleting plugins.', 'accesspress' ),
			'edit_plugins' => __( 'Allows editing plugin files.', 'accesspress' ),
			'edit_themes' => __( 'Allows editing theme files.', 'accesspress' ),
			'install_themes' => __( 'Allows installing themes.', 'accesspress' ),
			'delete_themes' => __( 'Allows deleting themes.', 'accesspress' ),
			'update_themes' => __( 'Allows updating themes.', 'accesspress' ),
			'switch_themes' => __( 'Allows switching the active theme.', 'accesspress' ),
			'customize' => __( 'Allows customizing the site.', 'accesspress' ),
			'edit_theme_options' => __( 'Allows managing theme options.', 'accesspress' ),
			'edit_users' => __( 'Allows editing users.', 'accesspress' ),
			'list_users' => __( 'Allows listing users.', 'accesspress' ),
			'create_users' => __( 'Allows creating users.', 'accesspress' ),
			'delete_users' => __( 'Allows deleting users.', 'accesspress' ),
			'remove_users' => __( 'Allows removing users from a site.', 'accesspress' ),
			'manage_network' => __( 'Allows managing network administration.', 'accesspress' ),
			'manage_network_users' => __( 'Allows managing network users.', 'accesspress' ),
			'manage_network_plugins' => __( 'Allows managing network plugins.', 'accesspress' ),
			'manage_network_themes' => __( 'Allows managing network themes.', 'accesspress' ),
			'manage_network_options' => __( 'Allows managing network options.', 'accesspress' ),
			'create_sites' => __( 'Allows creating sites in a multisite network.', 'accesspress' ),
			'delete_sites' => __( 'Allows deleting sites in a multisite network.', 'accesspress' ),
			'upgrade_network' => __( 'Allows upgrading the network.', 'accesspress' ),
			'setup_network' => __( 'Allows setting up the network.', 'accesspress' ),
			'delete_site' => __( 'Allows deleting the current site.', 'accesspress' ),
		];
		foreach ( Capabilities::definitions() as $capability => $definition ) {
			$descriptions[ $capability ] = $definition['description'];
		}

		return $descriptions;
	}
    /**
     * Collects the capabilities submitted from the form.
     *
     * @return array The submitted capabilities.
     */
	private function submitted_capabilities(): array {

		$capabilities = array_map( [ SanitizationHelper::class, 'key' ], RequestHelper::array( $_POST, 'capabilities' ) );
		$capabilities = array_unique( array_filter( $capabilities ) );

		return array_fill_keys( $capabilities, true );
	}
    /**
     * Checks if a role has any users assigned to it.
     *
     * @param string $slug The role slug.
     * @return bool True if the role has users, false otherwise.
     */
	private function role_has_users( string $slug ): bool {

		return ! empty( get_users( [ 'role' => $slug, 'fields' => 'ID', 'number' => 1 ] ) );

	}
    /**
     * Validates a role slug.
     *
     * @param string $value The role slug to validate.
     * @return string The sanitized role slug, or an empty string if invalid.
     */
	private function valid_slug( $value ): string {

		$slug = strtolower( SanitizationHelper::text( $value ) );

		return preg_match( '/^[a-z0-9_-]+$/', $slug ) ? $slug : '';

	}

	private function valid_role_name( string $value ): string {

		$name = trim( SanitizationHelper::text( $value ) );

		return preg_match( '/^[A-Za-z0-9 _-]+$/', $name ) ? $name : '';

	}

	private function role_name_exists( string $name, string $exclude_slug = '' ): bool {

	$name = strtolower( trim( $name ) );

		foreach ( wp_roles()->roles as $slug => $role ) {
			if ( $slug !== $exclude_slug && $name === strtolower( trim( (string) ( $role['name'] ?? '' ) ) ) ) {
				return true;
			}
		}

		return false;

	}
    /**
     * Authorizes an action by checking user capabilities and nonce.
     *
     * @param string $action The action to authorize.
     */
	private function authorize_view(): void {
		if ( ! PermissionHelper::can( 'accesspress_roles_view' ) ) {
			wp_die( esc_html__( 'You are not allowed to view roles.', 'accesspress' ), 403 );
		}
	}

	private function authorize_action( string $action, string $capability ): void {

		if ( ! PermissionHelper::can( $capability ) ) {

			wp_die( esc_html__( 'You are not allowed to manage roles.', 'accesspress' ), 403 );

		}

		if ( ! check_admin_referer( $action, '_wpnonce', false ) ) {
			wp_die( esc_html__( 'Security check failed.', 'accesspress' ), 403 );
		}
	}
    /**
     * Redirects to the Roles Manager page with a status message.
     *
     * @param string $status The status message key.
     */
	private function redirect( string $status ): void {

		wp_safe_redirect( add_query_arg( [ 'page' => self::PAGE, 'role_status' => $status ], admin_url( 'users.php' ) ) );

		exit;

	}
}