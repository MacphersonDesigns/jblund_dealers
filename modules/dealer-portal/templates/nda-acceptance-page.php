<?php
/**
 * NDA Acceptance Page Template
 *
 * Frontend form for dealers to accept NDA with digital signature.
 * This template is loaded via the [jblund_nda_acceptance] shortcode.
 *
 * @package JBLund_Dealers
 * @subpackage Dealer_Portal
 * @since 1.1.0
 */

// Exit if accessed directly
if (!\defined('ABSPATH')) {
    exit;
}

// Get current user
$current_user = \wp_get_current_user();
$dealer_name = \get_user_meta($current_user->ID, 'dealer_company_name', true);
if (empty($dealer_name)) {
    $dealer_name = $current_user->display_name . "'s Company";
}

// Check if NDA already accepted
$nda_accepted = false;
$acceptance_date = '';
$acceptance_data = null;
$just_accepted = isset($_GET['nda_accepted']) && $_GET['nda_accepted'] === '1';

if (class_exists('JBLund\DealerPortal\NDA_Acceptance_Manager')) {
    $acceptance_manager = new \JBLund\DealerPortal\NDA_Acceptance_Manager();
    $nda_accepted = $acceptance_manager->is_accepted($current_user->ID);
    if ($nda_accepted) {
        $acceptance_data = $acceptance_manager->get_acceptance_data($current_user->ID);
        $acceptance_date = isset($acceptance_data['acceptance_date']) ? $acceptance_data['acceptance_date'] : '';
    }
}

// Get NDA content - check if using PDF version or text version
$use_pdf = false;
$pdf_url = '';
$nda_content = '';

if (class_exists('JBLund\DealerPortal\NDA_Editor')) {
    $use_pdf = \JBLund\DealerPortal\NDA_Editor::is_using_pdf();
    if ($use_pdf) {
        $pdf_url = \JBLund\DealerPortal\NDA_Editor::get_pdf_url();
    } else {
        $nda_content = \JBLund\DealerPortal\NDA_Editor::get_content();
    }
}

// Fallback default if NDA_Editor not loaded or no content
if (!$use_pdf && empty($nda_content)) {
    $nda_content = \__(
        '<h2>Non-Disclosure Agreement</h2>
        <p>This Agreement is entered into by and between JB Lund Company ("Disclosing Party") and ' . \esc_html($dealer_name) . ' ("Receiving Party").</p>
        <h3>1. Purpose</h3>
        <p>The parties wish to explore a business opportunity of mutual interest...</p>
        <h3>2. Confidential Information</h3>
        <ul>
            <li>Pricing information and dealer cost structures</li>
            <li>Product specifications and technical documentation</li>
            <li>Territory assignments and dealer network information</li>
        </ul>
        <h3>3. Obligations</h3>
        <ul>
            <li>Hold and maintain the Confidential Information in strict confidence</li>
            <li>Not disclose the Confidential Information to any third parties</li>
        </ul>
        <h3>4. Term</h3>
        <p>This Agreement shall remain in effect for the duration of the dealer relationship.</p>
        <h3>5. Return of Materials</h3>
        <p>Upon termination, return or destroy all Confidential Information.</p>',
        'jblund-dealers'
    );
}

$allowed_html = [
    'p' => ['class' => [], 'style' => []],
    'h1' => ['class' => []],
    'h2' => ['class' => []],
    'h3' => ['class' => []],
    'h4' => ['class' => []],
    'strong' => [],
    'em' => [],
    'ul' => ['class' => []],
    'ol' => ['class' => []],
    'li' => [],
    'br' => [],
    'div' => ['class' => [], 'style' => []],
    'span' => ['class' => [], 'style' => []],
    'a' => ['href' => [], 'target' => [], 'class' => []],
];


?>

