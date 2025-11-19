<?php
/**
 * Email template for admin notification of NDA acceptance.
 *
 * Adapted from login-terms-acceptance email-template.php
 *
 * @var string $user_name       Dealer representative name
 * @var string $user_email      Dealer representative email
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
	<title><?php echo \esc_html__( 'New Dealer NDA Accepted', 'jblund-dealers' ); ?></title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4;">
<div style="font-family: Arial, sans-serif; font-size: 16px; line-height: 1.6; max-width: 600px; margin: 20px auto; background-color: #ffffff; border: 1px solid #e5e5e5; border-radius: 10px; overflow: hidden; box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1);">
	<!-- Header -->
	<div style="background-color: #28a745; color: #ffffff; padding: 30px 20px; text-align: center;">
		<h1 style="margin: 0; font-size: 24px;"><?php echo \esc_html__( '✓ New Dealer NDA Accepted', 'jblund-dealers' ); ?></h1>
		<p style="margin: 10px 0 0 0; font-size: 14px; opacity: 0.9;"><?php echo \esc_html__( 'Dealer Portal Admin Notification', 'jblund-dealers' ); ?></p>
	</div>

	<!-- Content -->
	<div style="padding: 30px 20px;">
		<p style="margin: 0 0 15px 0; font-size: 18px; color: #333;"><?php echo \esc_html__( 'A dealer has successfully accepted the NDA and gained portal access.', 'jblund-dealers' ); ?></p>

		<div style="background-color: #f9f9f9; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0;">
			<p style="margin: 0 0 10px 0; font-weight: bold; color: #28a745;"><?php echo \esc_html__( 'Dealer Information:', 'jblund-dealers' ); ?></p>
			<p style="margin: 0 0 5px 0;"><strong><?php echo \esc_html__( 'Dealer Company:', 'jblund-dealers' ); ?></strong> <?php echo \esc_html( $dealer_name ); ?></p>
			<p style="margin: 0 0 5px 0;"><strong><?php echo \esc_html__( 'Representative:', 'jblund-dealers' ); ?></strong> <?php echo \esc_html( $user_name ); ?></p>
			<p style="margin: 0 0 5px 0;"><strong><?php echo \esc_html__( 'Email:', 'jblund-dealers' ); ?></strong> <?php echo \esc_html( $user_email ); ?></p>
			<p style="margin: 0;"><strong><?php echo \esc_html__( 'Date Accepted:', 'jblund-dealers' ); ?></strong> <?php echo \esc_html( $acceptance_date ); ?></p>
		</div>

		<p style="margin: 0 0 15px 0;"><?php echo \esc_html__( 'The signed NDA document is attached to this email for your records.', 'jblund-dealers' ); ?></p>

		<div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0;">
			<p style="margin: 0 0 10px 0; font-weight: bold; color: #856404;"><?php echo \esc_html__( 'Next Steps:', 'jblund-dealers' ); ?></p>
			<ul style="margin: 0; padding-left: 20px;">
				<li><?php echo \esc_html__( 'Dealer now has full portal access', 'jblund-dealers' ); ?></li>
				<li><?php echo \esc_html__( 'Signed NDA PDF is stored in dealer records', 'jblund-dealers' ); ?></li>
				<li><?php echo \esc_html__( 'Review dealer territory assignments if needed', 'jblund-dealers' ); ?></li>
			</ul>
		</div>

		<!-- CTA Button -->
		<div style="text-align: center; margin: 30px 0;">
			<a href="<?php echo \esc_url( \admin_url( 'edit.php?post_type=dealer' ) ); ?>" style="display: inline-block; background-color: #003366; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;"><?php echo \esc_html__( 'View Dealer Records', 'jblund-dealers' ); ?></a>
		</div>
	</div>

	<!-- Footer -->
	<div style="padding: 20px; text-align: center; background-color: #f9f9f9; border-top: 1px solid #e5e5e5;">
		<p style="margin: 0; font-size: 12px; color: #666;">
			<?php echo \esc_html__( 'This is an automated notification from the JB Lund Dealer Portal.', 'jblund-dealers' ); ?>
		</p>
		<p style="margin: 5px 0 0 0; font-size: 12px; color: #666;">
			&copy; <?php echo \esc_html( \date( 'Y' ) ); ?> <?php echo \esc_html__( 'JB Lund Dock B2B. All rights reserved.', 'jblund-dealers' ); ?>
		</p>
	</div>
</div>
</body>
</html>
