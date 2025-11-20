<?php
/**
 * Message Scheduler Class
 *
 * Manages scheduled success messages for dealer registration form.
 * Allows saving multiple message templates and scheduling them for specific time periods.
 *
 * @package JBLund_Dealers
 * @since 1.4.0
 */

namespace JBLund\Admin;

class Message_Scheduler {

    /**
     * Option name for saved messages
     */
    const MESSAGES_OPTION = 'jblund_dealers_registration_messages';

    /**
     * Option name for active schedule
     */
    const SCHEDULE_OPTION = 'jblund_dealers_registration_schedule';

    /**
     * Initialize the message scheduler
     */
    public function __construct() {
        add_action('admin_init', array($this, 'handle_message_actions'));
        add_action('wp_ajax_jblund_save_message', array($this, 'ajax_save_message'));
        add_action('wp_ajax_jblund_delete_message', array($this, 'ajax_delete_message'));
        add_action('wp_ajax_jblund_set_active_message', array($this, 'ajax_set_active_message'));
        add_action('wp_ajax_jblund_schedule_message', array($this, 'ajax_schedule_message'));
    }

    /**
     * Get all saved messages
     *
     * @return array Array of saved messages
     */
    public function get_messages() {
        $messages = get_option(self::MESSAGES_OPTION, array());

        // Ensure we always have a default message
        if (empty($messages)) {
            $messages = array(
                'default' => array(
                    'id' => 'default',
                    'name' => __('Default Message', 'jblund-dealers'),
                    'title' => __('Application Submitted Successfully!', 'jblund-dealers'),
                    'message' => __('Thank you for your interest in becoming a JBLund dealer. Your application has been received and is currently under review. One of our account representatives will contact you shortly to discuss your application and next steps.', 'jblund-dealers'),
                    'note' => __('Please allow 2-3 business days for us to review your application.', 'jblund-dealers'),
                    'created' => current_time('timestamp'),
                    'is_default' => true,
                )
            );
            update_option(self::MESSAGES_OPTION, $messages);
        }

        return $messages;
    }

    /**
     * Get the currently active message based on schedule
     *
     * @return array The active message array
     */
    public function get_active_message() {
        $schedule = get_option(self::SCHEDULE_OPTION, array());
        $current_time = current_time('timestamp');

        // Check if there's an active scheduled message
        if (!empty($schedule)) {
            foreach ($schedule as $schedule_item) {
                $start_time = strtotime($schedule_item['start_date'] . ' ' . $schedule_item['start_time']);
                $end_time = strtotime($schedule_item['end_date'] . ' ' . $schedule_item['end_time']);

                if ($current_time >= $start_time && $current_time <= $end_time) {
                    $messages = $this->get_messages();
                    if (isset($messages[$schedule_item['message_id']])) {
                        return $messages[$schedule_item['message_id']];
                    }
                }
            }
        }

        // Fall back to default active message or default message
        $messages = $this->get_messages();
        $active_id = get_option('jblund_dealers_active_message', 'default');

        return isset($messages[$active_id]) ? $messages[$active_id] : reset($messages);
    }

    /**
     * Save a new message
     *
     * @param string $name Message name/label
     * @param string $title Success page title
     * @param string $message Success message body
     * @param string $note Timeline note
     * @param string $id Optional ID for updating existing message
     * @return string Message ID
     */
    public function save_message($name, $title, $message, $note, $id = null) {
        $messages = $this->get_messages();

        if (empty($id)) {
            $id = 'msg_' . uniqid();
        }

        $messages[$id] = array(
            'id' => $id,
            'name' => sanitize_text_field($name),
            'title' => sanitize_text_field($title),
            'message' => wp_kses_post($message),
            'note' => sanitize_text_field($note),
            'created' => current_time('timestamp'),
            'modified' => current_time('timestamp'),
            'is_default' => false,
        );

        update_option(self::MESSAGES_OPTION, $messages);

        return $id;
    }

