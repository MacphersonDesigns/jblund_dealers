<?php
/**
 * Menu Visibility Handler Class
 *
 * Handles role-based menu visibility for dealer portal:
 * - Hide menu items from dealers
 * - Show dealer-only menu items
 *
 * Adapted from hide-menu-items-by-role plugin
 *
 * @package JBLund_Dealers
 * @subpackage Dealer_Portal
 * @since 1.0.0
 */

namespace JBLund\DealerPortal;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Menu Visibility class for dealer portal
 */
class Menu_Visibility {

	/**
	 * Meta key for menu item visibility settings
	 *
	 * @var string
	 */
	private $meta_key = '_menu_item_dealer_visibility';

	/**
	 * Constructor
	 */
	public function __construct() {
		// Hook into WordPress menu system
		\add_filter( 'wp_nav_menu_objects', array( $this, 'filter_menu_items' ), 10, 2 );
		\add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'add_menu_item_fields' ), 10, 4 );
		\add_action( 'wp_update_nav_menu_item', array( $this, 'save_menu_item_fields' ), 10, 2 );
	}

	/**
	 * Filter menu items based on dealer role visibility
	 *
	 * Adapted from: hmi_hide_menu_items_based_on_role()
	 * Simplified for single dealer role (vs multi-role array checking)
	 *
	 * @param array    $items Menu items.
	 * @param \stdClass $args  Menu arguments.
	 * @return array Filtered menu items.
	 */
	public function filter_menu_items( $items, $args ) {
		// Get current user
		$user = \wp_get_current_user();

		// Allow administrators and staff to see all menu items
		if ( Dealer_Role::can_bypass_dealer_restrictions( $user ) ) {
			return $items; // Show everything to admin/staff
		}

		$is_dealer = Dealer_Role::is_dealer( $user );

		// Filter items based on visibility settings
		foreach ( $items as $key => $item ) {
			$visibility = \get_post_meta( $item->ID, $this->meta_key, true );

			// Skip items without visibility settings
			if ( empty( $visibility ) || $visibility === 'all' ) {
				continue;
			}

			// Hide from dealers
			if ( $visibility === 'hide_from_dealers' && $is_dealer ) {
				unset( $items[ $key ] );
			}

			// Show only to dealers (hide from non-dealers, except admin/staff already returned above)
			if ( $visibility === 'dealers_only' && ! $is_dealer ) {
				unset( $items[ $key ] );
			}
		}

		return $items;
	}

	/**
	 * Add visibility fields to menu item settings
	 *
	 * @param int      $item_id Menu item ID.
	 * @param \WP_Post  $item    Menu item data object.
	 * @param int      $depth   Depth of menu item.
	 * @param \stdClass $args    Menu item args.
	 * @return void
	 */
	public function add_menu_item_fields( $item_id, $item, $depth, $args ) {
		$visibility = \get_post_meta( $item_id, $this->meta_key, true );
		if ( empty( $visibility ) ) {
			$visibility = 'all';
		}
		?>
		<p class="field-dealer-visibility description description-wide">
			<label for="menu-item-dealer-visibility-<?php echo \esc_attr( $item_id ); ?>">
				<?php \esc_html_e( 'Dealer Visibility', 'jblund-dealers' ); ?><br>
				<select
					name="menu-item-dealer-visibility[<?php echo \esc_attr( $item_id ); ?>]"
					id="menu-item-dealer-visibility-<?php echo \esc_attr( $item_id ); ?>"
					class="widefat"
				>
					<option value="all" <?php \selected( $visibility, 'all' ); ?>>
						<?php \esc_html_e( 'Everyone', 'jblund-dealers' ); ?>
					</option>
					<option value="dealers_only" <?php \selected( $visibility, 'dealers_only' ); ?>>
						<?php \esc_html_e( 'Dealers Only', 'jblund-dealers' ); ?>
					</option>
					<option value="hide_from_dealers" <?php \selected( $visibility, 'hide_from_dealers' ); ?>>
						<?php \esc_html_e( 'Hide from Dealers', 'jblund-dealers' ); ?>
					</option>
				</select>
			</label>
		</p>
		<?php
	}

	/**
	 * Save menu item visibility fields
	 *
	 * @param int $menu_id Menu ID.
	 * @param int $item_id Menu item ID.
	 * @return void
	 */
	public function save_menu_item_fields( $menu_id, $item_id ) {
		// Check if our field is set
		if ( ! isset( $_POST['menu-item-dealer-visibility'][ $item_id ] ) ) {
			return;
		}

		// Sanitize and save
		$visibility = \sanitize_text_field( \wp_unslash( $_POST['menu-item-dealer-visibility'][ $item_id ] ) );

		// Validate value
		$allowed_values = array( 'all', 'dealers_only', 'hide_from_dealers' );
		if ( ! \in_array( $visibility, $allowed_values, true ) ) {
			$visibility = 'all';
		}

		\update_post_meta( $item_id, $this->meta_key, $visibility );
	}
}
