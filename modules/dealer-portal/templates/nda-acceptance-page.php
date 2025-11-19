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
    <div class="nda-header">
        <h1><?php \esc_html_e('Non-Disclosure Agreement', 'jblund-dealers'); ?></h1>
        <p class="nda-subtitle"><?php \esc_html_e('Please review and sign the agreement below to access the dealer portal.', 'jblund-dealers'); ?></p>
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
                    <div class="signature-pad-notice">
                        <p><strong><?php \esc_html_e('Note:', 'jblund-dealers'); ?></strong> <?php \esc_html_e('Signature functionality requires JavaScript integration (Signature Pad). This will be implemented in Phase 2.', 'jblund-dealers'); ?></p>
                        <p><?php \esc_html_e('For now, you can test the form submission without a signature.', 'jblund-dealers'); ?></p>
                    </div>

                    <!-- Signature canvas will be added here in Phase 2 -->
                    <div class="signature-placeholder">
                        <canvas id="signature-canvas" width="600" height="200" style="border: 2px dashed #ccc; background: #f9f9f9;"></canvas>
                    </div>

                    <div class="signature-actions">
                        <button type="button" id="clear-signature" class="button-secondary" disabled>
                            <?php \esc_html_e('Clear Signature', 'jblund-dealers'); ?>
                        </button>
                    </div>

                    <input type="hidden" id="signature_data" name="signature_data" value="" />
                </div>
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
    </div>
</div>

<style>
/* Inline styles for NDA acceptance page */
.dealer-nda-acceptance {
    max-width: 900px;
    margin: 40px auto;
    padding: 40px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.nda-header {
    text-align: center;
    margin-bottom: 40px;
    padding-bottom: 20px;
    border-bottom: 2px solid #003366;
}

.nda-header h1 {
    color: #003366;
    font-size: 32px;
    margin: 0 0 10px 0;
}

.nda-subtitle {
    color: #666;
    font-size: 16px;
    margin: 0;
}

.nda-agreement-text {
    background: #f9f9f9;
    padding: 30px;
    border-radius: 5px;
    margin-bottom: 30px;
    max-height: 500px;
    overflow-y: auto;
    border: 1px solid #ddd;
}

.nda-agreement-text h2 {
    color: #003366;
    font-size: 24px;
    margin-top: 0;
}

.nda-agreement-text h3 {
    color: #333;
    font-size: 18px;
    margin-top: 20px;
}

/* PDF Display Styles */
.nda-pdf-container {
    padding: 20px 0;
}

.nda-pdf-container h2 {
    margin-bottom: 15px;
}

.pdf-notice {
    color: #666;
    font-style: italic;
    margin-bottom: 15px;
}

.pdf-actions {
    margin-bottom: 20px;
}

.pdf-actions a {
    display: inline-block;
    margin-right: 10px;
}

.pdf-embed-wrapper {
    margin-top: 20px;
    background: #fff;
    padding: 10px;
    border-radius: 4px;
}

.pdf-embed-wrapper iframe {
    display: block;
}

/* Text Version Styles */
.nda-text-version {
    line-height: 1.6;
}

.nda-text-version h2,
.nda-text-version h3,
.nda-text-version h4 {
    color: #003366;
    margin-top: 20px;
    margin-bottom: 10px;
}

.nda-text-version ul,
.nda-text-version ol {
    margin: 15px 0;
    padding-left: 25px;
}

.nda-text-version li {
    margin: 8px 0;
}

.nda-parties-section {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #ddd;
}

.nda-text-section p {
    line-height: 1.6;
    margin: 15px 0;
}

.nda-parties,
.nda-text-section ul {
    margin: 15px 0;
    padding-left: 25px;
}

.nda-parties li,
.nda-text-section ul li {
    margin: 8px 0;
    line-height: 1.6;
}

.nda-form-section {
    margin-bottom: 30px;
    padding: 20px;
    background: #f9f9f9;
    border-radius: 5px;
}

.nda-form-section h3 {
    color: #003366;
    font-size: 20px;
    margin-top: 0;
}

.form-row {
    margin-bottom: 20px;
}

.form-row label {
    display: block;
    font-weight: 600;
    margin-bottom: 5px;
    color: #333;
}

.form-row input[type="text"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
}

.form-row input[readonly] {
    background: #e9ecef;
}

.signature-instructions {
    color: #666;
    margin-bottom: 20px;
}

.signature-pad-wrapper {
    margin-top: 20px;
}

.signature-pad-notice {
    background: #fff3cd;
    border: 1px solid #ffc107;
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 15px;
}

.signature-pad-notice p {
    margin: 5px 0;
}

.signature-placeholder {
    margin: 20px 0;
    text-align: center;
}

.signature-actions {
    margin-top: 10px;
}

.acceptance-checkbox {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    cursor: pointer;
}

.acceptance-checkbox input[type="checkbox"] {
    margin-top: 3px;
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.acceptance-checkbox span {
    flex: 1;
    font-size: 16px;
    line-height: 1.5;
}

.required {
    color: #dc3545;
    font-weight: bold;
}

.nda-form-actions {
    text-align: center;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 2px solid #ddd;
}

.button-primary,
.button-secondary {
    padding: 12px 30px;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
    border: none;
    transition: all 0.3s ease;
}

.button-primary {
    background: #003366;
    color: #fff;
}

.button-primary:hover {
    background: #002244;
}

.button-secondary {
    background: #6c757d;
    color: #fff;
    margin-right: 10px;
}

.button-secondary:hover {
    background: #5a6268;
}

.button-secondary:disabled {
    background: #ccc;
    cursor: not-allowed;
}

@media (max-width: 768px) {
    .dealer-nda-acceptance {
        margin: 20px;
        padding: 20px;
    }

    .nda-header h1 {
        font-size: 24px;
    }

    .signature-placeholder canvas {
        max-width: 100%;
        height: auto;
    }
}
</style>
