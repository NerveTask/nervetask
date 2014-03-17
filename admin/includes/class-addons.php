<?php
/**
 * Addons Page
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'NerveTask_Addons' ) ) :

/**
 * NerveTask_Addons Class
 */
class NerveTask_Addons {

	/**
	 * Handles output of the reports page in admin.
	 */
	public function output() {

		if ( false === ( $addons = get_transient( 'nervetask_addons_html' ) ) ) {

			$raw_addons = wp_remote_get( 'http://nervetask.com/add-ons/' );

			if ( ! is_wp_error( $raw_addons ) ) {

				$raw_addons = wp_remote_retrieve_body( $raw_addons );

				// Get Products
				$dom = new DOMDocument();
				libxml_use_internal_errors(true);
				$dom->loadHTML( $raw_addons );

				$xpath  = new DOMXPath( $dom );
				$tags   = $xpath->query('//ul[@class="items"]');
				foreach ( $tags as $tag ) {
					$addons = $tag->ownerDocument->saveXML( $tag );
					break;
				}

				$addons = wp_kses_post( $addons );

				if ( $addons )
					set_transient( 'nervetask_addons_html', $addons, 60*60*24*7 ); // Cached for a week
			}
		}

		?>
		<div class="wrap nervetask nervetask_addons_wrap">
			<div class="icon32 icon32-posts-job_listing" id="icon-edit"><br /></div>
			<h2><?php _e( 'NerveTask Add-ons', 'nervetask' ); ?></h2>
			<?php echo $addons; ?>
		</div>
		<?php
	}
}

endif;

return new NerveTask_Addons();