    /**
     * Delete a message
     *
     * @param string $id Message ID
     * @return bool Success
     */
    public function delete_message($id) {
        $messages = $this->get_messages();

        // Can't delete the default message
        if ($id === 'default' || !isset($messages[$id])) {
            return false;
        }

        // Remove any schedules using this message
        $schedule = get_option(self::SCHEDULE_OPTION, array());
        $schedule = array_filter($schedule, function($item) use ($id) {
            return $item['message_id'] !== $id;
        });
        update_option(self::SCHEDULE_OPTION, array_values($schedule));

        // If this was the active message, revert to default
        $active_id = get_option('jblund_dealers_active_message', 'default');
        if ($active_id === $id) {
            update_option('jblund_dealers_active_message', 'default');
        }

        unset($messages[$id]);
        update_option(self::MESSAGES_OPTION, $messages);

        return true;
    }

    /**
     * Set the active message (non-scheduled)
     *
     * @param string $id Message ID
     * @return bool Success
     */
    public function set_active_message($id) {
        $messages = $this->get_messages();

        if (!isset($messages[$id])) {
            return false;
        }

        update_option('jblund_dealers_active_message', $id);
        return true;
    }

    /**
     * Schedule a message for a specific time period
     *
     * @param string $message_id Message ID
     * @param string $start_date Start date (Y-m-d)
     * @param string $start_time Start time (H:i)
     * @param string $end_date End date (Y-m-d)
     * @param string $end_time End time (H:i)
     * @return bool Success
     */
    public function schedule_message($message_id, $start_date, $start_time, $end_date, $end_time) {
        $messages = $this->get_messages();

        if (!isset($messages[$message_id])) {
            return false;
        }

        $schedule = get_option(self::SCHEDULE_OPTION, array());

        $schedule[] = array(
            'id' => 'schedule_' . uniqid(),
            'message_id' => $message_id,
            'start_date' => sanitize_text_field($start_date),
            'start_time' => sanitize_text_field($start_time),
            'end_date' => sanitize_text_field($end_date),
            'end_time' => sanitize_text_field($end_time),
            'created' => current_time('timestamp'),
        );

        update_option(self::SCHEDULE_OPTION, $schedule);

        return true;
    }

    /**
     * Get all scheduled messages
     *
     * @return array Array of schedules
     */
    public function get_schedules() {
        return get_option(self::SCHEDULE_OPTION, array());
    }

    /**
     * Delete a schedule
     *
     * @param string $schedule_id Schedule ID
     * @return bool Success
     */
    public function delete_schedule($schedule_id) {
        $schedule = get_option(self::SCHEDULE_OPTION, array());

        $schedule = array_filter($schedule, function($item) use ($schedule_id) {
            return $item['id'] !== $schedule_id;
        });

        update_option(self::SCHEDULE_OPTION, array_values($schedule));

        return true;
    }

    /**
     * Handle non-AJAX message actions
     */
    public function handle_message_actions() {
        // This can handle form-based actions if needed
        // Currently using AJAX for better UX
    }

