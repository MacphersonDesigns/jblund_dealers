<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php _e('Dealer Portal Account Approved', 'jblund-dealers'); ?></title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f4; padding: 20px 0;">
        <tr>
            <td align="center">
                <!-- Email Container -->
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">

                    <!-- Header -->
                    <tr>
                        <td style="background-color: #003366; padding: 30px 40px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 600;">
                                <?php _e('Welcome to JBLund Dealer Portal', 'jblund-dealers'); ?>
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
                                <?php _e('Congratulations! Your dealer portal registration has been approved. Your account has been created and you can now access the JBLund Dealer Portal.', 'jblund-dealers'); ?>
                            </p>

                            <!-- Login Credentials Box -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 30px 0; background-color: #f8f9fa; border-left: 4px solid #003366; border-radius: 4px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="margin: 0 0 15px; font-size: 18px; color: #003366;">
                                            <?php _e('Your Login Credentials', 'jblund-dealers'); ?>
                                        </h3>
                                        <p style="margin: 0 0 10px; font-size: 14px; color: #555;">
                                            <strong><?php _e('Username:', 'jblund-dealers'); ?></strong>
                                            <span style="font-family: 'Courier New', monospace; color: #003366;"><?php echo esc_html($username); ?></span>
                                        </p>
                                        <p style="margin: 0 0 10px; font-size: 14px; color: #555;">
                                            <strong><?php _e('Temporary Password:', 'jblund-dealers'); ?></strong>
                                            <span style="font-family: 'Courier New', monospace; color: #003366;"><?php echo esc_html($password); ?></span>
                                        </p>
                                        <p style="margin: 15px 0 0; font-size: 13px; color: #666; font-style: italic;">
                                            <?php _e('For security, please change your password after your first login.', 'jblund-dealers'); ?>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Next Steps -->
                            <h3 style="margin: 30px 0 15px; font-size: 18px; color: #003366;">
                                <?php _e('Next Steps', 'jblund-dealers'); ?>
                            </h3>
                            <ol style="margin: 0 0 30px; padding-left: 20px; font-size: 15px; color: #555; line-height: 1.8;">
                                <li><?php _e('Log in to the dealer portal using the credentials above', 'jblund-dealers'); ?></li>
                                <li><?php _e('Review and sign the Non-Disclosure Agreement (NDA)', 'jblund-dealers'); ?></li>
                                <li><?php _e('Complete your dealer profile with company information', 'jblund-dealers'); ?></li>
                                <li><?php _e('Explore resources, pricing, and territory information', 'jblund-dealers'); ?></li>
                            </ol>

                            <!-- CTA Button -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="<?php echo esc_url($login_url); ?>"
                                           style="display: inline-block; padding: 14px 32px; background-color: #003366; color: #ffffff; text-decoration: none; border-radius: 4px; font-size: 16px; font-weight: 600;">
                                            <?php _e('Log In to Dealer Portal', 'jblund-dealers'); ?>
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Support Information -->
                            <p style="margin: 30px 0 0; padding: 20px; background-color: #f8f9fa; border-radius: 4px; font-size: 14px; color: #555; line-height: 1.6;">
                                <?php _e('If you have any questions or need assistance, please contact our dealer support team. We\'re here to help you get started!', 'jblund-dealers'); ?>
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
