<?php
/**
 * Portal Repeater Fields Manager
 *
 * Handles complex repeater fields for portal updates and required documents
 *
 * @package JBLund_Dealers
 * @subpackage Admin
 */

namespace JBLund\Admin;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Portal_Fields {

    /**
     * Singleton instance
     */
    private static $instance = null;

    /**
     * Get singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor (private for singleton)
     */
    private function __construct() {
        // This class is instantiated on-demand
    }

    /**
     * Portal updates section callback
     */
    public function portal_updates_section() {
        echo '<h3>' . __('Portal Updates', 'jblund-dealers') . '</h3>';
        echo '<p>' . __('Manage updates that appear on the dealer dashboard "Recent Updates" card. Add announcements, news, or important information for dealers.', 'jblund-dealers') . '</p>';
    }

    /**
     * Portal updates field callback
     */
    public function portal_updates_field() {
        $options = get_option('jblund_dealers_settings');
        $updates = isset($options['portal_updates']) ? $options['portal_updates'] : array();

        // Ensure updates is an array
        if (!is_array($updates)) {
            $updates = array();
        }
        ?>
        <div id="portal-updates-manager">
            <div id="updates-list" style="margin-bottom: 20px;">
                <?php if (!empty($updates)) : ?>
                    <?php foreach ($updates as $index => $update) : ?>
                        <?php $this->render_update_row($index, $update); ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <button type="button" class="button button-secondary" id="add-update">
                <span class="dashicons dashicons-plus-alt" style="margin-top: 3px;"></span>
                <?php _e('Add Update', 'jblund-dealers'); ?>
            </button>

            <p class="description" style="margin-top: 15px;">
                <?php _e('Updates appear on the dealer dashboard in reverse chronological order. They can be scheduled with start and end dates.', 'jblund-dealers'); ?>
            </p>
        </div>

        <script type="text/javascript">
        jQuery(document).ready(function($) {
            var updateIndex = <?php echo count($updates); ?>;

            // Add new update
            $('#add-update').on('click', function() {
                var template = `
                    <div class="update-row" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; background: #f9f9f9; position: relative;">
                        <button type="button" class="button button-link-delete remove-update" style="position: absolute; top: 10px; right: 10px; color: #dc3232;">
                            <span class="dashicons dashicons-trash"></span> <?php _e('Remove', 'jblund-dealers'); ?>
                        </button>

                        <div style="margin-bottom: 10px;">
                            <label style="display: block; margin-bottom: 5px;">
                                <strong><?php _e('Update Title:', 'jblund-dealers'); ?></strong>
                            </label>
                            <input type="text"
                                   name="jblund_dealers_settings[portal_updates][${updateIndex}][title]"
                                   class="large-text"
                                   placeholder="<?php _e('e.g., New Product Launch, Price Update, etc.', 'jblund-dealers'); ?>" />
                        </div>

                        <div style="margin-bottom: 10px;">
                            <label style="display: block; margin-bottom: 5px;">
                                <strong><?php _e('Message:', 'jblund-dealers'); ?></strong>
                            </label>
                            <textarea name="jblund_dealers_settings[portal_updates][${updateIndex}][message]"
                                      rows="3"
                                      class="large-text"
                                      placeholder="<?php _e('Enter your update message here...', 'jblund-dealers'); ?>"></textarea>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div>
                                <label style="display: block; margin-bottom: 5px;">
                                    <strong><?php _e('Start Date (Optional):', 'jblund-dealers'); ?></strong>
                                </label>
                                <input type="date"
                                       name="jblund_dealers_settings[portal_updates][${updateIndex}][start_date]"
                                       class="regular-text" />
                                <p class="description"><?php _e('Update will appear starting from this date', 'jblund-dealers'); ?></p>
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px;">
                                    <strong><?php _e('End Date (Optional):', 'jblund-dealers'); ?></strong>
                                </label>
                                <input type="date"
                                       name="jblund_dealers_settings[portal_updates][${updateIndex}][end_date]"
                                       class="regular-text" />
                                <p class="description"><?php _e('Update will be hidden after this date', 'jblund-dealers'); ?></p>
                            </div>
                        </div>
                    </div>
                `;

                $('#updates-list').append(template);
                updateIndex++;
            });

            // Remove update
            $(document).on('click', '.remove-update', function() {
                if (confirm('<?php _e('Are you sure you want to remove this update?', 'jblund-dealers'); ?>')) {
                    $(this).closest('.update-row').remove();
                }
            });
        });
        </script>

        <style>
        .update-row .dashicons {
            margin-top: 3px;
        }
        .update-row label strong {
            color: #23282d;
        }
        </style>
        <?php
    }