    /**
     * AJAX handler for saving messages
     */
    public function ajax_save_message() {
        check_ajax_referer('jblund_message_scheduler', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'jblund-dealers')));
        }

        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $title = isset($_POST['title']) ? $_POST['title'] : '';
        $message = isset($_POST['message']) ? $_POST['message'] : '';
        $note = isset($_POST['note']) ? $_POST['note'] : '';
        $id = isset($_POST['id']) ? $_POST['id'] : null;

        if (empty($name) || empty($title) || empty($message)) {
            wp_send_json_error(array('message' => __('All fields are required', 'jblund-dealers')));
        }

        $message_id = $this->save_message($name, $title, $message, $note, $id);

        wp_send_json_success(array(
            'message' => __('Message saved successfully', 'jblund-dealers'),
            'id' => $message_id
        ));
    }

    /**
     * AJAX handler for deleting messages
     */
    public function ajax_delete_message() {
        check_ajax_referer('jblund_message_scheduler', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'jblund-dealers')));
        }

        $id = isset($_POST['id']) ? $_POST['id'] : '';

        if (empty($id)) {
            wp_send_json_error(array('message' => __('Invalid message ID', 'jblund-dealers')));
        }

        if ($this->delete_message($id)) {
            wp_send_json_success(array('message' => __('Message deleted successfully', 'jblund-dealers')));
        } else {
            wp_send_json_error(array('message' => __('Failed to delete message', 'jblund-dealers')));
        }
    }

    /**
     * AJAX handler for setting active message
     */
    public function ajax_set_active_message() {
        check_ajax_referer('jblund_message_scheduler', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'jblund-dealers')));
        }

        $id = isset($_POST['id']) ? $_POST['id'] : '';

        if (empty($id)) {
            wp_send_json_error(array('message' => __('Invalid message ID', 'jblund-dealers')));
        }

        if ($this->set_active_message($id)) {
            wp_send_json_success(array('message' => __('Active message updated', 'jblund-dealers')));
        } else {
            wp_send_json_error(array('message' => __('Failed to set active message', 'jblund-dealers')));
        }
    }

    /**
     * AJAX handler for scheduling messages
     */
    public function ajax_schedule_message() {
        check_ajax_referer('jblund_message_scheduler', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'jblund-dealers')));
        }

        $message_id = isset($_POST['message_id']) ? $_POST['message_id'] : '';
        $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
        $start_time = isset($_POST['start_time']) ? $_POST['start_time'] : '';
        $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';
        $end_time = isset($_POST['end_time']) ? $_POST['end_time'] : '';

        if (empty($message_id) || empty($start_date) || empty($end_date)) {
            wp_send_json_error(array('message' => __('All fields are required', 'jblund-dealers')));
        }

        if ($this->schedule_message($message_id, $start_date, $start_time, $end_date, $end_time)) {
            wp_send_json_success(array('message' => __('Message scheduled successfully', 'jblund-dealers')));
        } else {
            wp_send_json_error(array('message' => __('Failed to schedule message', 'jblund-dealers')));
        }
    }

    /**
     * Render the message scheduler interface
     */
    public function render_scheduler_interface() {
        $messages = $this->get_messages();
        $schedules = $this->get_schedules();
        $active_message = $this->get_active_message();
        $active_id = get_option('jblund_dealers_active_message', 'default');
        ?>
        <div class="jblund-message-scheduler">
            <div class="scheduler-header" style="margin-bottom: 30px;">
                <h2><?php _e('Registration Success Messages', 'jblund-dealers'); ?></h2>
                <p class="description">
                    <?php _e('Create multiple success message templates and schedule them for specific time periods. Perfect for holidays, team outings, or special events when response times may vary.', 'jblund-dealers'); ?>
                </p>
            </div>

            <!-- Active Message Display -->
            <div class="active-message-display" style="padding: 20px; background: #d4edda; border-left: 4px solid #28a745; margin-bottom: 30px;">
                <h3 style="margin-top: 0; color: #155724;">
                    <span class="dashicons dashicons-yes-alt"></span>
                    <?php _e('Currently Active Message', 'jblund-dealers'); ?>
                </h3>
                <p><strong><?php echo esc_html($active_message['name']); ?></strong></p>
                <p><em><?php echo esc_html($active_message['title']); ?></em></p>
            </div>

            <!-- Saved Messages List -->
            <div class="saved-messages" style="margin-bottom: 30px;">
                <h3><?php _e('Saved Messages', 'jblund-dealers'); ?></h3>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Name', 'jblund-dealers'); ?></th>
                            <th><?php _e('Title', 'jblund-dealers'); ?></th>
                            <th><?php _e('Created', 'jblund-dealers'); ?></th>
                            <th><?php _e('Status', 'jblund-dealers'); ?></th>
                            <th><?php _e('Actions', 'jblund-dealers'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $message): ?>
                            <tr>
                                <td><strong><?php echo esc_html($message['name']); ?></strong></td>
                                <td><?php echo esc_html($message['title']); ?></td>
                                <td><?php echo date_i18n(get_option('date_format'), $message['created']); ?></td>
                                <td>
                                    <?php if ($message['id'] === $active_id): ?>
                                        <span class="dashicons dashicons-yes-alt" style="color: #28a745;"></span>
                                        <strong style="color: #28a745;"><?php _e('Active', 'jblund-dealers'); ?></strong>
                                    <?php else: ?>
                                        <span class="dashicons dashicons-minus"></span>
                                        <?php _e('Inactive', 'jblund-dealers'); ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="button button-small edit-message" data-id="<?php echo esc_attr($message['id']); ?>">
                                        <?php _e('Edit', 'jblund-dealers'); ?>
                                    </button>
                                    <?php if ($message['id'] !== $active_id): ?>
                                        <button class="button button-small set-active" data-id="<?php echo esc_attr($message['id']); ?>">
                                            <?php _e('Set Active', 'jblund-dealers'); ?>
                                        </button>
                                    <?php endif; ?>
                                    <button class="button button-small schedule-message" data-id="<?php echo esc_attr($message['id']); ?>">
                                        <?php _e('Schedule', 'jblund-dealers'); ?>
                                    </button>
                                    <?php if (!isset($message['is_default']) || !$message['is_default']): ?>
                                        <button class="button button-small button-link-delete delete-message" data-id="<?php echo esc_attr($message['id']); ?>">
                                            <?php _e('Delete', 'jblund-dealers'); ?>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p>
                    <button class="button button-primary" id="add-new-message">
                        <span class="dashicons dashicons-plus-alt"></span>
                        <?php _e('Add New Message', 'jblund-dealers'); ?>
                    </button>
                </p>
            </div>

            <!-- Scheduled Messages -->
            <?php if (!empty($schedules)): ?>
                <div class="scheduled-messages" style="margin-bottom: 30px;">
                    <h3><?php _e('Scheduled Messages', 'jblund-dealers'); ?></h3>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th><?php _e('Message', 'jblund-dealers'); ?></th>
                                <th><?php _e('Start', 'jblund-dealers'); ?></th>
                                <th><?php _e('End', 'jblund-dealers'); ?></th>
                                <th><?php _e('Status', 'jblund-dealers'); ?></th>
                                <th><?php _e('Actions', 'jblund-dealers'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schedules as $schedule):
                                $start_time = strtotime($schedule['start_date'] . ' ' . $schedule['start_time']);
                                $end_time = strtotime($schedule['end_date'] . ' ' . $schedule['end_time']);
                                $current_time = current_time('timestamp');
                                $is_active = ($current_time >= $start_time && $current_time <= $end_time);
                                $is_upcoming = ($current_time < $start_time);
                                $is_past = ($current_time > $end_time);
                                $message = isset($messages[$schedule['message_id']]) ? $messages[$schedule['message_id']] : null;
                            ?>
                                <tr>
                                    <td><strong><?php echo $message ? esc_html($message['name']) : __('(Deleted)', 'jblund-dealers'); ?></strong></td>
                                    <td><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $start_time); ?></td>
                                    <td><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $end_time); ?></td>
                                    <td>
                                        <?php if ($is_active): ?>
                                            <span class="dashicons dashicons-clock" style="color: #28a745;"></span>
                                            <strong style="color: #28a745;"><?php _e('Active Now', 'jblund-dealers'); ?></strong>
                                        <?php elseif ($is_upcoming): ?>
                                            <span class="dashicons dashicons-calendar-alt" style="color: #0073aa;"></span>
                                            <?php _e('Upcoming', 'jblund-dealers'); ?>
                                        <?php else: ?>
                                            <span class="dashicons dashicons-archive" style="color: #999;"></span>
                                            <?php _e('Past', 'jblund-dealers'); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="button button-small button-link-delete delete-schedule" data-id="<?php echo esc_attr($schedule['id']); ?>">
                                            <?php _e('Delete', 'jblund-dealers'); ?>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <!-- Hidden form template for new/edit message modal -->
            <div id="message-editor-modal" style="display: none;">
                <div class="modal-content" style="background: #fff; padding: 20px; max-width: 800px; margin: 50px auto;">
                    <h2 id="modal-title"><?php _e('Add New Message', 'jblund-dealers'); ?></h2>
                    <form id="message-editor-form">
                        <input type="hidden" id="message-id" name="message_id" value="">
                        <table class="form-table">
                            <tr>
                                <th><label for="message-name"><?php _e('Message Name', 'jblund-dealers'); ?></label></th>
                                <td>
                                    <input type="text" id="message-name" name="message_name" class="regular-text" required>
                                    <p class="description"><?php _e('Internal name for this message (e.g., "Holiday Schedule", "Standard Response")', 'jblund-dealers'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="message-title"><?php _e('Success Page Title', 'jblund-dealers'); ?></label></th>
                                <td>
                                    <input type="text" id="message-title" name="message_title" class="regular-text" required>
                                    <p class="description"><?php _e('The headline shown to applicants', 'jblund-dealers'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="message-body"><?php _e('Message Body', 'jblund-dealers'); ?></label></th>
                                <td>
                                    <textarea id="message-body" name="message_body" rows="6" class="large-text" required></textarea>
                                    <p class="description"><?php _e('The main message content', 'jblund-dealers'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="message-note"><?php _e('Timeline Note', 'jblund-dealers'); ?></label></th>
                                <td>
                                    <input type="text" id="message-note" name="message_note" class="large-text">
                                    <p class="description"><?php _e('Optional timeline information', 'jblund-dealers'); ?></p>
                                </td>
                            </tr>
                        </table>
                        <p>
                            <button type="submit" class="button button-primary"><?php _e('Save Message', 'jblund-dealers'); ?></button>
                            <button type="button" class="button cancel-modal"><?php _e('Cancel', 'jblund-dealers'); ?></button>
                        </p>
                    </form>
                </div>
            </div>

            <!-- Hidden form template for scheduling modal -->
            <div id="schedule-editor-modal" style="display: none;">
                <div class="modal-content" style="background: #fff; padding: 20px; max-width: 600px; margin: 50px auto;">
                    <h2><?php _e('Schedule Message', 'jblund-dealers'); ?></h2>
                    <form id="schedule-editor-form">
                        <input type="hidden" id="schedule-message-id" name="schedule_message_id" value="">
                        <table class="form-table">
                            <tr>
                                <th><label for="schedule-start-date"><?php _e('Start Date', 'jblund-dealers'); ?></label></th>
                                <td>
                                    <input type="date" id="schedule-start-date" name="schedule_start_date" required>
                                    <input type="time" id="schedule-start-time" name="schedule_start_time" value="00:00" required>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="schedule-end-date"><?php _e('End Date', 'jblund-dealers'); ?></label></th>
                                <td>
                                    <input type="date" id="schedule-end-date" name="schedule_end_date" required>
                                    <input type="time" id="schedule-end-time" name="schedule_end_time" value="23:59" required>
                                </td>
                            </tr>
                        </table>
                        <p class="description">
                            <?php _e('This message will automatically become active during the scheduled time period, then revert to your default message afterward.', 'jblund-dealers'); ?>
                        </p>
                        <p>
                            <button type="submit" class="button button-primary"><?php _e('Schedule Message', 'jblund-dealers'); ?></button>
                            <button type="button" class="button cancel-modal"><?php _e('Cancel', 'jblund-dealers'); ?></button>
                        </p>
                    </form>
                </div>
            </div>

            <script>
            jQuery(document).ready(function($) {
                var messages = <?php echo json_encode($messages); ?>;

                var messageScheduler = {
                    nonce: '<?php echo wp_create_nonce('jblund_message_scheduler'); ?>',

                    // Show message editor modal
                    showMessageEditor: function(messageId) {
                        if (messageId) {
                            // Editing existing message
                            var message = messages[messageId];
                            if (message) {
                                $('#modal-title').text('<?php _e('Edit Message', 'jblund-dealers'); ?>');
                                $('#message-id').val(message.id);
                                $('#message-name').val(message.name);
                                $('#message-title').val(message.title);
                                $('#message-body').val(message.message);
                                $('#message-note').val(message.note);
                            }
                        } else {
                            // Adding new message
                            $('#modal-title').text('<?php _e('Add New Message', 'jblund-dealers'); ?>');
                            $('#message-id').val('');
                            $('#message-name').val('');
                            $('#message-title').val('');
                            $('#message-body').val('');
                            $('#message-note').val('');
                        }
                        $('#message-editor-modal').show();
                    },

                    // Show schedule editor modal
                    showScheduleEditor: function(messageId) {
                        $('#schedule-message-id').val(messageId);
                        $('#schedule-editor-modal').show();
                    },

                    // Save message
                    saveMessage: function(formData) {
                        $.post(ajaxurl, {
                            action: 'jblund_save_message',
                            nonce: this.nonce,
                            name: formData.name,
                            title: formData.title,
                            message: formData.message,
                            note: formData.note,
                            id: formData.id
                        }, function(response) {
                            if (response.success) {
                                location.reload();
                            } else {
                                alert(response.data.message);
                            }
                        });
                    },

                    // Delete message
                    deleteMessage: function(messageId) {
                        if (!confirm('<?php _e('Are you sure you want to delete this message?', 'jblund-dealers'); ?>')) {
                            return;
                        }

                        $.post(ajaxurl, {
                            action: 'jblund_delete_message',
                            nonce: this.nonce,
                            id: messageId
                        }, function(response) {
                            if (response.success) {
                                location.reload();
                            } else {
                                alert(response.data.message);
                            }
                        });
                    },

                    // Set active message
                    setActiveMessage: function(messageId) {
                        $.post(ajaxurl, {
                            action: 'jblund_set_active_message',
                            nonce: this.nonce,
                            id: messageId
                        }, function(response) {
                            if (response.success) {
                                location.reload();
                            } else {
                                alert(response.data.message);
                            }
                        });
                    },

                    // Schedule message
                    scheduleMessage: function(formData) {
                        $.post(ajaxurl, {
                            action: 'jblund_schedule_message',
                            nonce: this.nonce,
                            message_id: formData.message_id,
                            start_date: formData.start_date,
                            start_time: formData.start_time,
                            end_date: formData.end_date,
                            end_time: formData.end_time
                        }, function(response) {
                            if (response.success) {
                                location.reload();
                            } else {
                                alert(response.data.message);
                            }
                        });
                    }
                };

                // Event handlers
                $('#add-new-message').on('click', function() {
                    messageScheduler.showMessageEditor(null);
                });

                $('.edit-message').on('click', function() {
                    messageScheduler.showMessageEditor($(this).data('id'));
                });

                $('.delete-message').on('click', function() {
                    messageScheduler.deleteMessage($(this).data('id'));
                });

                $('.set-active').on('click', function() {
                    messageScheduler.setActiveMessage($(this).data('id'));
                });

                $('.schedule-message').on('click', function() {
                    messageScheduler.showScheduleEditor($(this).data('id'));
                });

                $('.cancel-modal').on('click', function() {
                    $(this).closest('[id$="-modal"]').hide();
                });

                $('#message-editor-form').on('submit', function(e) {
                    e.preventDefault();
                    messageScheduler.saveMessage({
                        id: $('#message-id').val(),
                        name: $('#message-name').val(),
                        title: $('#message-title').val(),
                        message: $('#message-body').val(),
                        note: $('#message-note').val()
                    });
                });

                $('#schedule-editor-form').on('submit', function(e) {
                    e.preventDefault();
                    messageScheduler.scheduleMessage({
                        message_id: $('#schedule-message-id').val(),
                        start_date: $('#schedule-start-date').val(),
                        start_time: $('#schedule-start-time').val(),
                        end_date: $('#schedule-end-date').val(),
                        end_time: $('#schedule-end_time').val()
                    });
                });
            });
            </script>
        </div>
        <?php
    }
}
