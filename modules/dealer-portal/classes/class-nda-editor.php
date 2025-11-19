<?php
/**
 * NDA Content Editor
 *
 * Admin interface for customizing NDA agreement text content.
 * Provides TinyMCE editors for each section of the NDA with live preview.
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
 * NDA_Editor Class
 *
 * Manages customization of NDA agreement content through WordPress admin.
 */
class NDA_Editor {

    /**
     * Option name for storing custom NDA content
     */
    const OPTION_NAME = 'jblund_nda_custom_content';

    /**
     * Constructor
     */
    public function __construct() {
        // No longer adding submenu page - NDA Editor is now a tab in main settings
        \add_action('admin_init', [$this, 'register_settings']);
        \add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        \add_action('wp_ajax_preview_nda_content', [$this, 'ajax_preview_nda']);
    }

    /**
     * Get default NDA content
     *
     * @return string Default full NDA content
     */
    public static function get_default_content() {
        return __(
            '<h2>Non-Disclosure Agreement</h2>

<p>This Non-Disclosure Agreement ("Agreement") is entered into by and between JB Lund Company ("Disclosing Party") and the dealer representative ("Receiving Party").</p>

<h3>1. Purpose</h3>
<p>The parties wish to explore a business opportunity of mutual interest and in connection with this opportunity, the Disclosing Party may disclose certain confidential technical and business information that the Receiving Party agrees to treat as confidential.</p>

<h3>2. Confidential Information</h3>
<p>For purposes of this Agreement, "Confidential Information" means all information disclosed by the Disclosing Party to the Receiving Party, including but not limited to:</p>
<ul>
    <li>Pricing information and dealer cost structures</li>
    <li>Product specifications and technical documentation</li>
    <li>Territory assignments and dealer network information</li>
    <li>Business strategies and marketing plans</li>
    <li>Customer lists and contact information</li>
</ul>

<h3>3. Obligations</h3>
<p>The Receiving Party agrees to:</p>
<ul>
    <li>Hold and maintain the Confidential Information in strict confidence</li>
    <li>Not disclose the Confidential Information to any third parties without prior written consent</li>
    <li>Use the Confidential Information solely for the purpose of the business relationship</li>
    <li>Protect the Confidential Information using the same degree of care used to protect their own confidential information</li>
</ul>

<h3>4. Term</h3>
<p>This Agreement shall remain in effect for the duration of the dealer relationship and for two (2) years following termination of the relationship.</p>

<h3>5. Return of Materials</h3>
<p>Upon termination of the business relationship, the Receiving Party shall return or destroy all Confidential Information and provide written certification of such destruction.</p>',
            'jblund-dealers'
        );
    }

    /**
     * Get current NDA content (custom or default)
     *
     * @return string Current NDA content
     */
    public static function get_content() {
        $options = \get_option(self::OPTION_NAME, []);

        // Return custom content if exists, otherwise return default
        if (!empty($options['content'])) {
            return $options['content'];
        }

        return self::get_default_content();
    }

    /**
     * Get NDA PDF URL if uploaded
     *
     * @return string|false PDF URL or false
     */
    public static function get_pdf_url() {
        $options = \get_option(self::OPTION_NAME, []);
        return !empty($options['pdf_url']) ? $options['pdf_url'] : false;
    }

    /**
     * Check if using PDF version
     *
     * @return bool
     */
    public static function is_using_pdf() {
        $options = \get_option(self::OPTION_NAME, []);
        return !empty($options['use_pdf']) && !empty($options['pdf_url']);
    }

    /**
     * Register settings (called via admin_init)
     */
    public function register_settings() {
        \register_setting('jblund_nda_editor', self::OPTION_NAME, [
            'type' => 'array',
            'sanitize_callback' => [$this, 'sanitize_nda_content'],
        ]);
    }

    /**
     * Sanitize NDA content
     *
     * @param array $input Raw input from form
     * @return array Sanitized content
     */
    public function sanitize_nda_content($input) {
        if (!\is_array($input)) {
            return [];
        }

        $sanitized = [];

        // Sanitize the main NDA content (allow more HTML for formatting)
        $allowed_html = [
            'p' => ['class' => [], 'style' => []],
            'strong' => [],
            'em' => [],
            'u' => [],
            'ul' => ['class' => []],
            'ol' => ['class' => []],
            'li' => [],
            'br' => [],
            'h1' => ['class' => []],
            'h2' => ['class' => []],
            'h3' => ['class' => []],
            'h4' => ['class' => []],
            'div' => ['class' => []],
            'span' => ['class' => []],
            'a' => ['href' => [], 'target' => [], 'rel' => []],
        ];

        if (isset($input['content'])) {
            $sanitized['content'] = \wp_kses($input['content'], $allowed_html);
        }

        // Handle PDF upload
        if (isset($input['pdf_url'])) {
            $sanitized['pdf_url'] = \esc_url_raw($input['pdf_url']);
        }

        // Handle use_pdf checkbox
        $sanitized['use_pdf'] = isset($input['use_pdf']) ? '1' : '0';

        return $sanitized;
    }

