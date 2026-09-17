<?php
/**
 * Admin settings page for the Demo plugin.
 *
 * @package AccessPress
 * @subpackage Includes\Plugins\Demo\Admin
 * @since 1.0.0
 */
namespace AccessPress\Includes\Plugins\Demo\Admin;

use AccessPress\Includes\Functions\Helpers\AlertHelper;
use AccessPress\Includes\Functions\Helpers\FormFieldHelper;
use AccessPress\Includes\Functions\Helpers\RequestHelper;
use AccessPress\Includes\Settings\Settings as BaseSettings;

final class DemoSettings {
    public function render(): void {
        $settings = BaseSettings::get_group( 'demo', [] ) ?? [];
        $profiles = is_array( $settings['demo_admin_settings'] ?? null ) ? $settings['demo_admin_settings'] : [];
        $nonce = wp_create_nonce( 'accesspress_demo_settings' );
        ?>
        <div class="accesspress-demo-settings card shadow-sm" data-demo-settings data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-nonce="<?php echo esc_attr( $nonce ); ?>">
            <?php if ( '' !== RequestHelper::get_text( 'updated' ) ) : ?>
                <?php AlertHelper::admin_success( __( 'Demo settings saved.', 'accesspress' ) ); ?>
            <?php endif; ?>

            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="accesspress-demo-form">
               
                <div class="card-body">
                    <?php echo FormFieldHelper::input( 'action', 'accesspress_demo_save_settings', [ 'type' => 'hidden' ] ); ?>
                    <?php wp_nonce_field( 'accesspress_demo_save_settings' ); ?>

                    <div class="mb-3">
                        <?php echo FormFieldHelper::switch( 'settings[enabled]', '1', __( 'Demo Integration', 'accesspress' ), [ 'checked' => ! empty( $settings['enabled'] ) ] ); ?>
                    </div>
                    <h3 class="h6 mt-4"><?php esc_html_e( 'Demo Admin', 'accesspress' ); ?></h3>

                    <p class="mb-0">
                        
                    </p>
                </div>

                <div class="card-footer">
                    <?php echo FormFieldHelper::button( __( 'Save Demo settings', 'accesspress' ), [ 'type' => 'submit' ] ); ?>
                </div>
            </form>
        </div>
        <?php
    }

}
