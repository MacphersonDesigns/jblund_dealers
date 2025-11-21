<?php
/**
 * Dashboard Card Shortcodes
 *
 * Individual shortcodes for each dashboard card section.
 * These can be used in Divi Builder to create a custom dashboard layout.
 *
 * @package JBLund_Dealers
 * @subpackage Dealer_Portal
 * @since 1.4.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Quick Links Card
 *
 * Usage: [dealer_quick_links]
 */
function jblund_dealer_quick_links_shortcode($atts) {
    if (!is_user_logged_in()) {
        return '';
    }

    ob_start();
    ?>
    <div class="dashboard-card quick-links">
        <h2><?php esc_html_e('Quick Links', 'jblund-dealers'); ?></h2>
        <ul class="link-list">
            <li>
                <a href="<?php echo esc_url(jblund_get_portal_page_url('profile') ?: home_url('/dealer-profile/')); ?>" class="dashboard-link">
                    <span class="link-icon">👤</span>
                    <span class="link-text"><?php esc_html_e('My Profile', 'jblund-dealers'); ?></span>
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url(jblund_get_portal_page_url('nda') ?: home_url('/dealer-nda-acceptance/')); ?>" class="dashboard-link">
                    <span class="link-icon">📄</span>
                    <span class="link-text"><?php esc_html_e('View NDA', 'jblund-dealers'); ?></span>
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" class="dashboard-link logout-link">
                    <span class="link-icon">🚪</span>
                    <span class="link-text"><?php esc_html_e('Logout', 'jblund-dealers'); ?></span>
                </a>
            </li>
        </ul>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('dealer_quick_links', 'jblund_dealer_quick_links_shortcode');

/**
 * Account Status Card
 *
 * Usage: [dealer_account_status]
 */
function jblund_dealer_account_status_shortcode($atts) {
    if (!is_user_logged_in()) {
        return '';
    }

    $current_user = wp_get_current_user();
    $company_name = get_user_meta($current_user->ID, '_dealer_company_name', true);

    $nda_data = get_user_meta($current_user->ID, '_dealer_nda_acceptance', true);
    $nda_accepted = !empty($nda_data['accepted']);

    ob_start();
    ?>
    <div class="dashboard-card account-status">
        <h2><?php esc_html_e('Account Status', 'jblund-dealers'); ?></h2>
        <div class="status-info">
            <div class="status-item">
                <span class="status-label"><?php esc_html_e('Account Type:', 'jblund-dealers'); ?></span>
                <span class="status-value"><?php esc_html_e('Authorized Dealer', 'jblund-dealers'); ?></span>
            </div>
            <div class="status-item">
                <span class="status-label"><?php esc_html_e('NDA Status:', 'jblund-dealers'); ?></span>
                <span class="status-value status-<?php echo $nda_accepted ? 'accepted' : 'pending'; ?>">
                    <?php echo $nda_accepted ? '✓ ' . esc_html__('Accepted', 'jblund-dealers') : '⚠ ' . esc_html__('Pending', 'jblund-dealers'); ?>
                </span>
            </div>
            <?php if ($company_name) : ?>
            <div class="status-item">
                <span class="status-label"><?php esc_html_e('Company:', 'jblund-dealers'); ?></span>
                <span class="status-value"><?php echo esc_html($company_name); ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('dealer_account_status', 'jblund_dealer_account_status_shortcode');

/**
 * Signed Documents Card
 *
 * Usage: [dealer_signed_documents]
 */
function jblund_dealer_signed_documents_shortcode($atts) {
    if (!is_user_logged_in()) {
        return '';
    }

    $current_user = wp_get_current_user();
    $nda_data = get_user_meta($current_user->ID, '_dealer_nda_acceptance', true);
    $nda_accepted = !empty($nda_data['accepted']);
    $nda_date = $nda_accepted && !empty($nda_data['acceptance_date']) ? $nda_data['acceptance_date'] : '';
    $nda_pdf_url = get_user_meta($current_user->ID, '_dealer_nda_pdf_url', true);

    ob_start();
    ?>
    <div class="dashboard-card signed-documents">
        <h2><?php esc_html_e('Signed Documents', 'jblund-dealers'); ?></h2>
        <p class="card-description"><?php esc_html_e('View and download your signed documents.', 'jblund-dealers'); ?></p>
        <ul class="documents-list">
            <?php if ($nda_accepted) : ?>
            <li class="document-item">
                <span class="document-icon">📄</span>
                <div class="document-info">
                    <strong><?php esc_html_e('Non-Disclosure Agreement', 'jblund-dealers'); ?></strong>
                    <div class="document-meta">
                        <span class="document-status signed">✓ <?php esc_html_e('Signed', 'jblund-dealers'); ?></span>
                        <?php if ($nda_date) : ?>
                        <span class="document-date"><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($nda_date))); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="document-actions">
                        <a href="<?php echo esc_url(jblund_get_portal_page_url('nda') ?: home_url('/dealer-nda-acceptance/')); ?>" class="document-link">
                            <?php esc_html_e('View Document', 'jblund-dealers'); ?> →
                        </a>
                        <?php if ($nda_pdf_url) : ?>
                        <a href="<?php echo esc_url($nda_pdf_url); ?>" class="document-link download-link" download>
                            <?php esc_html_e('Download PDF', 'jblund-dealers'); ?> ⬇
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </li>
            <?php else : ?>
            <li class="no-documents">
                <p><?php esc_html_e('No signed documents yet.', 'jblund-dealers'); ?></p>
            </li>
            <?php endif; ?>
        </ul>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('dealer_signed_documents', 'jblund_dealer_signed_documents_shortcode');

/**
 * Documents to Complete Card
 *
 * Usage: [dealer_documents_to_complete]
 */
function jblund_dealer_documents_to_complete_shortcode($atts) {
    if (!is_user_logged_in()) {
        return '';
    }

    $settings = get_option('jblund_dealers_settings');
    $required_documents = isset($settings['required_documents']) ? $settings['required_documents'] : array();

    if (empty($required_documents)) {
        return '';
    }

    ob_start();
    ?>
    <div class="dashboard-card documents-to-complete">
        <h2><?php esc_html_e('Documents to Complete', 'jblund-dealers'); ?></h2>
        <p class="card-description"><?php esc_html_e('Please review and complete the following documents.', 'jblund-dealers'); ?></p>
        <ul class="documents-list">
            <?php
            foreach ($required_documents as $document) :
                if (empty($document['title'])) {
                    continue;
                }
                $doc_title = $document['title'];
                $doc_url = !empty($document['url']) ? $document['url'] : '#';
                $doc_description = !empty($document['description']) ? $document['description'] : '';
                ?>
                <li class="document-item pending">
                    <span class="document-icon">📋</span>
                    <div class="document-info">
                        <strong><?php echo esc_html($doc_title); ?></strong>
                        <?php if ($doc_description) : ?>
                        <p class="document-description"><?php echo esc_html($doc_description); ?></p>
                        <?php endif; ?>
                        <a href="<?php echo esc_url($doc_url); ?>" class="document-link primary-action">
                            <?php esc_html_e('Complete Form', 'jblund-dealers'); ?> →
                        </a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('dealer_documents_to_complete', 'jblund_dealer_documents_to_complete_shortcode');

/**
 * Resources Card
 *
 * Usage: [dealer_resources]
 * Attributes: resources="Product Catalog|/catalog/,Marketing Materials|/marketing/"
 */
function jblund_dealer_resources_shortcode($atts) {
    if (!is_user_logged_in()) {
        return '';
    }

    $atts = shortcode_atts(array(
        'resources' => '',
    ), $atts);

    // Parse resources from attribute
    $resources_list = array();
    if (!empty($atts['resources'])) {
        $items = explode(',', $atts['resources']);
        foreach ($items as $item) {
            $parts = explode('|', $item);
            if (count($parts) === 2) {
                $resources_list[] = array(
                    'title' => trim($parts[0]),
                    'url' => trim($parts[1])
                );
            }
        }
    }

    // Default resources if none provided
    if (empty($resources_list)) {
        $resources_list = array(
            array('title' => 'Product Catalog', 'url' => '#'),
            array('title' => 'Marketing Materials', 'url' => '#'),
            array('title' => 'Contact Support', 'url' => '#'),
        );
    }

    ob_start();
    ?>
    <div class="dashboard-card resources">
        <h2><?php esc_html_e('Resources', 'jblund-dealers'); ?></h2>
        <p class="card-description"><?php esc_html_e('Access dealer resources and materials here.', 'jblund-dealers'); ?></p>
        <ul class="resources-list">
            <?php foreach ($resources_list as $resource) : ?>
            <li>
                <a href="<?php echo esc_url($resource['url']); ?>" class="resource-link">
                    <span class="resource-icon">📚</span>
                    <span class="resource-title"><?php echo esc_html($resource['title']); ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('dealer_resources', 'jblund_dealer_resources_shortcode');

/**
 * Recent Updates Card
 *
 * Usage: [dealer_recent_updates]
 */
function jblund_dealer_recent_updates_shortcode($atts) {
    if (!is_user_logged_in()) {
        return '';
    }

    $settings = get_option('jblund_dealers_settings', array());
    $messages = get_option('jblund_registration_messages', array());

    $active_message = null;
    $today = current_time('Y-m-d');

    foreach ($messages as $message) {
        if (empty($message['start_date']) || empty($message['end_date'])) {
            continue;
        }

        if ($today >= $message['start_date'] && $today <= $message['end_date']) {
            $active_message = $message;
            break;
        }
    }

    ob_start();
    ?>
    <div class="dashboard-card recent-updates">
        <h2><?php esc_html_e('Recent Updates', 'jblund-dealers'); ?></h2>
        <p class="card-description"><?php esc_html_e('Stay informed about the latest news and announcements.', 'jblund-dealers'); ?></p>
        <?php if ($active_message) : ?>
        <div class="update-item">
            <div class="update-content">
                <?php echo wp_kses_post($active_message['message']); ?>
            </div>
        </div>
        <?php else : ?>
        <p class="no-updates"><?php esc_html_e('No new updates at this time.', 'jblund-dealers'); ?></p>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('dealer_recent_updates', 'jblund_dealer_recent_updates_shortcode');

/**
 * Dealer Representative Card
 *
 * Usage: [dealer_representative]
 */
function jblund_dealer_representative_shortcode($atts) {
    if (!is_user_logged_in()) {
        return '';
    }

    $current_user = wp_get_current_user();
    $dealer_rep = jblund_get_dealer_representative($current_user->ID);

    if (!$dealer_rep) {
        return '';
    }

    ob_start();
    ?>
    <div class="dashboard-card dealer-representative">
        <h2><?php esc_html_e('Your Dealer Representative', 'jblund-dealers'); ?></h2>
        <div class="rep-info">
            <div class="rep-item">
                <span class="rep-icon">👤</span>
                <div class="rep-details">
                    <strong><?php echo esc_html($dealer_rep['name']); ?></strong>
                </div>
            </div>
            <?php if (!empty($dealer_rep['phone'])) : ?>
            <div class="rep-item">
                <span class="rep-icon">📞</span>
                <div class="rep-details">
                    <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9]/', '', $dealer_rep['phone'])); ?>">
                        <?php echo esc_html($dealer_rep['phone']); ?>
                    </a>
                </div>
            </div>
            <?php endif; ?>
            <?php if (!empty($dealer_rep['email'])) : ?>
            <div class="rep-item">
                <span class="rep-icon">✉️</span>
                <div class="rep-details">
                    <a href="mailto:<?php echo esc_attr($dealer_rep['email']); ?>">
                        <?php echo esc_html($dealer_rep['email']); ?>
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('dealer_representative', 'jblund_dealer_representative_shortcode');

/**
 * Welcome Header
 *
 * Usage: [dealer_welcome_header]
 */
function jblund_dealer_welcome_header_shortcode($atts) {
    if (!is_user_logged_in()) {
        return '';
    }

    $current_user = wp_get_current_user();
    $company_name = get_user_meta($current_user->ID, '_dealer_company_name', true);
    $display_name = $current_user->display_name;

    ob_start();
    ?>
    <div class="dashboard-header">
        <h1><?php esc_html_e('Dealer Dashboard', 'jblund-dealers'); ?></h1>
        <p class="welcome-message">
            <?php
            printf(
                esc_html__('Welcome back, %s!', 'jblund-dealers'),
                esc_html($company_name ? $company_name : $display_name)
            );
            ?>
        </p>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('dealer_welcome_header', 'jblund_dealer_welcome_header_shortcode');
