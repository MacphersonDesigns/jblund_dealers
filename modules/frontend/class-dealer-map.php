<?php
/**
 * Dealer Map
 *
 * Registers the [jblund_dealer_map] shortcode and handles Google Maps
 * rendering with dealer location markers.
 *
 * @package    JBLund_Dealers
 * @subpackage Frontend
 * @since      2.2.0
 */

namespace JBLund\Frontend;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Dealer_Map
 */
class Dealer_Map {

    /**
     * Google Maps API key (read from plugin settings)
     *
     * @var string
     */
    private $api_key;

    /**
     * Constructor
     */
    public function __construct() {
        $this->api_key = $this->get_google_maps_api_key();
        add_shortcode('jblund_dealer_map', array($this, 'render_map'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * Retrieve the Google Maps API key from plugin settings.
     *
     * @return string API key or empty string if not configured
     */
    private function get_google_maps_api_key() {
        $options = get_option('jblund_dealers_settings', array());
        $key = isset($options['google_maps_api_key']) ? $options['google_maps_api_key'] : '';
        return sanitize_text_field($key);
    }

    /**
     * Enqueue map scripts only on pages that use the shortcode
     */
    public function enqueue_scripts() {
        global $post;

        if (!is_a($post, 'WP_Post') || !has_shortcode($post->post_content, 'jblund_dealer_map')) {
            return;
        }

        if (empty($this->api_key)) {
            return;
        }

        // Our initialisation script must load BEFORE the Maps API
        // so the jblundInitMap callback exists when Maps calls it
        wp_enqueue_script(
            'jblund-dealer-map',
            JBLUND_DEALERS_PLUGIN_URL . 'assets/js/dealer-map.js',
            array(),
            JBLUND_DEALERS_VERSION,
            true
        );

        // Google Maps API — depends on our script so it loads after
        wp_enqueue_script(
            'google-maps-api',
            'https://maps.googleapis.com/maps/api/js?key=' . esc_attr($this->api_key) . '&callback=jblundInitMap',
            array('jblund-dealer-map'),
            null,
            true
        );
    }

    /**
     * Render the dealer map shortcode
     *
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function render_map($atts) {
        $atts = shortcode_atts(array(
            'height' => '500px',
        ), $atts, 'jblund_dealer_map');

        if (empty($this->api_key)) {
            return '<p class="jblund-map-notice">'
                . esc_html__('Map unavailable: Google Maps API key not configured. Add your key under Dealers → Settings.', 'jblund-dealers')
                . '</p>';
        }

        $dealers = $this->get_dealers_with_coordinates();

        if (empty($dealers)) {
            return '<p class="jblund-map-notice">'
                . esc_html__('No dealers with location data found.', 'jblund-dealers')
                . '</p>';
        }

        wp_localize_script('jblund-dealer-map', 'jblundMapData', array(
            'dealers' => $dealers,
            'icons'   => array(
                'docks'    => jblund_get_service_icon_url('docks'),
                'lifts'    => jblund_get_service_icon_url('lifts'),
                'trailers' => jblund_get_service_icon_url('trailers'),
            ),
        ));

        $height = sanitize_text_field($atts['height']);

        return '<div id="jblund-dealer-map" style="width:100%;height:' . esc_attr($height) . ';" aria-label="' . esc_attr__('Dealer locations map', 'jblund-dealers') . '"></div>';
    }

    /**
     * Query all published dealers, resolving coordinates from meta or by
     * geocoding the address when lat/lng are not stored.
     *
     * Geocoded coordinates are cached back to post meta so subsequent page
     * loads do not incur an API call. Dealers whose geocoding fails (or who
     * have neither coordinates nor an address) are silently skipped.
     *
     * @return array Dealer data arrays
     */
    private function get_dealers_with_coordinates() {
        $query = new \WP_Query(array(
            'post_type'      => 'dealer',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => 'title',
            'order'          => 'ASC',
            'no_found_rows'  => true,
        ));

        $dealers = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID();

                $lat = get_post_meta($post_id, '_dealer_latitude', true);
                $lng = get_post_meta($post_id, '_dealer_longitude', true);

                // Fall back to geocoding when coordinates are missing
                if (empty($lat) || empty($lng)) {
                    $address = get_post_meta($post_id, '_dealer_company_address', true);

                    if (empty($address)) {
                        continue; // Nothing to geocode
                    }

                    // Skip dealers that previously failed geocoding
                    if (get_post_meta($post_id, '_dealer_geocode_failed', true)) {
                        continue;
                    }

                    $coords = $this->geocode_address($address);

                    if (!$coords) {
                        // Mark as failed so we don't retry on every page load
                        update_post_meta($post_id, '_dealer_geocode_failed', '1');
                        continue;
                    }

                    // Cache coordinates for future page loads
                    update_post_meta($post_id, '_dealer_latitude', $coords['lat']);
                    update_post_meta($post_id, '_dealer_longitude', $coords['lng']);

                    $lat = $coords['lat'];
                    $lng = $coords['lng'];
                }

                $dealers[] = array(
                    'id'       => $post_id,
                    'name'     => get_the_title(),
                    'address'  => get_post_meta($post_id, '_dealer_company_address', true),
                    'phone'    => get_post_meta($post_id, '_dealer_company_phone', true),
                    'website'  => get_post_meta($post_id, '_dealer_website', true),
                    'lat'      => (float) $lat,
                    'lng'      => (float) $lng,
                    'docks'    => get_post_meta($post_id, '_dealer_docks', true),
                    'lifts'    => get_post_meta($post_id, '_dealer_lifts', true),
                    'trailers' => get_post_meta($post_id, '_dealer_trailers', true),
                );
            }
            wp_reset_postdata();
        }

        return $dealers;
    }

    /**
     * Geocode an address string using the Google Maps Geocoding API.
     *
     * @param string $address Full address to geocode
     * @return array|false Associative array with 'lat' and 'lng' floats, or false on failure
     */
    private function geocode_address($address) {
        $url = add_query_arg(
            array(
                'address' => rawurlencode($address),
                'key'     => $this->api_key,
            ),
            'https://maps.googleapis.com/maps/api/geocode/json'
        );

        $response = wp_remote_get($url, array('timeout' => 5));

        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (
            empty($data['status']) ||
            $data['status'] !== 'OK' ||
            empty($data['results'][0]['geometry']['location'])
        ) {
            return false;
        }

        $location = $data['results'][0]['geometry']['location'];

        return array(
            'lat' => (float) $location['lat'],
            'lng' => (float) $location['lng'],
        );
    }
}
