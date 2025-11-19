<?php
/**
 * Dealer Role Management
 *
 * Handles creation and management of the custom dealer role.
 *
 * @package JBLund_Dealers
 * @subpackage Dealer_Portal
 * @since 1.1.0
 */

namespace JBLund\DealerPortal;

// Exit if accessed directly
if (!\defined('ABSPATH')) {
    exit;
}

/**
 * Class Dealer_Role
 *
 * Manages the custom dealer user role with specific capabilities.
 */
class Dealer_Role {

    /**
     * Role slug
     *
     * @var string
     */
    const ROLE_SLUG = 'dealer';

    /**
     * Role display name
     *
     * @var string
     */
    const ROLE_NAME = 'Dealer';

    /**
     * Constructor
     */
    public function __construct() {
        // Role is created during plugin activation, not on every page load
    }

    /**
     * Create the dealer role
     *
     * Called during plugin activation.
     *
     * @return void
     */
    public static function create_role() {
        // Check if role already exists
        if (\get_role(self::ROLE_SLUG)) {
            // Role exists, update capabilities
            self::update_capabilities();
            return;
        }

        // Define dealer capabilities
        $capabilities = self::get_capabilities();

        // Create the role
        \add_role(
            self::ROLE_SLUG,
            self::ROLE_NAME,
            $capabilities
        );
    }

    /**
     * Remove the dealer role
     *
     * Called during plugin uninstall (not deactivation).
     *
     * @return void
     */
    public static function remove_role() {
        \remove_role(self::ROLE_SLUG);
    }

    /**
     * Update role capabilities
     *
     * Useful for updating existing roles with new capabilities.
     *
     * @return void
     */
    public static function update_capabilities() {
        $role = \get_role(self::ROLE_SLUG);
        if (!$role) {
            return;
        }

        $capabilities = self::get_capabilities();

        // Remove all existing capabilities
        foreach ($role->capabilities as $cap => $granted) {
            $role->remove_cap($cap);
        }

        // Add current capabilities
        foreach ($capabilities as $cap => $granted) {
            if ($granted) {
                $role->add_cap($cap);
            }
        }
    }

    /**
     * Get dealer capabilities
     *
     * Defines what dealers can and cannot do.
     *
     * @return array Associative array of capability => bool
     */
    private static function get_capabilities() {
        return array(
            // Basic WordPress capabilities
            'read'                   => true,  // View admin dashboard
            'read_private_pages'     => false,
            'read_private_posts'     => false,

            // Dealer Portal specific capabilities
            'view_dealer_portal'     => true,  // Access dealer portal pages
            'download_dealer_resources' => true,  // Download files from resource library
            'view_dealer_pricing'    => true,  // View pricing information
            'manage_dealer_profile'  => true,  // Edit own profile
            'view_territory_info'    => true,  // View assigned territory data

            // Editing capabilities (dealers cannot publish content)
            'edit_posts'             => false,
            'edit_pages'             => false,
            'edit_published_posts'   => false,
            'publish_posts'          => false,
            'delete_posts'           => false,
            'upload_files'           => false,

            // Admin capabilities (dealers have none)
            'manage_categories'      => false,
            'manage_options'         => false,
            'moderate_comments'      => false,
            'edit_users'             => false,
            'delete_users'           => false,
            'create_users'           => false,
        );
    }

    /**
     * Check if user is a dealer
     *
     * Utility method to check if a user has the dealer role.
     *
     * @param int|\WP_User|null $user User ID, WP_User object, or null for current user.
     * @return bool True if user is a dealer, false otherwise.
     */
    public static function is_dealer($user = null) {
        if ($user === null) {
            $user = \wp_get_current_user();
        } elseif (\is_numeric($user)) {
            $user = \get_user_by('id', $user);
        }

        if (!$user || !($user instanceof \WP_User)) {
            return false;
        }

        return \in_array(self::ROLE_SLUG, (array) $user->roles, true);
    }

    /**
     * Check if user can bypass dealer restrictions
     *
     * Administrators and Staff roles should have unrestricted access
     * to dealer portal pages without NDA requirements.
     *
     * @param int|\WP_User|null $user User ID, WP_User object, or null for current user.
     * @return bool True if user can bypass restrictions, false otherwise.
     */
    public static function can_bypass_dealer_restrictions($user = null) {
        if ($user === null) {
            $user = \wp_get_current_user();
        } elseif (\is_numeric($user)) {
            $user = \get_user_by('id', $user);
        }

        if (!$user || !($user instanceof \WP_User)) {
            return false;
        }

        // Allow administrators and staff to bypass all dealer restrictions
        $elevated_roles = array('administrator', 'staff');

        foreach ($elevated_roles as $role) {
            if (\in_array($role, (array) $user->roles, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has dealer capability
     *
     * @param string $capability Capability to check.
     * @param int|\WP_User|null $user User ID, WP_User object, or null for current user.
     * @return bool True if user has the capability, false otherwise.
     */
    public static function has_capability($capability, $user = null) {
        if ($user === null) {
            return \current_user_can($capability);
        } elseif (\is_numeric($user)) {
            $user = \get_user_by('id', $user);
        }

        if (!$user || !($user instanceof \WP_User)) {
            return false;
        }

        return $user->has_cap($capability);
    }

    /**
     * Get all users with dealer role
     *
     * @param array $args Optional. Arguments to pass to get_users().
     * @return array Array of WP_User objects.
     */
    public static function get_dealers($args = array()) {
        $defaults = array(
            'role' => self::ROLE_SLUG,
            'orderby' => 'display_name',
            'order' => 'ASC',
        );

        $args = \wp_parse_args($args, $defaults);

        return \get_users($args);
    }

    /**
     * Assign dealer role to user
     *
     * Removes all other roles and assigns dealer role.
     *
     * @param int $user_id User ID.
     * @return bool True on success, false on failure.
     */
    public static function assign_to_user($user_id) {
        $user = \get_user_by('id', $user_id);
        if (!$user) {
            return false;
        }

        // Remove all existing roles
        foreach ($user->roles as $role) {
            $user->remove_role($role);
        }

        // Add dealer role
        $user->add_role(self::ROLE_SLUG);

        return true;
    }

    /**
     * Remove dealer role from user
     *
     * Assigns subscriber role as fallback.
     *
     * @param int $user_id User ID.
     * @return bool True on success, false on failure.
     */
    public static function remove_from_user($user_id) {
        $user = \get_user_by('id', $user_id);
        if (!$user) {
            return false;
        }

        // Remove dealer role
        $user->remove_role(self::ROLE_SLUG);

        // If user has no roles, assign subscriber
        if (empty($user->roles)) {
            $user->add_role('subscriber');
        }

        return true;
    }
}
