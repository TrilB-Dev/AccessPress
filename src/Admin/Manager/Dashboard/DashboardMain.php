<?php
/**
 * Dashboard main UI for the AccessPress plugin.
 *
 * @package AccessPress\Admin\Manager\Dashboard
 */
namespace AccessPress\Admin\Manager\Dashboard;

use AccessPress\Includes\UserManagement\Groups\Groups;
use AccessPress\Includes\UserManagement\Roles\Roles;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class DashboardMain extends DashboardManager {
	/**
	 * Render the dashboard UI content.
	 *
	 * @return void
	 */
	public function render_page_content(): void {
		$this->header( __( 'AccessPress Dashboard', 'accesspress' ) );
		$this->render_summary();
		$this->render_cards();
		$this->footer();
	}

	/**
	 * Render the key AccessPress overview metrics for users, groups, and roles.
	 *
	 * @return void
	 */
	protected function render_summary(): void {
		$total_users = count_users();
		$user_count  = (int) ( $total_users['total_users'] ?? 0 );
		$group_count = count( Groups::get_group_definitions() );
		$role_count  = count( Roles::get_available_roles() );
		$max_roles   = Roles::get_max_roles();
		$cards       = array(
			array(
				'label'       => __( 'Users', 'accesspress' ),
				'value'       => $user_count,
				'url'         => admin_url( 'users.php' ),
				'description' => __( 'Total WordPress users managed by the site.', 'accesspress' ),
				'icon'        => 'dashicons-admin-users',
			),
			array(
				'label'       => __( 'Groups', 'accesspress' ),
				'value'       => $group_count,
				'url'         => admin_url( 'admin.php?page=accesspress&group=user-management&tab=user-groups' ),
				'description' => __( 'Configured AccessPress membership groups.', 'accesspress' ),
				'icon'        => 'dashicons-groups',
			),
			array(
				'label'       => __( 'Roles', 'accesspress' ),
				'value'       => $role_count,
				'url'         => admin_url( 'admin.php?page=accesspress&group=user-management&tab=user-roles' ),
				'description' => __( 'Available WordPress roles in the AccessPress role layer.', 'accesspress' ),
				'icon'        => 'dashicons-shield',
			),
			array(
				'label'       => __( 'Max per user', 'accesspress' ),
				'value'       => $max_roles,
				'url'         => admin_url( 'admin.php?page=accesspress&group=settings&tab=access' ),
				'description' => __( 'Maximum linked roles allowed on one account.', 'accesspress' ),
				'icon'        => 'dashicons-admin-network',
			),
		);
		?>
		<section class="mb-4" aria-labelledby="accesspress-dashboard-summary">
			<div class="d-flex justify-content-between align-items-center mb-3">
				<h2 id="accesspress-dashboard-summary" class="h5 mb-0"><?php esc_html_e( 'AccessPress overview', 'accesspress' ); ?></h2>
				<span class="small text-secondary"><?php esc_html_e( 'Frontend user management health', 'accesspress' ); ?></span>
			</div>
			<div class="row g-3">
				<?php foreach ( $cards as $card ) : ?>
					<div class="col-md-6 col-xl-3">
						<a class="accesspress-summary-card h-100 d-flex flex-column gap-1" href="<?php echo esc_url( $card['url'] ?? '' ); ?>">
							<span class="accesspress-summary-icon dashicons <?php echo esc_attr( $card['icon'] ?? 'dashicons-admin-generic' ); ?>" aria-hidden="true"></span>
							<span class="text-uppercase small fw-semibold text-secondary"><?php echo esc_html( $card['label'] ?? __( 'Metric', 'accesspress' ) ); ?></span>
							<strong class="h4 mb-0"><?php echo esc_html( (string) ( $card['value'] ?? 0 ) ); ?></strong>
							<span class="small text-secondary"><?php echo esc_html( $card['description'] ?? '' ); ?></span>
						</a>
					</div>
				<?php endforeach; ?>
			</div>
		</section>
		<?php
	}

	/**
	 * Render quick actions for the AccessPress portal configuration.
	 *
	 * @return void
	 */
	protected function render_cards(): void {
		$cards = array(
			array(
				'title'       => __( 'User groups', 'accesspress' ),
				'description' => __( 'Create and manage membership groups for access control and portal segmentation.', 'accesspress' ),
				'icon'        => 'dashicons-groups',
				'url'         => admin_url( 'admin.php?page=accesspress&group=user-management&tab=user-groups' ),
				'priority'    => 10,
			),
			array(
				'title'       => __( 'User roles', 'accesspress' ),
				'description' => __( 'Adjust role combinations and ensure users stay within the configured maximum role count.', 'accesspress' ),
				'icon'        => 'dashicons-shield',
				'url'         => admin_url( 'admin.php?page=accesspress&group=user-management&tab=user-roles' ),
				'priority'    => 20,
			),
			array(
				'title'       => __( 'Login', 'accesspress' ),
				'description' => __( 'Review and manage the frontend login experience and authentication flow.', 'accesspress' ),
				'icon'        => 'dashicons-admin-network',
				'url'         => admin_url( 'admin.php?page=accesspress&group=user-management&tab=user-login' ),
				'priority'    => 30,
			),
			array(
				'title'       => __( 'Registration', 'accesspress' ),
				'description' => __( 'Configure the custom AccessPress registration and activation workflow.', 'accesspress' ),
				'icon'        => 'dashicons-plus-alt',
				'url'         => admin_url( 'admin.php?page=accesspress&group=user-management&tab=user-registration' ),
				'priority'    => 40,
			),
			array(
				'title'       => __( 'Profile', 'accesspress' ),
				'description' => __( 'Review the frontend account profile experience and user details management.', 'accesspress' ),
				'icon'        => 'dashicons-id',
				'url'         => admin_url( 'admin.php?page=accesspress&group=user-management&tab=user-profile' ),
				'priority'    => 50,
			),
			array(
				'title'       => __( 'Access settings', 'accesspress' ),
				'description' => __( 'Update portal-wide security, access restrictions, and default member controls.', 'accesspress' ),
				'icon'        => 'dashicons-lock',
				'url'         => admin_url( 'admin.php?page=accesspress&group=settings&tab=access' ),
				'priority'    => 60,
			),
		);

		usort(
			$cards,
			static fn( array $left, array $right ): int => (int) ( $left['priority'] ?? 100 ) <=> (int) ( $right['priority'] ?? 100 )
		);
		?>
		<section aria-labelledby="accesspress-dashboard-cards">
			<div class="d-flex justify-content-between align-items-center mb-3">
				<h2 id="accesspress-dashboard-cards" class="h5 mb-0"><?php esc_html_e( 'Quick actions', 'accesspress' ); ?></h2>
				<span class="small text-secondary"><?php esc_html_e( 'Manage the core access experience', 'accesspress' ); ?></span>
			</div>
			<div class="row g-3">
				<?php foreach ( $cards as $card ) : ?>
					<div class="col-md-6 col-xl-4">
						<a class="accesspress-summary-card h-100 d-flex flex-column gap-1" href="<?php echo esc_url( $card['url'] ?? '' ); ?>">
							<span class="accesspress-summary-icon dashicons <?php echo esc_attr( $card['icon'] ?? 'dashicons-admin-generic' ); ?>" aria-hidden="true"></span>
							<span class="fw-semibold text-body"><?php echo esc_html( $card['title'] ?? __( 'Action', 'accesspress' ) ); ?></span>
							<span class="small text-secondary"><?php echo esc_html( $card['description'] ?? '' ); ?></span>
						</a>
					</div>
				<?php endforeach; ?>
			</div>
		</section>
		<?php
	}
}