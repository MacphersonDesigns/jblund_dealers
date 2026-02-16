# PHPUnit Testing Setup & Configuration

## Quick Start

### 1. Install WordPress Test Suite

**Using included script** (Linux/Mac):
```bash
cd /Users/macphersondesigns/Local\ Sites/plugin-testing/app/public/wp-content/plugins/jblund_dealers

# Download and install test environment
bash bin/install-wp-tests.sh wordpress_test_db wordpress_test_user password 127.0.0.1
```

**Using Docker** (Recommended):
```bash
docker-compose up -d
docker-compose exec wordpress bash
cd /var/www/html/wp-content/plugins/jblund_dealers
composer install --dev
phpunit
```

### 2. Install PHPUnit

```bash
# Via Composer
composer require --dev phpunit/phpunit "^9.0"

# Or globally
composer global require phpunit/phpunit "^9.0"
```

### 3. Run Tests

```bash
# Run all tests
phpunit

# Run specific test file
phpunit tests/test-security.php

# Run specific test class
phpunit tests/test-security.php --filter Security_Tests

# Run specific test method
phpunit tests/test-security.php --filter test_email_handler_no_extract

# With coverage report
phpunit --coverage-html coverage/

# Verbose output
phpunit -v
```

---

## Environment Setup

### WordPress Test Environment

Create a test WordPress instance separate from production:

```bash
# Using wp-cli
wp core download --path=/tmp/wordpress --version=latest

wp config create \
  --dbname=wordpress_test_db \
  --dbuser=wordpress_test_user \
  --dbpass=test_password \
  --dbhost=127.0.0.1

wp db create

wp core install \
  --url=http://wordpress-test.local \
  --title="JBLund Dealers Test" \
  --admin_user=admin \
  --admin_password=password \
  --admin_email=test@example.com
```

### Environment Variables

Create `.env` file in plugin root:

```bash
# .env
WP_TESTS_DIR=/tmp/wordpress-tests-lib
WP_DEBUG=1
WP_DEBUG_LOG=1
```

Or set directly:
```bash
export WP_TESTS_DIR=/tmp/wordpress-tests-lib
export WP_DEBUG=1
export WP_DEBUG_LOG=1
```

### Docker Setup (Recommended)

Create `docker-compose.yml`:

```yaml
version: '3'
services:
  mysql:
    image: mysql:5.7
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: wordpress_test_db
      MYSQL_USER: wordpress_test_user
      MYSQL_PASSWORD: test_password
    ports:
      - "3306:3306"

  wordpress:
    image: wordpress:latest
    depends_on:
      - mysql
    environment:
      WORDPRESS_DB_HOST: mysql:3306
      WORDPRESS_DB_NAME: wordpress_test_db
      WORDPRESS_DB_USER: wordpress_test_user
      WORDPRESS_DB_PASSWORD: test_password
      WORDPRESS_DEBUG: 1
    ports:
      - "8000:80"
    volumes:
      - ./:/var/www/html/wp-content/plugins/jblund_dealers
```

Run:
```bash
docker-compose up -d
docker-compose exec wordpress bash
```

---

## Test Structure

### Test Files

```
tests/
├── bootstrap.php                  # Test environment setup
├── test-security.php              # Security vulnerability tests
├── test-csv-import-export.php     # CSV functionality tests
└── test-dealer-functionality.php  # Dealer core tests
```

### Test Organization

Each test file contains related test cases in a `WP_UnitTestCase` class:

```php
class Security_Tests extends WP_UnitTestCase {
    
    public function test_email_handler_no_extract() {
        // Test implementation
    }
    
    public function test_uninstaller_safe_queries() {
        // Test implementation
    }
}
```

---

## Writing Tests

### Basic Test Template

