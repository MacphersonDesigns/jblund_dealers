<?php
/**
 * Email template for dealer NDA confirmation.
 *
 * Adapted from login-terms-acceptance email-template.php
 *
 * @var string $user_name       Dealer representative name
 * @var string $dealer_name     Dealer company name
 * @var string $acceptance_date Date NDA was accepted
 *
 * @package JBLund_Dealers
 * @subpackage Dealer_Portal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo \esc_html__( 'NDA Acceptance Confirmation', 'jblund-dealers' ); ?></title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4;">
<div style="font-family: Arial, sans-serif; font-size: 16px; line-height: 1.6; max-width: 600px; margin: 20px auto; background-color: #ffffff; border: 1px solid #e5e5e5; border-radius: 10px; overflow: hidden; box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1);">
	<!-- Header -->
	<div style="background-color: #003366; color: #ffffff; padding: 30px 20px; text-align: center;">
		<h1 style="margin: 0; font-size: 24px;"><?php echo \esc_html__( 'NDA Acceptance Confirmation', 'jblund-dealers' ); ?></h1>
		<p style="margin: 10px 0 0 0; font-size: 14px; opacity: 0.9;"><?php echo \esc_html__( 'JB Lund Dock B2B Dealer Portal', 'jblund-dealers' ); ?></p>
	</div>

	<!-- Content -->
	<div style="padding: 30px 20px;">
		<p style="margin: 0 0 15px 0;"><?php echo \esc_html( \sprintf( __( 'Dear %s,', 'jblund-dealers' ), $user_name ) ); ?></p>

		<p style="margin: 0 0 15px 0;"><?php echo \esc_html__( 'Thank you for accepting the JB Lund Dock B2B Non-Disclosure Agreement.', 'jblund-dealers' ); ?></p>

		<div style="background-color: #f9f9f9; border-left: 4px solid #003366; padding: 15px; margin: 20px 0;">
			<p style="margin: 0 0 10px 0; font-weight: bold; color: #003366;"><?php echo \esc_html__( 'Agreement Details:', 'jblund-dealers' ); ?></p>
			<p style="margin: 0 0 5px 0;"><strong><?php echo \esc_html__( 'Dealer:', 'jblund-dealers' ); ?></strong> <?php echo \esc_html( $dealer_name ); ?></p>
			<p style="margin: 0 0 5px 0;"><strong><?php echo \esc_html__( 'Representative:', 'jblund-dealers' ); ?></strong> <?php echo \esc_html( $user_name ); ?></p>
			<p style="margin: 0;"><strong><?php echo \esc_html__( 'Date Accepted:', 'jblund-dealers' ); ?></strong> <?php echo \esc_html( $acceptance_date ); ?></p>
		</div>

		<p style="margin: 0 0 15px 0;"><?php echo \esc_html__( 'Your signed NDA document is attached to this email for your records.', 'jblund-dealers' ); ?></p>

		<p style="margin: 0 0 20px 0;"><?php echo \esc_html__( 'You now have full access to the dealer portal. Please log in to access pricing, resources, and support.', 'jblund-dealers' ); ?></p>

		<!-- CTA Button -->
		<div style="text-align: center; margin: 30px 0;">
			<a href="<?php echo \esc_url( \home_url( '/dealer-dashboard/' ) ); ?>" style="display: inline-block; background-color: #003366; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;"><?php echo \esc_html__( 'Access Dealer Portal', 'jblund-dealers' ); ?></a>
		</div>

		<p style="margin: 20px 0 0 0; font-size: 14px; color: #666;"><?php echo \esc_html__( 'If you have any questions or need assistance, please contact our support team.', 'jblund-dealers' ); ?></p>
	</div>

	<!-- Footer -->
	<div style="padding: 20px; text-align: center; background-color: #f9f9f9; border-top: 1px solid #e5e5e5;">
		<p style="margin: 0; font-size: 12px; color: #666;">
			&copy; <?php echo \esc_html( \date( 'Y' ) ); ?> <?php echo \esc_html__( 'JB Lund Dock B2B. All rights reserved.', 'jblund-dealers' ); ?>
		</p>
	</div>
</div>
</body>
</html>
