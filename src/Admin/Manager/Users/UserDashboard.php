<?php
/**
 * User dashboard UI for AccessPress.
 *
 * @package AccessPress
 * @subpackage Admin\Manager\Users
 */
namespace AccessPress\Admin\Manager\Users;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class UserDashboard extends UserManager {
	/**
	 * Prevent recursive parent initialization when the dashboard page is instantiated.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct( false );
	}

	/**
	 * Render the user dashboard page content.
	 *
	 * @return void
	 */
	public function render_page_content(): void {
		$total_users = count_users();
		$user_count  = (int) ( $total_users['total_users'] ?? 0 );
		$roles_count = count( wp_roles()->roles );
		$groups_count = 0;
		$cards       = array(
			array(
				'label'       => __( 'Total users', 'accesspress' ),
				'value'       => $user_count,
				'description' => __( 'All users currently registered on the site.', 'accesspress' ),
			),
			array(
				'label'       => __( 'Groups', 'accesspress' ),
				'value'       => $groups_count,
				'description' => __( 'AccessPress user groups available for membership segmentation.', 'accesspress' ),
			),
			array(
				'label'       => __( 'Roles', 'accesspress' ),
				'value'       => $roles_count,
				'description' => __( 'WordPress roles configured for the AccessPress user layer.', 'accesspress' ),
			),
			array(
				'label'       => __( 'Profile actions', 'accesspress' ),
				'value'       => 3,
				'description' => __( 'Primary profile and registration flows available.', 'accesspress' ),
			),
		);
		?>
		<div class="row g-3 mb-4">
			<?php foreach ( $cards as $card ) : ?>
				<div class="col-md-6 col-xl-3">
					<div class="card h-100 shadow-sm border-0">
						<div class="card-body">
							<div class="small text-uppercase text-muted"><?php echo esc_html( $card['label'] ); ?></div>
							<div class="display-6 mb-1"><?php echo esc_html( (string) $card['value'] ); ?></div>
							<p class="text-secondary small mb-0"><?php echo esc_html( $card['description'] ); ?></p>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="row g-4">
			<div class="col-xl-6">
				<div class="card shadow-sm h-100">
					<div class="card-body">
						<h2 class="h5 mb-3"><?php esc_html_e( 'Overview', 'accesspress' ); ?></h2>
						<ul class="list-group list-group-flush">
							<li class="list-group-item px-0"><strong><?php esc_html_e( 'Login flow', 'accesspress' ); ?>:</strong> <?php esc_html_e( 'Frontend login screen ready for customization.', 'accesspress' ); ?></li>
							<li class="list-group-item px-0"><strong><?php esc_html_e( 'Registration', 'accesspress' ); ?>:</strong> <?php esc_html_e( 'Registration settings can be managed per site policy.', 'accesspress' ); ?></li>
							<li class="list-group-item px-0"><strong><?php esc_html_e( 'Profiles', 'accesspress' ); ?>:</strong> <?php esc_html_e( 'User profile management is available through the public portal.', 'accesspress' ); ?></li>
						</ul>
					</div>
				</div>
			</div>
			<div class="col-xl-6">
				<div class="card shadow-sm h-100">
					<div class="card-body">
						<h2 class="h5 mb-3"><?php esc_html_e( 'Quick actions', 'accesspress' ); ?></h2>
						<div class="d-grid gap-2">
							<a class="btn btn-outline-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=accesspress&group=user-management&tab=groups' ) ); ?>"><?php esc_html_e( 'Manage groups', 'accesspress' ); ?></a>
							<a class="btn btn-outline-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=accesspress&group=user-management&tab=roles' ) ); ?>"><?php esc_html_e( 'Manage roles', 'accesspress' ); ?></a>
							<a class="btn btn-outline-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=accesspress&group=user-management&tab=login' ) ); ?>"><?php esc_html_e( 'Review login settings', 'accesspress' ); ?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}