    /**
     * Enqueue admin assets
     *
     * @param string $hook Current admin page hook
     */
    public function enqueue_admin_assets($hook) {
        // This method is no longer needed - assets are enqueued directly in render_settings_page()
        // Keeping it empty for backwards compatibility
    }

    /**
     * AJAX handler for NDA preview
     * Note: This method is no longer used - preview is now handled via JavaScript
     * Keeping it for backwards compatibility
     */
    public function ajax_preview_nda() {
        \check_ajax_referer('preview_nda', 'nonce');

        if (!\current_user_can('manage_options')) {
            \wp_send_json_error(__('Insufficient permissions', 'jblund-dealers'));
        }

        // This is now handled client-side
        \wp_send_json_success('');
    }

    /**
     * Render settings page
     */
    public function render_settings_page() {
        if (!\current_user_can('manage_options')) {
            return;
        }

        // Handle revert to defaults
        if (isset($_POST['revert_to_defaults']) && \check_admin_referer('revert_nda_defaults')) {
            \delete_option(self::OPTION_NAME);
            echo '<div class="notice notice-success"><p>' . \esc_html__('NDA content reverted to defaults.', 'jblund-dealers') . '</p></div>';
        }

        // Enqueue media uploader
        \wp_enqueue_media();

        $options = \get_option(self::OPTION_NAME, []);
        $content = $options['content'] ?? self::get_default_content();
        $pdf_url = $options['pdf_url'] ?? '';
        $use_pdf = ($options['use_pdf'] ?? '0') === '1';
        ?>
        <div class="wrap">
            <h1><?php echo \esc_html__('NDA Content Editor', 'jblund-dealers'); ?></h1>
            <p><?php \esc_html_e('Customize the Non-Disclosure Agreement that dealers must accept. You can either upload a PDF document or use the text editor below.', 'jblund-dealers'); ?></p>

            <form method="post" action="options.php" class="nda-editor-form">
                <?php \settings_fields('jblund_nda_editor'); ?>

                <div class="nda-editor-container">

                    <!-- PDF Upload Section -->
                    <div class="nda-section">
                        <h3><?php \_e('PDF Document (Optional)', 'jblund-dealers'); ?></h3>
                        <p class="description"><?php \_e('If you have a legal PDF document, upload it here. When enabled, dealers will view and sign this PDF instead of the text version below.', 'jblund-dealers'); ?></p>

                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="use_pdf"><?php \_e('Use PDF Version', 'jblund-dealers'); ?></label>
                                </th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="<?php echo \esc_attr(self::OPTION_NAME); ?>[use_pdf]" id="use_pdf" value="1" <?php \checked($use_pdf, true); ?> />
                                        <?php \_e('Enable PDF version (requires PDF upload)', 'jblund-dealers'); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="pdf_url"><?php \_e('PDF File', 'jblund-dealers'); ?></label>
                                </th>
                                <td>
                                    <input type="text" name="<?php echo \esc_attr(self::OPTION_NAME); ?>[pdf_url]" id="pdf_url" value="<?php echo \esc_attr($pdf_url); ?>" class="regular-text" readonly />
                                    <button type="button" class="button button-secondary" id="upload_pdf_button"><?php \_e('Upload PDF', 'jblund-dealers'); ?></button>
                                    <?php if ($pdf_url): ?>
                                        <button type="button" class="button button-secondary" id="remove_pdf_button"><?php \_e('Remove PDF', 'jblund-dealers'); ?></button>
                                        <br>
                                        <a href="<?php echo \esc_url($pdf_url); ?>" target="_blank" class="description"><?php \_e('View current PDF', 'jblund-dealers'); ?></a>
                                    <?php endif; ?>
                                    <p class="description"><?php \_e('Upload a PDF file containing your legal NDA document.', 'jblund-dealers'); ?></p>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- Text Editor Section -->
                    <div class="nda-section">
                        <h3><?php \_e('Text/HTML Version (Fallback)', 'jblund-dealers'); ?></h3>
                        <p class="description"><?php \_e('This content will be displayed when PDF version is not enabled. You can format the complete NDA document here.', 'jblund-dealers'); ?></p>

                        <label for="nda_content"><?php \_e('NDA Content:', 'jblund-dealers'); ?></label>
                        <?php
                        \wp_editor($content, 'nda_content', [
                            'textarea_name' => self::OPTION_NAME . '[content]',
                            'textarea_rows' => 20,
                            'media_buttons' => false,
                            'teeny' => false,
                        ]);
                        ?>
                    </div>

                </div>

                <div class="nda-actions">
                    <?php \submit_button(__('Save Changes', 'jblund-dealers'), 'primary', 'submit', false); ?>
                    <button type="button" id="preview-nda-btn" class="button button-secondary"><?php \_e('Preview', 'jblund-dealers'); ?></button>
                </div>
            </form>

            <form method="post" action="" style="margin-top: 20px;">
                <?php \wp_nonce_field('revert_nda_defaults'); ?>
                <button type="submit" name="revert_to_defaults" class="button button-secondary" id="revert-defaults-btn">
                    <?php \_e('Revert to Defaults', 'jblund-dealers'); ?>
                </button>
                <p class="description"><?php \_e('This will delete all custom content and restore the original NDA text.', 'jblund-dealers'); ?></p>
            </form>
        </div>

        <!-- Preview Modal -->
        <div id="nda-preview-modal" class="preview-modal">
            <div class="preview-modal-content">
                <span class="preview-close">&times;</span>
                <div id="nda-preview-body"></div>
            </div>
        </div>

        <script type="text/javascript">
        jQuery(document).ready(function($) {
            // PDF Upload
            var mediaUploader;
            $('#upload_pdf_button').on('click', function(e) {
                e.preventDefault();
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }
                mediaUploader = wp.media({
                    title: '<?php \_e('Select PDF File', 'jblund-dealers'); ?>',
                    button: {
                        text: '<?php \_e('Use this PDF', 'jblund-dealers'); ?>'
                    },
                    library: {
                        type: 'application/pdf'
                    },
                    multiple: false
                });
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#pdf_url').val(attachment.url);
                });
                mediaUploader.open();
            });