    /**
     * Render a single update row
     */
    private function render_update_row($index, $update) {
        $title = isset($update['title']) ? $update['title'] : '';
        $message = isset($update['message']) ? $update['message'] : '';
        $start_date = isset($update['start_date']) ? $update['start_date'] : '';
        $end_date = isset($update['end_date']) ? $update['end_date'] : '';
        ?>
        <div class="update-row" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; background: #f9f9f9; position: relative;">
            <button type="button" class="button button-link-delete remove-update" style="position: absolute; top: 10px; right: 10px; color: #dc3232;">
                <span class="dashicons dashicons-trash"></span> <?php _e('Remove', 'jblund-dealers'); ?>
            </button>

            <div style="margin-bottom: 10px;">
                <label style="display: block; margin-bottom: 5px;">
                    <strong><?php _e('Update Title:', 'jblund-dealers'); ?></strong>
                </label>
                <input type="text"
                       name="jblund_dealers_settings[portal_updates][<?php echo $index; ?>][title]"
                       value="<?php echo esc_attr($title); ?>"
                       class="large-text"
                       placeholder="<?php _e('e.g., New Product Launch, Price Update, etc.', 'jblund-dealers'); ?>" />
            </div>

            <div style="margin-bottom: 10px;">
                <label style="display: block; margin-bottom: 5px;">
                    <strong><?php _e('Message:', 'jblund-dealers'); ?></strong>
                </label>
                <textarea name="jblund_dealers_settings[portal_updates][<?php echo $index; ?>][message]"
                          rows="3"
                          class="large-text"
                          placeholder="<?php _e('Enter your update message here...', 'jblund-dealers'); ?>"><?php echo esc_textarea($message); ?></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 5px;">
                        <strong><?php _e('Start Date (Optional):', 'jblund-dealers'); ?></strong>
                    </label>
                    <input type="date"
                           name="jblund_dealers_settings[portal_updates][<?php echo $index; ?>][start_date]"
                           value="<?php echo esc_attr($start_date); ?>"
                           class="regular-text" />
                    <p class="description"><?php _e('Update will appear starting from this date', 'jblund-dealers'); ?></p>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px;">
                        <strong><?php _e('End Date (Optional):', 'jblund-dealers'); ?></strong>
                    </label>
                    <input type="date"
                           name="jblund_dealers_settings[portal_updates][<?php echo $index; ?>][end_date]"
                           value="<?php echo esc_attr($end_date); ?>"
                           class="regular-text" />
                    <p class="description"><?php _e('Update will be hidden after this date', 'jblund-dealers'); ?></p>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Dealer Representative section callback
     */
    public function representative_section() {
        echo '<h3>' . __('Dealer Representative', 'jblund-dealers') . '</h3>';
        echo '<p>' . __('Configure the dealer representative contact information shown on the dealer dashboard.', 'jblund-dealers') . '</p>';
    }

    /**
     * Required documents section callback
     */
    public function required_documents_section() {
        echo '<h3>' . __('Required Documents', 'jblund-dealers') . '</h3>';
        echo '<p>' . __('Manage documents that dealers need to complete. These appear in the "Documents to Complete" section on the dealer dashboard.', 'jblund-dealers') . '</p>';
    }

    /**
     * Required documents field callback
     */
    public function required_documents_field() {
        $options = get_option('jblund_dealers_settings');
        $documents = isset($options['required_documents']) ? $options['required_documents'] : array();

        // Ensure documents is an array
        if (!is_array($documents)) {
            $documents = array();
        }
        ?>
        <div id="required-documents-manager">
            <div id="documents-list" style="margin-bottom: 20px;">
                <?php if (!empty($documents)) : ?>
                    <?php foreach ($documents as $index => $document) : ?>
                        <?php $this->render_document_row($index, $document); ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <button type="button" class="button button-secondary" id="add-document">
                <span class="dashicons dashicons-plus-alt" style="margin-top: 3px;"></span>
                <?php _e('Add Document', 'jblund-dealers'); ?>
            </button>

            <p class="description" style="margin-top: 15px;">
                <?php _e('Add forms, documents, or links that dealers need to complete.', 'jblund-dealers'); ?>
            </p>
        </div>

        <script type="text/javascript">
        jQuery(document).ready(function($) {
            var documentIndex = <?php echo count($documents); ?>;

            // Add new document
            $('#add-document').on('click', function() {
                var template = `
                    <div class="document-row" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; background: #f9f9f9; position: relative;">
                        <button type="button" class="button button-link-delete remove-document" style="position: absolute; top: 10px; right: 10px; color: #dc3232;">
                            <span class="dashicons dashicons-trash"></span> <?php _e('Remove', 'jblund-dealers'); ?>
                        </button>

                        <div style="margin-bottom: 10px;">
                            <label style="display: block; margin-bottom: 5px;">
                                <strong><?php _e('Document Title:', 'jblund-dealers'); ?></strong>
                            </label>
                            <input type="text"
                                   name="jblund_dealers_settings[required_documents][${documentIndex}][title]"
                                   class="large-text"
                                   placeholder="<?php _e('e.g., W-9 Form, Insurance Certificate, etc.', 'jblund-dealers'); ?>" />
                        </div>

                        <div style="margin-bottom: 10px;">
                            <label style="display: block; margin-bottom: 5px;">
                                <strong><?php _e('Description:', 'jblund-dealers'); ?></strong>
                            </label>
                            <textarea name="jblund_dealers_settings[required_documents][${documentIndex}][description]"
                                      rows="2"
                                      class="large-text"
                                      placeholder="<?php _e('Brief description of what this document is for...', 'jblund-dealers'); ?>"></textarea>
                        </div>

                        <div style="margin-bottom: 10px;">
                            <label style="display: block; margin-bottom: 5px;">
                                <strong><?php _e('Document URL:', 'jblund-dealers'); ?></strong>
                            </label>
                            <input type="url"
                                   name="jblund_dealers_settings[required_documents][${documentIndex}][url]"
                                   class="large-text"
                                   placeholder="<?php _e('https://example.com/form', 'jblund-dealers'); ?>" />
                            <p class="description"><?php _e('Link to the form or document page', 'jblund-dealers'); ?></p>
                        </div>

                        <div style="margin-bottom: 10px;">
                            <label>
                                <input type="checkbox"
                                       name="jblund_dealers_settings[required_documents][${documentIndex}][required]"
                                       value="1" />
                                <strong><?php _e('Mark as Required', 'jblund-dealers'); ?></strong>
                            </label>
                            <p class="description"><?php _e('Required documents show a red badge', 'jblund-dealers'); ?></p>
                        </div>
                    </div>
                `;

                $('#documents-list').append(template);
                documentIndex++;
            });

            // Remove document
            $(document).on('click', '.remove-document', function() {
                if (confirm('<?php _e('Are you sure you want to remove this document?', 'jblund-dealers'); ?>')) {
                    $(this).closest('.document-row').remove();
                }
            });
        });
        </script>

        <style>
        .document-row .dashicons {
            margin-top: 3px;
        }
        .document-row label strong {
            color: #23282d;
        }
        </style>
        <?php
    }

    /**
     * Render a single document row
     */
    private function render_document_row($index, $document) {
        $title = isset($document['title']) ? $document['title'] : '';
        $description = isset($document['description']) ? $document['description'] : '';
        $url = isset($document['url']) ? $document['url'] : '';
        $required = isset($document['required']) && $document['required'] === '1';
        ?>
        <div class="document-row" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; background: #f9f9f9; position: relative;">
            <button type="button" class="button button-link-delete remove-document" style="position: absolute; top: 10px; right: 10px; color: #dc3232;">
                <span class="dashicons dashicons-trash"></span> <?php _e('Remove', 'jblund-dealers'); ?>
            </button>

            <div style="margin-bottom: 10px;">
                <label style="display: block; margin-bottom: 5px;">
                    <strong><?php _e('Document Title:', 'jblund-dealers'); ?></strong>
                </label>
                <input type="text"
                       name="jblund_dealers_settings[required_documents][<?php echo $index; ?>][title]"
                       value="<?php echo esc_attr($title); ?>"
                       class="large-text"
                       placeholder="<?php _e('e.g., W-9 Form, Insurance Certificate, etc.', 'jblund-dealers'); ?>" />
            </div>

            <div style="margin-bottom: 10px;">
                <label style="display: block; margin-bottom: 5px;">
                    <strong><?php _e('Description:', 'jblund-dealers'); ?></strong>
                </label>
                <textarea name="jblund_dealers_settings[required_documents][<?php echo $index; ?>][description]"
                          rows="2"
                          class="large-text"
                          placeholder="<?php _e('Brief description of what this document is for...', 'jblund-dealers'); ?>"><?php echo esc_textarea($description); ?></textarea>
            </div>

            <div style="margin-bottom: 10px;">
                <label style="display: block; margin-bottom: 5px;">
                    <strong><?php _e('Document URL:', 'jblund-dealers'); ?></strong>
                </label>
                <input type="url"
                       name="jblund_dealers_settings[required_documents][<?php echo $index; ?>][url]"
                       value="<?php echo esc_attr($url); ?>"
                       class="large-text"
                       placeholder="<?php _e('https://example.com/form', 'jblund-dealers'); ?>" />
                <p class="description"><?php _e('Link to the form or document page', 'jblund-dealers'); ?></p>
            </div>

            <div style="margin-bottom: 10px;">
                <label>
                    <input type="checkbox"
                           name="jblund_dealers_settings[required_documents][<?php echo $index; ?>][required]"
                           value="1"
                           <?php checked($required, true); ?> />
                    <strong><?php _e('Mark as Required', 'jblund-dealers'); ?></strong>
                </label>
                <p class="description"><?php _e('Required documents show a red badge', 'jblund-dealers'); ?></p>
            </div>
        </div>
        <?php
    }
}
