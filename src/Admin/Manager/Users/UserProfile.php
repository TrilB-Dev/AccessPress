<?php
/**
 * User profile screen UI for AccessPress.
 *
 * @package AccessPress
 * @subpackage Admin\Manager\Users
 */
namespace AccessPress\Admin\Manager\Users;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class UserProfile extends UserManager {
	/**
	 * Prevent recursive parent initialization when the profile page is instantiated.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct( false );
	}

	/**
	 * Render the profile management screen.
	 *
	 * @return void
	 */
	public function render_page_content(): void {
		?>
		<div class="card shadow-sm">
			<div class="card-body">
				<h2 class="h5 mb-3"><?php esc_html_e( 'Profile settings', 'accesspress' ); ?></h2>
				<p class="text-secondary mb-0"><?php esc_html_e( 'Set the profile fields and account experience shown to logged-in users on the frontend.', 'accesspress' ); ?></p>
			</div>
		</div>
		<?php
	}
}