            // Remove PDF
            $('#remove_pdf_button').on('click', function(e) {
                e.preventDefault();
                if (confirm('<?php \_e('Are you sure you want to remove the PDF?', 'jblund-dealers'); ?>')) {
                    $('#pdf_url').val('');
                    $(this).hide();
                    $(this).prev('br').hide();
                    $(this).prev('a').hide();
                }
            });

            // Preview button
            $('#preview-nda-btn').on('click', function(e) {
                e.preventDefault();
                var content = tinyMCE.get('nda_content') ? tinyMCE.get('nda_content').getContent() : $('#nda_content').val();
                var usePdf = $('#use_pdf').is(':checked');
                var pdfUrl = $('#pdf_url').val();

                var previewHtml = '';
                if (usePdf && pdfUrl) {
                    previewHtml = '<h2><?php \_e('NDA Preview (PDF Version)', 'jblund-dealers'); ?></h2>';
                    previewHtml += '<p><strong><?php \_e('PDF File:', 'jblund-dealers'); ?></strong> <a href="' + pdfUrl + '" target="_blank"><?php \_e('View PDF', 'jblund-dealers'); ?></a></p>';
                    previewHtml += '<iframe src="' + pdfUrl + '" style="width:100%;height:600px;border:1px solid #ccc;"></iframe>';
                } else {
                    previewHtml = '<h2><?php \_e('NDA Preview (Text Version)', 'jblund-dealers'); ?></h2>';
                    previewHtml += '<div class="nda-content">' + content + '</div>';
                }

                $('#nda-preview-body').html(previewHtml);
                $('#nda-preview-modal').fadeIn();
            });

            // Close modal
            $('.preview-close, #nda-preview-modal').on('click', function(e) {
                if (e.target === this) {
                    $('#nda-preview-modal').fadeOut();
                }
            });

            // Revert to defaults
            $('#revert-defaults-btn').on('click', function(e) {
                if (!confirm('<?php echo \esc_js(__('Are you sure you want to revert all content to defaults? This cannot be undone.', 'jblund-dealers')); ?>')) {
                    e.preventDefault();
                    return false;
                }
            });
        });
        </script>

        <style>
            .nda-editor-container { max-width: 1200px; margin: 20px 0; }
            .nda-section { background: #fff; padding: 20px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 4px; }
            .nda-section h3 { margin-top: 0; color: #003366; }
            .nda-section label { display: block; font-weight: 600; margin-bottom: 10px; }
            .nda-actions { background: #f8f9fa; padding: 15px 20px; border-radius: 4px; margin-top: 20px; }
            .nda-actions .button { margin-right: 10px; }
            .preview-modal { display: none; position: fixed; z-index: 100000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); }
            .preview-modal-content { background: #fff; margin: 5% auto; padding: 30px; width: 80%; max-width: 900px; max-height: 80vh; overflow-y: auto; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.3); }
            .preview-close { float: right; font-size: 28px; font-weight: bold; cursor: pointer; color: #aaa; }
            .preview-close:hover { color: #000; }
        </style>
        <?php
    }
}