```php
<?php
class Your_Tests extends WP_UnitTestCase {

    public function setUp() {
        parent::setUp();
        // Setup before each test
    }

    public function tearDown() {
        parent::tearDown();
        // Cleanup after each test
    }

    public function test_something() {
        // Arrange
        $dealer_id = self::factory()->post->create( array(
            'post_type' => 'dealer',
            'post_title' => 'Test Dealer',
        ) );

        // Act
        update_post_meta( $dealer_id, '_dealer_docks', '1' );

        // Assert
        $this->assertEquals( '1', get_post_meta( $dealer_id, '_dealer_docks', true ) );
    }
}
```

### Common Assertions

```php
// Equality
$this->assertEquals( $expected, $actual );
$this->assertNotEquals( $expected, $actual );

// Truth/Falsehood
$this->assertTrue( $condition );
$this->assertFalse( $condition );

// Existence
$this->assertEmpty( $value );
$this->assertNotEmpty( $value );
$this->assertNull( $value );
$this->assertNotNull( $value );

// Array/String
$this->assertArrayHasKey( $key, $array );
$this->assertStringContainsString( $needle, $haystack );
$this->assertStringNotContainsString( $needle, $haystack );

// Type
$this->assertIsArray( $value );
$this->assertIsString( $value );
$this->assertIsInt( $value );

// Exceptions
$this->expectException( Exception::class );
$this->expectExceptionMessage( 'Message text' );
```

### Test Factories

```php
// Create test data
$post_id = self::factory()->post->create( array(
    'post_type' => 'dealer',
    'post_title' => 'Test Dealer',
) );

$user_id = self::factory()->user->create( array(
    'role' => 'administrator',
) );

$term_id = self::factory()->term->create( array(
    'taxonomy' => 'category',
) );
```

---

## Running Specific Tests

### By Test Class
```bash
# Run all tests in Security_Tests class
phpunit tests/test-security.php --filter Security_Tests
```

### By Test Method
```bash
# Run single test
phpunit tests/test-security.php --filter test_email_handler_no_extract

# Run tests matching pattern
phpunit tests/ --filter "csv"  # Runs all CSV-related tests
```

### By File
```bash
# All security tests
phpunit tests/test-security.php

# All tests
phpunit tests/
```

### With Options
```bash
# Stop on first failure
phpunit --stop-on-failure

# Stop on first error
phpunit --stop-on-error

# Verbose
phpunit -v

# Very verbose
phpunit -vv

# Generate coverage report
phpunit --coverage-html coverage/

# Fail on risky tests
phpunit --strict
```

---

## Continuous Integration

### GitHub Actions Example

Create `.github/workflows/phpunit.yml`:

```yaml
name: PHPUnit Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    services:
      mysql:
        image: mysql:5.7
        env:
          MYSQL_DATABASE: wordpress_test
          MYSQL_USER: wordpress_test
          MYSQL_PASSWORD: test
          MYSQL_ROOT_PASSWORD: root
        options: >-
          --health-cmd "mysqladmin ping"
          --health-interval 10s
          --health-timeout 5s
          --health-retries 5
    
    steps:
    - uses: actions/checkout@v2
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: 7.4
        extensions: mysqli, mysqlnd
    
    - name: Install Composer Dependencies
      run: composer install --prefer-dist
    
    - name: Install WordPress Test Suite
      run: bash bin/install-wp-tests.sh wordpress_test wordpress_test test mysql 127.0.0.1
    
    - name: Run PHPUnit
      run: vendor/bin/phpunit
```

---

## Debugging Tests

### Enable WordPress Debug Logging

```php
// In tests/bootstrap.php or locally
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
```

Check log at: `wp-content/debug.log`

### Print Debug Information

```php
public function test_debug_example() {
    $dealer_id = self::factory()->post->create( array(
        'post_type' => 'dealer',
    ) );
    
    update_post_meta( $dealer_id, '_dealer_docks', '1' );
    
    // Print for debugging
    var_dump( get_post_meta( $dealer_id, '_dealer_docks', true ) );
    
    // Or use
    echo 'Dealer ID: ' . $dealer_id . "\n";
}
```

