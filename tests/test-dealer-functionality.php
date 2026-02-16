<?php
/**
 * Dealer Core Functionality Tests
 *
 * Tests for dealer creation, editing, and display.
 *
 * @package JBLund_Dealers
 * @subpackage Tests
 */

class Dealer_Functionality_Tests extends WP_UnitTestCase {

	/**
	 * Test dealer post type is registered
	 */
	public function test_dealer_post_type_registered() {
		$this->assertTrue( post_type_exists( 'dealer' ), 'Dealer post type should be registered' );
	}

	/**
	 * Test dealer registration post type is registered
	 */
	public function test_registration_post_type_registered() {
		$this->assertTrue( post_type_exists( 'dealer_registration' ), 'Dealer registration post type should be registered' );
	}

	/**
	 * Test creating a basic dealer
	 */
	public function test_create_basic_dealer() {
		$dealer_id = self::factory()->post->create( array(
			'post_type' => 'dealer',
			'post_title' => 'Test Dealer',
			'post_status' => 'publish',
		) );
		
		$dealer = get_post( $dealer_id );
		
		$this->assertNotNull( $dealer );
		$this->assertEquals( 'dealer', $dealer->post_type );
		$this->assertEquals( 'publish', $dealer->post_status );
	}

	/**
	 * Test dealer meta fields are saved correctly
	 */
	public function test_dealer_meta_fields_saved() {
		$dealer_id = self::factory()->post->create( array(
			'post_type' => 'dealer',
			'post_title' => 'Meta Test Dealer',
		) );
		
		$test_data = array(
			'_dealer_company_address' => '123 Harbor St, Marina Bay, CA',
			'_dealer_company_phone' => '(555) 123-4567',
			'_dealer_website' => 'https://testdealer.com',
			'_dealer_docks' => '1',
			'_dealer_lifts' => '0',
			'_dealer_trailers' => '1',
		);
		
		foreach ( $test_data as $key => $value ) {
			update_post_meta( $dealer_id, $key, $value );
		}
		
		// Verify all meta saved correctly
		foreach ( $test_data as $key => $expected_value ) {
			$saved_value = get_post_meta( $dealer_id, $key, true );
			$this->assertEquals( $expected_value, $saved_value, "Meta key $key should be saved correctly" );
		}
	}

	/**
	 * Test dealer sub-locations are serialized correctly
	 */
	public function test_dealer_sublocations_serialized() {
		$dealer_id = self::factory()->post->create( array(
			'post_type' => 'dealer',
			'post_title' => 'Multi-Location Dealer',
		) );
		
		$sublocations = array(
			array(
				'name' => 'Main Location',
				'address' => '123 Main St',
				'phone' => '555-1111',
				'website' => 'https://main.example.com',
				'docks' => '1',
				'lifts' => '1',
				'trailers' => '0',
			),
			array(
				'name' => 'Branch Location',
				'address' => '456 Branch Ave',
				'phone' => '555-2222',
				'website' => '',
				'docks' => '0',
				'lifts' => '1',
				'trailers' => '1',
			),
		);
		
		update_post_meta( $dealer_id, '_dealer_sublocations', $sublocations );
		
		$saved_sublocations = get_post_meta( $dealer_id, '_dealer_sublocations', true );
		
		$this->assertIsArray( $saved_sublocations );
		$this->assertCount( 2, $saved_sublocations );
		$this->assertEquals( 'Main Location', $saved_sublocations[0]['name'] );
		$this->assertEquals( 'Branch Location', $saved_sublocations[1]['name'] );
	}

	/**
	 * Test shortcode displays dealers
	 */
	public function test_shortcode_displays_dealers() {
		// Create test dealers
		$dealer1_id = self::factory()->post->create( array(
			'post_type' => 'dealer',
			'post_title' => 'Display Test Dealer 1',
			'post_status' => 'publish',
		) );
		
		$dealer2_id = self::factory()->post->create( array(
			'post_type' => 'dealer',
			'post_title' => 'Display Test Dealer 2',
			'post_status' => 'publish',
		) );
		
		// Get shortcode output
		$output = do_shortcode( '[jblund_dealers]' );
		
		// Should contain dealer information
		$this->assertStringContainsString( 'Display Test Dealer 1', $output );
		$this->assertStringContainsString( 'Display Test Dealer 2', $output );
	}

