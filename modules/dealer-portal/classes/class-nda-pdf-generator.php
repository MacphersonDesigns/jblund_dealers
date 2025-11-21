<?php
/**
 * NDA PDF Generator
 *
 * Generates PDF versions of signed NDAs using TCPDF library
 *
 * @package JBLund_Dealers
 * @subpackage DealerPortal
 */

namespace JBLund\DealerPortal;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class NDA_PDF_Generator {

    /**
     * Upload directory for signed NDAs
     */
    private $upload_dir = 'dealers/signed-documents';

    /**
     * Constructor
     */
    public function __construct() {
        add_action('jblund_dealer_nda_accepted', array($this, 'generate_nda_pdf'), 10, 2);
        add_action('init', array($this, 'ensure_upload_directory'));
    }

    /**
     * Ensure the upload directory exists
     */
    public function ensure_upload_directory() {
        $upload_base = wp_upload_dir();
        $nda_dir = trailingslashit($upload_base['basedir']) . $this->upload_dir;

        if (!file_exists($nda_dir)) {
            wp_mkdir_p($nda_dir);

            // Add .htaccess to protect directory
            $htaccess_file = $nda_dir . '/.htaccess';
            if (!file_exists($htaccess_file)) {
                $htaccess_content = "Options -Indexes\n";
                $htaccess_content .= "<FilesMatch '\.(pdf)$'>\n";
                $htaccess_content .= "    Order Allow,Deny\n";
                $htaccess_content .= "    Allow from all\n";
                $htaccess_content .= "</FilesMatch>";
                file_put_contents($htaccess_file, $htaccess_content);
            }
        }
    }

