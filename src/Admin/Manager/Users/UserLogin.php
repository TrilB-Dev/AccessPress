<?php
/**
 * User login screen UI for AccessPress.
 *
 * @package AccessPress
 * @subpackage Admin\Manager\Users
 */
namespace AccessPress\Admin\Manager\Users;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class UserLogin extends UserManager {
	/**
	 * Prevent recursive parent initialization when the login page is instantiated.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct( false );
	}

	/**
	 * Render the login management screen.
	 *
	 * @return void
	 */
	public function render_page_content(): void {
		?>
		<div class="card shadow-sm">
			<div class="card-body">
				<h2 class="h5 mb-3"><?php esc_html_e( 'Login settings', 'accesspress' ); ?></h2>
				<p class="text-secondary mb-0"><?php esc_html_e( 'Configure the frontend login experience, redirect rules, and default login behavior.', 'accesspress' ); ?></p>
			</div>
		</div>
		<?php
	}
}