	/**
	 * Test shortcode respects layout parameter
	 */
	public function test_shortcode_layout_parameter() {
		self::factory()->post->create( array(
			'post_type' => 'dealer',
			'post_title' => 'Layout Test Dealer',
			'post_status' => 'publish',
		) );
		
		$layouts = array( 'grid', 'list', 'compact' );
		
		foreach ( $layouts as $layout ) {
			$output = do_shortcode( '[jblund_dealers layout="' . $layout . '"]' );
			$this->assertIsString( $output );
			$this->assertNotEmpty( $output );
		}
	}

	/**
	 * Test shortcode respects posts_per_page parameter
	 */
	public function test_shortcode_posts_per_page_parameter() {
		for ( $i = 1; $i <= 5; $i++ ) {
			self::factory()->post->create( array(
				'post_type' => 'dealer',
				'post_title' => "Dealer $i",
				'post_status' => 'publish',
			) );
		}
		
		$output = do_shortcode( '[jblund_dealers posts_per_page="2"]' );
		$this->assertIsString( $output );
		$this->assertNotEmpty( $output );
	}

	/**
	 * Test only published dealers display
	 */
	public function test_only_published_dealers_display() {
		$draft_id = self::factory()->post->create( array(
			'post_type' => 'dealer',
			'post_title' => 'Draft Dealer',
			'post_status' => 'draft',
		) );
		
		$published_id = self::factory()->post->create( array(
			'post_type' => 'dealer',
			'post_title' => 'Published Dealer',
			'post_status' => 'publish',
		) );
		
		$output = do_shortcode( '[jblund_dealers]' );
		
		$this->assertStringContainsString( 'Published Dealer', $output );
		$this->assertStringNotContainsString( 'Draft Dealer', $output );
	}

	/**
	 * Test dealer data is properly escaped in output
	 */
	public function test_dealer_data_escaped_in_output() {
		$dealer_id = self::factory()->post->create( array(
			'post_type' => 'dealer',
			'post_title' => 'Escaped Dealer',
			'post_status' => 'publish',
		) );
		
		// Try to inject script
		update_post_meta( $dealer_id, '_dealer_company_address', '<script>alert("xss")</script>' );
		update_post_meta( $dealer_id, '_dealer_website', 'javascript:alert("xss")' );
		
		$output = do_shortcode( '[jblund_dealers]' );
		
		// Script tags should not be in output
		$this->assertStringNotContainsString( '<script>', $output );
		$this->assertStringNotContainsString( 'javascript:', $output );
	}

	/**
	 * Test dealer services display correctly
	 */
	public function test_dealer_services_display() {
		$dealer_id = self::factory()->post->create( array(
			'post_type' => 'dealer',
			'post_title' => 'Services Test Dealer',
			'post_status' => 'publish',
		) );
		
		update_post_meta( $dealer_id, '_dealer_docks', '1' );
		update_post_meta( $dealer_id, '_dealer_lifts', '0' );
		update_post_meta( $dealer_id, '_dealer_trailers', '1' );
		
		$output = do_shortcode( '[jblund_dealers]' );
		
		// Should show services
		$this->assertIsString( $output );
	}

	/**
	 * Test dealer custom link fields
	 */
	public function test_dealer_custom_map_link() {
		$dealer_id = self::factory()->post->create( array(
			'post_type' => 'dealer',
			'post_title' => 'Map Link Dealer',
		) );
		
		$custom_link = 'https://maps.google.com/?q=123+Main+St';
		update_post_meta( $dealer_id, '_dealer_custom_map_link', $custom_link );
		
		$saved_link = get_post_meta( $dealer_id, '_dealer_custom_map_link', true );
		$this->assertEquals( $custom_link, $saved_link );
	}

	/**
	 * Test latitude/longitude coordinates
	 */
	public function test_dealer_coordinates() {
		$dealer_id = self::factory()->post->create( array(
			'post_type' => 'dealer',
			'post_title' => 'Coordinate Dealer',
		) );
		
		$latitude = '45.123456';
		$longitude = '-93.654321';
		
		update_post_meta( $dealer_id, '_dealer_latitude', $latitude );
		update_post_meta( $dealer_id, '_dealer_longitude', $longitude );
		
		$saved_lat = get_post_meta( $dealer_id, '_dealer_latitude', true );
		$saved_lon = get_post_meta( $dealer_id, '_dealer_longitude', true );
		
		$this->assertEquals( $latitude, $saved_lat );
		$this->assertEquals( $longitude, $saved_lon );
	}
}
