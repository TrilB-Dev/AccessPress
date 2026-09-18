<?php
/**
 * User groups manager admin screen for AccessPress.
 *
 * @package AccessPress
 * @subpackage Admin/Manager/Users
 * 
 * @since 1.0.0
 */
namespace AccessPress\Admin\Manager\Users;

use AccessPress\Includes\Functions\Helpers\FormFieldHelper;
use AccessPress\Includes\Functions\Helpers\PermissionHelper;
use AccessPress\Includes\Functions\Helpers\RequestHelper;
use AccessPress\Includes\Functions\Helpers\SanitizationHelper;
use AccessPress\Includes\Functions\Helpers\UrlHelper;
use AccessPress\Includes\UserManagement\Groups\Groups;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class UserGroups extends UserManager {
	/**
	 * Admin page slug used in redirects.
	 *
	 * @var string
	 */
	private const PAGE = 'user-management';

	public function __construct() {
		parent::__construct( false );
	}

	/**
	 * Register group admin hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'admin_post_accesspress_create_group', array( $this, 'create_group' ) );
		add_action( 'admin_post_accesspress_update_group', array( $this, 'update_group' ) );
		add_action( 'admin_post_accesspress_delete_group', array( $this, 'delete_group' ) );
	}

	/**
	 * Backwards-compatible render entry point used by the roles manager pattern.
	 *
	 * @return void
	 */
	public function render(): void {
		$this->render_page_content();
	}

	/**
	 * Render the groups management screen.
	 *
	 * @return void
	 */
	public function render_page_content(): void {
		$this->authorize_view();

		$groups = Groups::register_default_groups();
		$this->header( __( 'Groups Manager', 'accesspress' ) );
		?>
		<?php if ( PermissionHelper::can( 'accesspress_groups_create' ) ) : ?>
		<div class="d-flex justify-content-end mb-4">
			<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#accesspress-add-group-modal">
				<?php esc_html_e( 'Add New', 'accesspress' ); ?>
			</button>
		</div>
		<?php endif; ?>

		<div class="row g-4">
			<?php foreach ( $groups as $slug => $group ) : ?>
				<div class="col-12 col-md-6 col-xl-4 d-flex">
					<article class="card shadow-sm h-100 w-100">
						<div class="card-body d-flex flex-column">
							<div class="d-flex align-items-center justify-content-between gap-3 mb-2">
								<h2 class="h5 mb-0"><?php echo esc_html( (string) ( $group['label'] ?? ucfirst( (string) $slug ) ) ); ?></h2>
								<span class="badge rounded-pill text-bg-light" style="border: 1px solid <?php echo esc_attr( (string) ( $group['color'] ?? '#4f46e5' ) ); ?>; color: <?php echo esc_attr( (string) ( $group['color'] ?? '#4f46e5' ) ); ?>;">
									<?php echo esc_html( $slug ); ?>
								</span>
							</div>
							<p class="text-secondary mb-2"><?php echo esc_html( (string) ( $group['description'] ?? __( 'No description set yet.', 'accesspress' ) ) ); ?></p>
							<ul class="list-unstyled small text-secondary mb-4">
								<li><strong><?php esc_html_e( 'Priority:', 'accesspress' ); ?></strong> <?php echo esc_html( (string) ( $group['priority'] ?? 0 ) ); ?></li>
								<li><strong><?php esc_html_e( 'Price:', 'accesspress' ); ?></strong> <?php echo esc_html( (string) ( $group['price'] ?? 0 ) ); ?></li>
							</ul>
							<?php if ( PermissionHelper::can( 'accesspress_groups_edit' ) ) : ?>
								<button type="button" class="btn btn-outline-primary mt-auto" data-bs-toggle="modal" data-bs-target="#accesspress-edit-group-<?php echo esc_attr( $slug ); ?>">
									<?php esc_html_e( 'Edit', 'accesspress' ); ?>
								</button>
							<?php endif; ?>
						</div>
					</article>
				</div>
			<?php endforeach; ?>
		</div>

		<?php if ( PermissionHelper::can( 'accesspress_groups_create' ) ) : ?>
			<?php $this->render_add_modal(); ?>
		<?php endif; ?>

		<?php if ( PermissionHelper::can( 'accesspress_groups_edit' ) ) : ?>
			<?php foreach ( $groups as $slug => $group ) : ?>
				<?php $this->render_edit_modal( $slug, $group ); ?>
			<?php endforeach; ?>
		<?php endif; ?>
		<?php
		$this->footer();
	}

	/**
	 * Create a new user group.
	 *
	 * @return void
	 */
	public function create_group(): void {
		$this->authorize_action( 'accesspress_create_group', 'accesspress_groups_create' );

		$slug = $this->valid_slug( RequestHelper::key( $_POST, 'group_slug' ) );
		$name = $this->valid_name( RequestHelper::text( $_POST, 'group_name' ) );

		if ( '' === $slug || '' === $name ) {
			$this->redirect( 'invalid' );
		}

		$definitions = Groups::register_default_groups();
		if ( isset( $definitions[ $slug ] ) ) {
			$this->redirect( 'invalid' );
		}

		$definitions[ $slug ] = array(
			'label'       => $name,
			'description' => SanitizationHelper::text( RequestHelper::text( $_POST, 'group_description' ) ),
			'priority'    => absint( RequestHelper::key( $_POST, 'group_priority' ) ),
			'color'       => sanitize_hex_color( RequestHelper::text( $_POST, 'group_color' ) ) ?: '#4f46e5',
			'price'       => floatval( RequestHelper::key( $_POST, 'group_price' ) ),
		);

		update_option( 'accesspress_default_user_groups', $definitions, false );
		$this->redirect( 'created' );
	}

	/**
	 * Update an existing user group.
	 *
	 * @return void
	 */
	public function update_group(): void {
		$this->authorize_action( 'accesspress_update_group', 'accesspress_groups_edit' );

		$slug = $this->valid_slug( RequestHelper::key( $_POST, 'group_slug' ) );
		$old_slug = $this->valid_slug( RequestHelper::key( $_POST, 'old_group_slug' ) );
		$name = $this->valid_name( RequestHelper::text( $_POST, 'group_name' ) );

		if ( '' === $old_slug || '' === $slug || '' === $name ) {
			$this->redirect( 'invalid' );
		}

		$definitions = Groups::register_default_groups();
		if ( ! isset( $definitions[ $old_slug ] ) ) {
			$this->redirect( 'invalid' );
		}

		unset( $definitions[ $old_slug ] );
		$definitions[ $slug ] = array(
			'label'       => $name,
			'description' => SanitizationHelper::text( RequestHelper::text( $_POST, 'group_description' ) ),
			'priority'    => absint( RequestHelper::key( $_POST, 'group_priority' ) ),
			'color'       => sanitize_hex_color( RequestHelper::text( $_POST, 'group_color' ) ) ?: '#4f46e5',
			'price'       => floatval( RequestHelper::key( $_POST, 'group_price' ) ),
		);

		update_option( 'accesspress_default_user_groups', $definitions, false );
		$this->redirect( 'updated' );
	}

	/**
	 * Delete an existing user group.
	 *
	 * @return void
	 */
	public function delete_group(): void {
		$this->authorize_action( 'accesspress_delete_group', 'accesspress_groups_delete' );

		$slug = $this->valid_slug( RequestHelper::key( $_POST, 'group_slug' ) );
		if ( '' === $slug ) {
			$this->redirect( 'invalid' );
		}

		$definitions = Groups::register_default_groups();
		if ( ! isset( $definitions[ $slug ] ) ) {
			$this->redirect( 'invalid' );
		}

		unset( $definitions[ $slug ] );
		update_option( 'accesspress_default_user_groups', $definitions, false );
		$this->redirect( 'deleted' );
	}

	/**
	 * Render the add group modal.
	 *
	 * @return void
	 */
	private function render_add_modal(): void {
		$this->render_modal_start( 'accesspress-add-group-modal', __( 'Add New Group', 'accesspress' ), 'accesspress_create_group' );
		?>
		<div class="row g-3 mb-4">
			<div class="col-md-6">
				<?php echo FormFieldHelper::label( 'accesspress-group-name', __( 'Group Name', 'accesspress' ) ); ?>
				<?php echo FormFieldHelper::input( 'group_name', '', array( 'id' => 'accesspress-group-name', 'required' => true ) ); ?>
			</div>
			<div class="col-md-6">
				<?php echo FormFieldHelper::label( 'accesspress-group-slug', __( 'Group Slug', 'accesspress' ) ); ?>
				<?php echo FormFieldHelper::input( 'group_slug', '', array( 'id' => 'accesspress-group-slug', 'required' => true, 'pattern' => '[a-z0-9_-]+' ) ); ?>
			</div>
			<div class="col-12">
				<?php echo FormFieldHelper::label( 'accesspress-group-description', __( 'Description', 'accesspress' ) ); ?>
				<?php echo FormFieldHelper::textarea( 'group_description', '', array( 'id' => 'accesspress-group-description', 'rows' => 4 ) ); ?>
			</div>
			<div class="col-md-4">
				<?php echo FormFieldHelper::label( 'accesspress-group-priority', __( 'Priority', 'accesspress' ) ); ?>
				<?php echo FormFieldHelper::input( 'group_priority', '0', array( 'id' => 'accesspress-group-priority', 'type' => 'number', 'min' => 0 ) ); ?>
			</div>
			<div class="col-md-4">
				<?php echo FormFieldHelper::label( 'accesspress-group-color', __( 'Color', 'accesspress' ) ); ?>
				<?php echo FormFieldHelper::input( 'group_color', '#4f46e5', array( 'id' => 'accesspress-group-color', 'type' => 'color' ) ); ?>
			</div>
			<div class="col-md-4">
				<?php echo FormFieldHelper::label( 'accesspress-group-price', __( 'Price', 'accesspress' ) ); ?>
				<?php echo FormFieldHelper::input( 'group_price', '0', array( 'id' => 'accesspress-group-price', 'type' => 'number', 'step' => '0.01', 'min' => 0 ) ); ?>
			</div>
		</div>
		<?php
		$this->render_modal_end();
	}

	/**
	 * Render the edit group modal.
	 *
	 * @param string $slug Group slug.
	 * @param array  $group Group definition.
	 * @return void
	 */
	private function render_edit_modal( string $slug, array $group ): void {
		$id = 'accesspress-edit-group-' . $slug;
		$this->render_modal_start( $id, __( 'Edit Group', 'accesspress' ), 'accesspress_update_group' );
		?>
		<?php echo FormFieldHelper::input( 'old_group_slug', $slug, array( 'type' => 'hidden' ) ); ?>
		<div class="row g-3 mb-4">
			<div class="col-md-6">
				<?php echo FormFieldHelper::label( $id . '-name', __( 'Group Name', 'accesspress' ) ); ?>
				<?php echo FormFieldHelper::input( 'group_name', (string) ( $group['label'] ?? $slug ), array( 'id' => $id . '-name', 'required' => true ) ); ?>
			</div>
			<div class="col-md-6">
				<?php echo FormFieldHelper::label( $id . '-slug', __( 'Group Slug', 'accesspress' ) ); ?>
				<?php echo FormFieldHelper::input( 'group_slug', $slug, array( 'id' => $id . '-slug', 'required' => true, 'pattern' => '[a-z0-9_-]+' ) ); ?>
			</div>
			<div class="col-12">
				<?php echo FormFieldHelper::label( $id . '-description', __( 'Description', 'accesspress' ) ); ?>
				<?php echo FormFieldHelper::textarea( 'group_description', (string) ( $group['description'] ?? '' ), array( 'id' => $id . '-description', 'rows' => 4 ) ); ?>
			</div>
			<div class="col-md-4">
				<?php echo FormFieldHelper::label( $id . '-priority', __( 'Priority', 'accesspress' ) ); ?>
				<?php echo FormFieldHelper::input( 'group_priority', (string) ( $group['priority'] ?? 0 ), array( 'id' => $id . '-priority', 'type' => 'number', 'min' => 0 ) ); ?>
			</div>
			<div class="col-md-4">
				<?php echo FormFieldHelper::label( $id . '-color', __( 'Color', 'accesspress' ) ); ?>
				<?php echo FormFieldHelper::input( 'group_color', (string) ( $group['color'] ?? '#4f46e5' ), array( 'id' => $id . '-color', 'type' => 'color' ) ); ?>
			</div>
			<div class="col-md-4">
				<?php echo FormFieldHelper::label( $id . '-price', __( 'Price', 'accesspress' ) ); ?>
				<?php echo FormFieldHelper::input( 'group_price', (string) ( $group['price'] ?? 0 ), array( 'id' => $id . '-price', 'type' => 'number', 'step' => '0.01', 'min' => 0 ) ); ?>
			</div>
		</div>
		<?php
		$this->render_modal_end( $slug );
	}

	/**
	 * Render the start of a modal dialog.
	 *
	 * @param string $id     Modal identifier.
	 * @param string $title  Modal title.
	 * @param string $action Form action name.
	 * @return void
	 */
	private function render_modal_start( string $id, string $title, string $action ): void {
		?>
		<div class="modal fade" id="<?php echo esc_attr( $id ); ?>" tabindex="-1" aria-labelledby="<?php echo esc_attr( $id ); ?>-title" aria-hidden="true">
			<div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
				<form method="post" action="<?php echo esc_url( UrlHelper::admin_action( $action ) ); ?>" class="modal-content">
					<div class="modal-header">
						<h2 class="modal-title h5" id="<?php echo esc_attr( $id ); ?>-title"><?php echo esc_html( $title ); ?></h2>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php esc_attr_e( 'Close', 'accesspress' ); ?>"></button>
					</div>
					<div class="modal-body">
						<?php echo FormFieldHelper::input( 'action', $action, array( 'type' => 'hidden' ) ); ?>
						<?php wp_nonce_field( $action ); ?>
					<?php
	}

	/**
	 * Render the end of a modal dialog.
	 *
	 * @param string $slug Optional slug used for delete action.
	 * @return void
	 */
	private function render_modal_end( string $slug = '' ): void {
		?>
					</div>
					<div class="modal-footer justify-content-between">
						<div>
							<?php if ( '' !== $slug ) : ?>
							<button type="submit" class="btn btn-outline-danger" formaction="<?php echo esc_url( UrlHelper::admin_action( 'accesspress_delete_group' ) ); ?>" formmethod="post" name="action" value="accesspress_delete_group" data-group-slug="<?php echo esc_attr( $slug ); ?>" onclick="this.form.querySelector('[name=group_slug]').value = this.dataset.groupSlug; this.form.querySelector('[name=_wpnonce]').value = '<?php echo esc_js( wp_create_nonce( 'accesspress_delete_group' ) ); ?>'; return confirm('<?php echo esc_js( __( 'Delete this group?', 'accesspress' ) ); ?>');">
								<?php esc_html_e( 'Delete', 'accesspress' ); ?>
							</button>
							<?php endif; ?>
						</div>
						<div class="d-flex gap-2">
							<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php esc_html_e( 'Cancel', 'accesspress' ); ?></button>
							<button type="submit" class="btn btn-primary"><?php esc_html_e( 'Save', 'accesspress' ); ?></button>
						</div>
					</div>
				</form>
			</div>
		</div>
		<?php
	}

	/**
	 * Validate and normalize a group slug.
	 *
	 * @param mixed $value Raw slug input.
	 * @return string Sanitized slug.
	 */
	private function valid_slug( $value ): string {
		$slug = strtolower( (string) SanitizationHelper::text( $value ) );
		return preg_match( '/^[a-z0-9_-]+$/', $slug ) ? $slug : '';
	}

	/**
	 * Validate a group name.
	 *
	 * @param mixed $value Raw group name.
	 * @return string Sanitized group name.
	 */
	private function valid_name( $value ): string {
		$name = trim( (string) SanitizationHelper::text( $value ) );
		return preg_match( '/^[A-Za-z0-9 _-]+$/', $name ) ? $name : '';
	}

	/**
	 * Authorize viewing the groups manager.
	 *
	 * @return void
	 */
	private function authorize_view(): void {
		if ( ! PermissionHelper::can( 'accesspress_groups_view' ) ) {
			wp_die( esc_html__( 'You are not allowed to view groups.', 'accesspress' ), 403 );
		}
	}

	/**
	 * Authorize an action by capability and nonce.
	 *
	 * @param string $action     Action name.
	 * @param string $capability Required capability.
	 * @return void
	 */
	private function authorize_action( string $action, string $capability ): void {
		if ( ! PermissionHelper::can( $capability ) ) {
			wp_die( esc_html__( 'You are not allowed to manage groups.', 'accesspress' ), 403 );
		}

		if ( ! check_admin_referer( $action, '_wpnonce', false ) ) {
			wp_die( esc_html__( 'Security check failed.', 'accesspress' ), 403 );
		}
	}

	/**
	 * Redirect back to the groups manager with a status key.
	 *
	 * @param string $status Redirect status.
	 * @return void
	 */
	private function redirect( string $status ): void {
		wp_safe_redirect(
			add_query_arg(
				array(
					'page' => self::PAGE,
					'tool' => 'user_groups',
					'group_status' => $status,
				),
				admin_url( 'admin.php' )
			)
		);
		exit;
	}
}