    /**
     * Generate NDA PDF when dealer accepts
     *
     * @param int $user_id User ID who accepted
     * @param string $signature_data Base64 signature data
     */
    public function generate_nda_pdf($user_id, $signature_data) {
        // TCPDF is loaded via Composer autoloader
        if (!class_exists('TCPDF')) {
            error_log('TCPDF class not found. Make sure Composer dependencies are installed.');
            return false;
        }

        // Get user data
        $user = get_userdata($user_id);
        if (!$user) {
            return false;
        }

        // Get NDA content from NDA Editor (single source of truth)
        if (class_exists('JBLund\DealerPortal\NDA_Editor')) {
            $nda_content = \JBLund\DealerPortal\NDA_Editor::get_content();
        } else {
            $nda_content = $this->get_default_nda_content();
        }

        // Get acceptance data
        $acceptance_data = json_decode(get_user_meta($user_id, '_dealer_nda_acceptance', true), true);
        $acceptance_date = isset($acceptance_data['acceptance_date']) ? $acceptance_data['acceptance_date'] : current_time('mysql');
        $acceptance_ip = isset($acceptance_data['acceptance_ip']) ? $acceptance_data['acceptance_ip'] : '';

        // Get linked dealer post if exists
        $dealer_post = $this->get_linked_dealer_post($user_id);
        $company_name = $dealer_post ? get_the_title($dealer_post->ID) : get_user_meta($user_id, '_dealer_company_name', true);

        // Create PDF
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator('JBLund Dealers Plugin');
        $pdf->SetAuthor($company_name);
        $pdf->SetTitle('Non-Disclosure Agreement - ' . $company_name);
        $pdf->SetSubject('Signed NDA');

        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Set margins
        $pdf->SetMargins(20, 20, 20);
        $pdf->SetAutoPageBreak(true, 20);

        // Add a page
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('helvetica', '', 11);

        // Add header
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->Cell(0, 15, 'Non-Disclosure Agreement', 0, 1, 'C');
        $pdf->Ln(5);

        // Add NDA content
        $pdf->SetFont('helvetica', '', 11);
        $pdf->writeHTML($nda_content, true, false, true, false, '');
        $pdf->Ln(10);

        // Add acceptance section
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Agreement Acceptance', 0, 1, 'L');
        $pdf->Ln(2);

        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(40, 8, 'Company:', 0, 0, 'L');
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 8, $company_name, 0, 1, 'L');

        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(40, 8, 'Accepted by:', 0, 0, 'L');
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 8, $user->display_name . ' (' . $user->user_email . ')', 0, 1, 'L');

        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(40, 8, 'Date:', 0, 0, 'L');
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 8, date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($acceptance_date)), 0, 1, 'L');

        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(40, 8, 'IP Address:', 0, 0, 'L');
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 8, $acceptance_ip, 0, 1, 'L');

        // Add signature if provided
        if ($signature_data && strpos($signature_data, 'data:image') === 0) {
            $pdf->Ln(10);
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(0, 8, 'Digital Signature:', 0, 1, 'L');

            // Decode base64 image
            $signature_data = str_replace('data:image/png;base64,', '', $signature_data);
            $signature_data = base64_decode($signature_data);

            // Save temp image
            $temp_file = tempnam(sys_get_temp_dir(), 'sig') . '.png';
            file_put_contents($temp_file, $signature_data);

            // Add image to PDF
            $pdf->Image($temp_file, 20, $pdf->GetY(), 60, 20, 'PNG');

            // Clean up
            @unlink($temp_file);
        }

        // Generate filename
        $filename = 'nda-' . sanitize_file_name($company_name) . '-' . date('Y-m-d-His') . '.pdf';

        // Get upload directory
        $upload_base = wp_upload_dir();
        $pdf_dir = trailingslashit($upload_base['basedir']) . $this->upload_dir;
        $pdf_url_dir = trailingslashit($upload_base['baseurl']) . $this->upload_dir;

        // Save PDF
        $pdf_path = $pdf_dir . '/' . $filename;
        $pdf_url = $pdf_url_dir . '/' . $filename;

        $pdf->Output($pdf_path, 'F');

        // Store PDF info in user meta
        update_user_meta($user_id, '_dealer_nda_pdf_path', $pdf_path);
        update_user_meta($user_id, '_dealer_nda_pdf_url', $pdf_url);
        update_user_meta($user_id, '_dealer_nda_signed_date', $acceptance_date);
        update_user_meta($user_id, '_dealer_nda_ip', $acceptance_ip);

        // If linked to dealer post, store there too
        if ($dealer_post) {
            update_post_meta($dealer_post->ID, '_dealer_nda_pdf', $pdf_url);
            update_post_meta($dealer_post->ID, '_dealer_nda_signed_date', $acceptance_date);
            update_post_meta($dealer_post->ID, '_dealer_nda_ip', $acceptance_ip);
        }

        // Send admin notification email
        $this->send_admin_notification($user, $company_name, $pdf_url);

        // Send dealer confirmation email
        $this->send_dealer_confirmation($user, $company_name, $pdf_url);

        return $pdf_url;
    }

    /**
     * Get linked dealer post for user
     */
    private function get_linked_dealer_post($user_id) {
        $args = array(
            'post_type' => 'dealer',
            'posts_per_page' => 1,
            'meta_query' => array(
                array(
                    'key' => '_dealer_linked_user_id',
                    'value' => $user_id,
                    'compare' => '='
                )
            )
        );

        $query = new \WP_Query($args);
        return $query->have_posts() ? $query->posts[0] : null;
    }

    /**
     * Send admin notification email
     */
    private function send_admin_notification($user, $company_name, $pdf_url) {
        $admin_email = get_option('admin_email');
        $subject = sprintf(__('[%s] New NDA Signed', 'jblund-dealers'), get_bloginfo('name'));

        $message = sprintf(
            __("A dealer has signed the NDA agreement.\n\nCompany: %s\nUser: %s (%s)\nDate: %s\n\nView signed NDA: %s", 'jblund-dealers'),
            $company_name,
            $user->display_name,
            $user->user_email,
            date_i18n(get_option('date_format') . ' ' . get_option('time_format')),
            $pdf_url
        );

        wp_mail($admin_email, $subject, $message);
    }

    /**
     * Send dealer confirmation email
     */
    private function send_dealer_confirmation($user, $company_name, $pdf_url) {
        $subject = sprintf(__('[%s] NDA Agreement Confirmation', 'jblund-dealers'), get_bloginfo('name'));

        $message = sprintf(
            __("Thank you for accepting our Non-Disclosure Agreement.\n\nA copy of your signed NDA has been generated and is available for download:\n\n%s\n\nYou can also access this from your dealer dashboard at any time.", 'jblund-dealers'),
            $pdf_url
        );

        wp_mail($user->user_email, $subject, $message);
    }

    /**
     * Get default NDA content
     */
    private function get_default_nda_content() {
        return '<p>This Non-Disclosure Agreement ("Agreement") is entered into for the purpose of protecting confidential and proprietary information.</p>

        <h3>1. Confidential Information</h3>
        <p>Confidential Information includes all non-public information, whether written, oral, or in any other form, that is disclosed by one party to the other.</p>

        <h3>2. Obligations</h3>
        <p>The receiving party agrees to:
        <ul>
            <li>Maintain the confidentiality of all Confidential Information</li>
            <li>Use the Confidential Information solely for authorized purposes</li>
            <li>Not disclose the Confidential Information to any third parties</li>
        </ul>
        </p>

        <h3>3. Term</h3>
        <p>This Agreement shall remain in effect for the duration of the business relationship and for a period of five (5) years thereafter.</p>';
    }
}
