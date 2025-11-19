<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php _e('Dealer Portal Application Update', 'jblund-dealers'); ?></title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f4; padding: 20px 0;">
        <tr>
            <td align="center">
                <!-- Email Container -->
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">

                    <!-- Header -->
                    <tr>
                        <td style="background-color: #6c757d; padding: 30px 40px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 600;">
                                <?php _e('Dealer Portal Application Update', 'jblund-dealers'); ?>
                            </h1>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 40px;">

                            <!-- Greeting -->
                            <p style="margin: 0 0 20px; font-size: 16px; color: #333333; line-height: 1.6;">
                                <?php printf(__('Hello %s,', 'jblund-dealers'), '<strong>' . esc_html($rep_name) . '</strong>'); ?>
                            </p>

                            <!-- Main Message -->
                            <p style="margin: 0 0 20px; font-size: 16px; color: #333333; line-height: 1.6;">
                                <?php _e('Thank you for your interest in joining the JBLund Dealer Network. We have carefully reviewed your application, and at this time, we are unable to approve your registration for the following reason:', 'jblund-dealers'); ?>
                            </p>

                            <!-- Reason Box -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 30px 0; background-color: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0; font-size: 15px; color: #856404; font-style: italic; line-height: 1.6;">
                                            "<?php echo esc_html($reason); ?>"
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Next Steps -->
                            <h3 style="margin: 30px 0 15px; font-size: 18px; color: #003366;">
                                <?php _e('What\'s Next?', 'jblund-dealers'); ?>
                            </h3>
                            <p style="margin: 0 0 20px; font-size: 15px; color: #555; line-height: 1.7;">
                                <?php _e('We understand this may not be the news you were hoping for. However, circumstances change, and we encourage you to:', 'jblund-dealers'); ?>
                            </p>
                            <ul style="margin: 0 0 30px; padding-left: 20px; font-size: 15px; color: #555; line-height: 1.8;">
                                <li><?php _e('Contact us if you have questions about this decision', 'jblund-dealers'); ?></li>
                                <li><?php _e('Address any concerns mentioned in the reason above', 'jblund-dealers'); ?></li>
                                <li><?php _e('Consider reapplying in the future when circumstances allow', 'jblund-dealers'); ?></li>
                            </ul>

                            <!-- Support Information -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 30px 0; background-color: #f8f9fa; border-radius: 4px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="margin: 0 0 10px; font-size: 16px; color: #003366;">
                                            <?php _e('Need to Discuss Your Application?', 'jblund-dealers'); ?>
                                        </h3>
                                        <p style="margin: 0; font-size: 14px; color: #555; line-height: 1.6;">
                                            <?php _e('If you believe this decision was made in error, or if you\'d like to discuss your application further, please don\'t hesitate to reach out to our dealer relations team. We\'re happy to provide additional clarification or reconsider your application under the right circumstances.', 'jblund-dealers'); ?>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Closing Message -->
                            <p style="margin: 30px 0 0; font-size: 15px; color: #333; line-height: 1.6;">
                                <?php _e('We appreciate your interest in partnering with JBLund and wish you the best in your business endeavors.', 'jblund-dealers'); ?>
                            </p>

                            <p style="margin: 20px 0 0; font-size: 15px; color: #333;">
                                <?php _e('Best regards,', 'jblund-dealers'); ?><br>
                                <strong><?php _e('The JBLund Dealer Relations Team', 'jblund-dealers'); ?></strong>
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 20px 40px; text-align: center; border-top: 1px solid #dee2e6;">
                            <p style="margin: 0; font-size: 13px; color: #6c757d; line-height: 1.5;">
                                <?php _e('This is an automated message from the JBLund Dealer Portal.', 'jblund-dealers'); ?><br>
                                <?php printf(__('© %s JBLund. All rights reserved.', 'jblund-dealers'), date('Y')); ?>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