### Mark Incomplete Tests

```php
public function test_incomplete_feature() {
    $this->markTestIncomplete( 'This test needs to be implemented' );
}

public function test_skip_for_reason() {
    $this->markTestSkipped( 'MySQL not available in CI environment' );
}
```

---

## Test Coverage

### Generate Coverage Report

```bash
# HTML report
phpunit --coverage-html coverage/

# Text report
phpunit --coverage-text

# Clover XML (for CI)
phpunit --coverage-clover coverage.xml
```

### Coverage Goals

- **Minimum**: 50% coverage
- **Target**: 80% coverage
- **Excellent**: 90%+ coverage

Check generated report:
```bash
open coverage/index.html  # macOS
firefox coverage/index.html  # Linux
```

---

## Best Practices

### 1. Test Isolation
- Each test should be independent
- Use `setUp()` and `tearDown()` for setup/cleanup
- Don't rely on test execution order

### 2. Clear Names
```php
// Good
public function test_dealer_with_docks_shows_docks_badge() { }

// Bad
public function test_1() { }
```

### 3. Arrange-Act-Assert Pattern
```php
public function test_example() {
    // ARRANGE - setup test data
    $dealer_id = self::factory()->post->create( array(
        'post_type' => 'dealer',
    ) );
    
    // ACT - perform action
    update_post_meta( $dealer_id, '_dealer_docks', '1' );
    
    // ASSERT - verify result
    $this->assertEquals( '1', get_post_meta( $dealer_id, '_dealer_docks', true ) );
}
```

### 4. Single Assertion (when possible)
```php
// Better - focuses on one behavior
public function test_dealer_saves_docks_meta() {
    $dealer_id = self::factory()->post->create( array( 'post_type' => 'dealer' ) );
    update_post_meta( $dealer_id, '_dealer_docks', '1' );
    $this->assertEquals( '1', get_post_meta( $dealer_id, '_dealer_docks', true ) );
}

// Works, but tests multiple things
public function test_dealer_meta() {
    $dealer_id = self::factory()->post->create( array( 'post_type' => 'dealer' ) );
    update_post_meta( $dealer_id, '_dealer_docks', '1' );
    update_post_meta( $dealer_id, '_dealer_lifts', '0' );
    $this->assertEquals( '1', get_post_meta( $dealer_id, '_dealer_docks', true ) );
    $this->assertEquals( '0', get_post_meta( $dealer_id, '_dealer_lifts', true ) );
}
```

### 5. Mock External Dependencies
```php
public function test_with_mock() {
    // For API calls, external services, etc.
    $mock_response = json_encode( array( 'status' => 'success' ) );
    
    // Use mock instead of actual API call
    $this->assertEquals( 'success', json_decode( $mock_response, true )['status'] );
}
```

---

## Troubleshooting

### "WordPress test suite not found"
```bash
# Check environment variable
echo $WP_TESTS_DIR

# Should output /tmp/wordpress-tests-lib or similar

# If not set, install it:
bash bin/install-wp-tests.sh db_name db_user db_pass db_host
```

### "Headers already sent" errors
- Make sure `ob_start()` is not called before test output
- Check for output in bootstrap files

### "Database connection failed"
```bash
# Check MySQL is running
mysql -u root -p -e "SELECT 1;"

# Check credentials match phpunit.xml.dist
```

### "Class not found" errors
- Verify plugin is loaded in `bootstrap.php`
- Check autoload configuration in composer.json
- Ensure namespace matches file structure

---

## Next Steps

1. **Setup environment**: Follow "Quick Start" above
2. **Run test suite**: `phpunit`
3. **Review results**: Check which tests pass/fail
4. **Write new tests**: Add tests for your changes
5. **Check coverage**: `phpunit --coverage-html coverage/`
6. **Commit tests**: Keep tests in version control

---

**For questions**: See SECURITY_REFACTOR.md for migration guide
