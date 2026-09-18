<?php
/**
 * User registration screen UI for AccessPress.
 *
 * @package AccessPress
 * @subpackage Admin\Manager\Users
 */
namespace AccessPress\Admin\Manager\Users;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class UserRegistration extends UserManager {
	/**
	 * Prevent recursive parent initialization when the registration page is instantiated.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct( false );
	}

	/**
	 * Render the registration management screen.
	 *
	 * @return void
	 */
	public function render_page_content(): void {
		?>
		<div class="card shadow-sm">
			<div class="card-body">
				<h2 class="h5 mb-3"><?php esc_html_e( 'Registration settings', 'accesspress' ); ?></h2>
				<p class="text-secondary mb-0"><?php esc_html_e( 'Define the automatic user registration flow, approval rules, and default account behavior.', 'accesspress' ); ?></p>
			</div>
		</div>
		<?php
	}
}