<div class="dealer-nda-acceptance">
    <?php if ($just_accepted): ?>
        <div class="nda-success-banner">
            <div class="success-icon">✓</div>
            <h2><?php \esc_html_e('Agreement Successfully Accepted!', 'jblund-dealers'); ?></h2>
            <p><?php \esc_html_e('Thank you for accepting the Non-Disclosure Agreement. You now have full access to the dealer portal.', 'jblund-dealers'); ?></p>
        </div>
    <?php endif; ?>

    <div class="nda-header">
        <h1><?php \esc_html_e('Non-Disclosure Agreement', 'jblund-dealers'); ?></h1>
        <?php if ($nda_accepted): ?>
            <p class="nda-subtitle nda-accepted-notice">
                <?php \esc_html_e('✓ You accepted this agreement on', 'jblund-dealers'); ?>
                <?php echo \esc_html(\date_i18n(\get_option('date_format'), \strtotime($acceptance_date))); ?>
            </p>
        <?php else: ?>
            <p class="nda-subtitle"><?php \esc_html_e('Please review and sign the agreement below to access the dealer portal.', 'jblund-dealers'); ?></p>
        <?php endif; ?>
    </div>

    <div class="nda-content">
        <div class="nda-agreement-text">
            <?php if ($use_pdf && !empty($pdf_url)): ?>
                <!-- PDF Version -->
                <div class="nda-pdf-container">
                    <h2><?php \esc_html_e('Non-Disclosure Agreement', 'jblund-dealers'); ?></h2>
                    <p class="pdf-notice"><?php \esc_html_e('Please review the PDF document below. You can download it for your records.', 'jblund-dealers'); ?></p>
                    <p class="pdf-actions">
                        <a href="<?php echo \esc_url($pdf_url); ?>" target="_blank" class="button-secondary">
                            <?php \esc_html_e('View PDF in New Tab', 'jblund-dealers'); ?>
                        </a>
                        <a href="<?php echo \esc_url($pdf_url); ?>" download class="button-secondary">
                            <?php \esc_html_e('Download PDF', 'jblund-dealers'); ?>
                        </a>
                    </p>
                    <div class="pdf-embed-wrapper">
                        <iframe
                            src="<?php echo \esc_url($pdf_url); ?>"
                            width="100%"
                            height="600"
                            style="border: 1px solid #ddd; border-radius: 4px;"
                            title="<?php \esc_attr_e('NDA Document', 'jblund-dealers'); ?>"
                        ></iframe>
                    </div>
                </div>
            <?php else: ?>
                <!-- Text/HTML Version -->
                <div class="nda-text-version">
                    <?php echo \wp_kses($nda_content, $allowed_html); ?>

                    <?php if (!empty($dealer_name)): ?>
                    <div class="nda-parties-section">
                        <h3><?php \esc_html_e('Parties to this Agreement:', 'jblund-dealers'); ?></h3>
                        <ul class="nda-parties">
                            <li><strong><?php \esc_html_e('JB Lund Company', 'jblund-dealers'); ?></strong> <?php \esc_html_e('("Disclosing Party")', 'jblund-dealers'); ?></li>
                            <li><strong><?php echo \esc_html($dealer_name); ?></strong> <?php \esc_html_e('("Receiving Party")', 'jblund-dealers'); ?></li>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!$nda_accepted): ?>
        <form method="post" action="" class="nda-acceptance-form" id="nda-acceptance-form">
            <?php \wp_nonce_field('accept_nda_action', 'accept_nda_nonce'); ?>
            <input type="hidden" name="jblund_nda_action" value="submit" />

            <div class="nda-form-section">
                <h3><?php \esc_html_e('Representative Information', 'jblund-dealers'); ?></h3>

                <div class="form-row">
                    <label for="representative_name">
                        <?php \esc_html_e('Your Full Name:', 'jblund-dealers'); ?>
                        <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="representative_name"
                        name="representative_name"
                        value="<?php echo \esc_attr($current_user->display_name); ?>"
                        required
                        readonly
                    />
                </div>

                <div class="form-row">
                    <label for="dealer_company">
                        <?php \esc_html_e('Dealer Company:', 'jblund-dealers'); ?>
                        <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="dealer_company"
                        name="dealer_company"
                        value="<?php echo \esc_attr($dealer_name); ?>"
                        required
                    />
                </div>
            </div>

            <div class="nda-form-section signature-section">
                <h3><?php \esc_html_e('Digital Signature', 'jblund-dealers'); ?></h3>
                <p class="signature-instructions">
                    <?php \esc_html_e('Please sign below using your mouse or touchscreen. Your signature confirms that you have read, understood, and agree to the terms of this Non-Disclosure Agreement.', 'jblund-dealers'); ?>
                </p>

                <div class="signature-pad-wrapper">
                    <canvas id="signature-canvas" width="600" height="200"></canvas>
                </div>

                <div class="signature-actions">
                    <button type="button" id="clear-signature" class="button-secondary">
                        <?php \esc_html_e('Clear Signature', 'jblund-dealers'); ?>
                    </button>
                </div>

                <input type="hidden" id="signature_data" name="signature_data" value="" />
            </div>

            <div class="nda-form-section acceptance-section">
                <label class="acceptance-checkbox">
                    <input type="checkbox" id="jblund_accept_nda" name="jblund_accept_nda" value="1" required />
                    <span>
                        <?php \esc_html_e('I have read and agree to the terms of this Non-Disclosure Agreement', 'jblund-dealers'); ?>
                        <span class="required">*</span>
                    </span>
                </label>
            </div>

            <div class="nda-form-actions">
                <button type="submit" class="button-primary" id="submit-nda">
                    <?php \esc_html_e('Accept Agreement & Access Portal', 'jblund-dealers'); ?>
                </button>
            </div>
        </form>
        <?php else: ?>
        <div class="nda-already-accepted">
            <div class="signed-nda-display">
                <h3><?php \esc_html_e('Your Signed Agreement', 'jblund-dealers'); ?></h3>
                
                <?php if ($acceptance_data): ?>
                <div class="signed-details">
                    <div class="detail-row">
                        <strong><?php \esc_html_e('Signed By:', 'jblund-dealers'); ?></strong>
                        <span><?php echo \esc_html($acceptance_data['representative_name']); ?></span>
                    </div>
                    <div class="detail-row">
                        <strong><?php \esc_html_e('Company:', 'jblund-dealers'); ?></strong>
                        <span><?php echo \esc_html($acceptance_data['dealer_company']); ?></span>
                    </div>
                    <div class="detail-row">
                        <strong><?php \esc_html_e('Date Accepted:', 'jblund-dealers'); ?></strong>
                        <span><?php echo \esc_html(\date_i18n(\get_option('date_format') . ' ' . \get_option('time_format'), \strtotime($acceptance_data['acceptance_date']))); ?></span>
                    </div>
                    <div class="detail-row">
                        <strong><?php \esc_html_e('IP Address:', 'jblund-dealers'); ?></strong>
                        <span><?php echo \esc_html($acceptance_data['ip_address']); ?></span>
                    </div>
                </div>

                <?php if (!empty($acceptance_data['signature_data'])): ?>
                <div class="signed-signature">
                    <strong><?php \esc_html_e('Digital Signature:', 'jblund-dealers'); ?></strong>
                    <div class="signature-display">
                        <img src="<?php echo \esc_attr($acceptance_data['signature_data']); ?>" alt="<?php \esc_attr_e('Digital Signature', 'jblund-dealers'); ?>" />
                    </div>
                </div>
                <?php endif; ?>
                <?php endif; ?>

                <div class="acceptance-actions">
                    <a href="<?php echo \esc_url(\jblund_get_portal_page_url('dashboard') ?: \home_url('/dealer-dashboard/')); ?>" class="button-primary">
                        <?php \esc_html_e('Return to Dashboard', 'jblund-dealers'); ?>
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

