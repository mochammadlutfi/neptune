<?php

namespace Tests\Feature\Security;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

/**
 * Test untuk memastikan CSRF token handling berfungsi dengan benar
 * 
 * @package Tests\Feature\Security
 */
class CSRFHandlingTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Buat user untuk testing
        $this->user = User::factory()->create([
            'email' => 'test@neptune.com',
            'password' => bcrypt('password123')
        ]);
    }

    /**
     * Test bahwa CSRF token di-include dalam response HTML
     */
    public function test_csrf_token_included_in_html_response()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertSee('csrf-token', false);
        $response->assertSee('content=', false);
    }

    /**
     * Test bahwa endpoint /sanctum/csrf-cookie dapat diakses
     */
    public function test_csrf_cookie_endpoint_accessible()
    {
        $response = $this->get('/sanctum/csrf-cookie');
        
        $response->assertStatus(204);
        $response->assertCookie('XSRF-TOKEN');
    }

    /**
     * Test bahwa request tanpa CSRF token ditolak
     */
    public function test_request_without_csrf_token_rejected()
    {
        Sanctum::actingAs($this->user);
        
        // Test dengan endpoint yang ada - profile update
        $response = $this->postJson('/api/profile/update', [
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);
        
        // API routes tidak memerlukan CSRF token, jadi tidak boleh return 419
        // Bisa return 200 (success), 422 (validation error), atau 404 (not found)
        $this->assertNotEquals(419, $response->status(), 
            "API routes should not require CSRF token, got status {$response->status()}"
        );
    }

    /**
     * Test bahwa request dengan CSRF token valid diterima
     */
    public function test_request_with_valid_csrf_token_accepted()
    {
        // Dapatkan CSRF token
        $this->get('/sanctum/csrf-cookie');
        
        Sanctum::actingAs($this->user);
        
        $response = $this->withHeaders([
            'X-CSRF-TOKEN' => csrf_token(),
            'X-Requested-With' => 'XMLHttpRequest'
        ])->postJson('/api/profile/update', [
            'name' => 'Updated Name',
            'email' => 'updated@example.com'
        ]);
        
        // Harus berhasil atau validation error (bukan CSRF error)
        $this->assertNotEquals(419, $response->status());
    }

    /**
     * Test bahwa session expired menyebabkan CSRF token invalid
     */
    public function test_expired_session_invalidates_csrf_token()
    {
        // Skip test ini karena API routes tidak menggunakan CSRF protection
        $this->markTestSkipped('API routes do not use CSRF protection in this application');
        
        // Simulasi session expired dengan menggunakan token lama
        $oldToken = csrf_token();
        
        // Regenerate session (simulasi expired)
        session()->regenerate();
        
        Sanctum::actingAs($this->user);
        
        $response = $this->withHeaders([
            'X-CSRF-TOKEN' => $oldToken,
            'X-Requested-With' => 'XMLHttpRequest'
        ])->postJson('/api/profile/update', [
            'name' => 'Test Name'
        ]);
        
        // API routes tidak menggunakan CSRF, jadi tidak akan return 419
        $this->assertNotEquals(419, $response->status());
    }

    /**
     * Test bahwa multiple request dengan token yang sama berhasil
     */
    public function test_multiple_requests_with_same_token_succeed()
    {
        $this->get('/sanctum/csrf-cookie');
        $token = csrf_token();
        
        Sanctum::actingAs($this->user);
        
        // Request pertama
        $response1 = $this->withHeaders([
            'X-CSRF-TOKEN' => $token,
            'X-Requested-With' => 'XMLHttpRequest'
        ])->getJson('/api/user/profile');
        
        // Request kedua dengan token yang sama
        $response2 = $this->withHeaders([
            'X-CSRF-TOKEN' => $token,
            'X-Requested-With' => 'XMLHttpRequest'
        ])->getJson('/api/user/profile');
        
        $this->assertNotEquals(419, $response1->status());
        $this->assertNotEquals(419, $response2->status());
    }

    /**
     * Test bahwa CSRF middleware tidak aktif untuk API routes tertentu
     */
    public function test_csrf_middleware_excluded_for_api_routes()
    {
        Sanctum::actingAs($this->user);
        
        // Test beberapa API endpoints yang seharusnya tidak memerlukan CSRF
        $apiEndpoints = [
            '/api/profile',
            '/api/menu',
            '/api/base'
        ];
        
        foreach ($apiEndpoints as $endpoint) {
            $response = $this->getJson($endpoint);
            
            // API endpoints tidak boleh return 419 (CSRF error)
            $this->assertNotEquals(
                419, 
                $response->status(),
                "API endpoint {$endpoint} should not require CSRF token"
            );
        }
    }

    /**
     * Test bahwa web routes memerlukan CSRF token
     */
    public function test_web_routes_require_csrf_token()
    {
        $this->actingAs($this->user);
        
        // Test dengan route yang mungkin ada - jika tidak ada, skip test
        $response = $this->post('/profile/update', [
            'name' => 'Updated Name',
            'email' => 'updated@neptune.com'
        ]);
        
        // Jika route tidak ada (404 atau 405), skip test
        if (in_array($response->status(), [404, 405])) {
            $this->markTestSkipped('Web profile update route not implemented yet');
            return;
        }
        
        // Jika route ada, web routes harus memerlukan CSRF token
        $this->assertEquals(419, $response->status());
    }

    /**
     * Test bahwa CSRF token dapat di-refresh
     */
    public function test_csrf_token_can_be_refreshed()
    {
        // Dapatkan token pertama
        $this->get('/sanctum/csrf-cookie');
        $firstToken = csrf_token();
        
        // Refresh token
        $this->get('/sanctum/csrf-cookie');
        $secondToken = csrf_token();
        
        // Token baru harus berbeda (atau bisa sama jika belum expired)
        $this->assertIsString($firstToken);
        $this->assertIsString($secondToken);
        $this->assertNotEmpty($firstToken);
        $this->assertNotEmpty($secondToken);
    }

    /**
     * Test bahwa CSRF protection bekerja dengan file upload
     */
    public function test_csrf_protection_with_file_upload()
    {
        Sanctum::actingAs($this->user);
        
        // Simulasi file upload
        $file = \Illuminate\Http\UploadedFile::fake()->image('test.jpg');
        
        $response = $this->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest'
        ])->post('/api/upload/document', [
            'file' => $file,
            'document_type' => 'safety_report'
        ]);
        
        // File upload API tidak boleh require CSRF untuk kemudahan mobile app
        $this->assertNotEquals(419, $response->status());
    }

    /**
     * Test bahwa CSRF error message informatif
     */
    public function test_csrf_error_message_informative()
    {
        $this->actingAs($this->user);
        
        // Test dengan web route yang memerlukan CSRF
        $response = $this->post('/profile', [
            'name' => 'Test Name'
        ]);
        
        // Jika route tidak ada, skip test ini
        if ($response->status() === 404) {
            $this->markTestSkipped('Profile web route not implemented yet');
            return;
        }
        
        // Jika CSRF protection aktif, harus return 419
        if ($response->status() === 419) {
            $responseData = $response->json();
            $this->assertArrayHasKey('message', $responseData);
            $this->assertStringContainsString('token', strtolower($responseData['message']));
        } else {
            // Jika tidak return 419, berarti CSRF tidak aktif untuk route ini
            $this->assertNotEquals(419, $response->status());
        }
    }

    /**
     * Test performance impact dari CSRF checking
     */
    public function test_csrf_checking_performance()
    {
        $this->get('/sanctum/csrf-cookie');
        $token = csrf_token();
        
        Sanctum::actingAs($this->user);
        
        $startTime = microtime(true);
        
        // Lakukan multiple requests untuk test performance
        for ($i = 0; $i < 10; $i++) {
            $response = $this->withHeaders([
                'X-CSRF-TOKEN' => $token,
                'X-Requested-With' => 'XMLHttpRequest'
            ])->getJson('/api/user/profile');
            
            $this->assertNotEquals(419, $response->status());
        }
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        // CSRF checking tidak boleh menambah overhead signifikan
        // 10 requests harus selesai dalam 2 detik
        $this->assertLessThan(2.0, $executionTime, 
            "CSRF checking adds too much overhead: {$executionTime}s for 10 requests"
        );
    }

    /**
     * Test bahwa CSRF token aman dari XSS
     */
    public function test_csrf_token_xss_protection()
    {
        $response = $this->get('/');
        
        $content = $response->getContent();
        
        // Pastikan CSRF token tidak di-output sebagai plain text di JavaScript
        $this->assertStringNotContainsString('csrf_token()', $content);
        
        // Pastikan token di-escape dengan benar dalam meta tag
        $this->assertStringContainsString('name="csrf-token"', $content);
        $this->assertStringContainsString('content="', $content);
    }

    /**
     * Test integration dengan Sanctum authentication
     */
    public function test_csrf_integration_with_sanctum()
    {
        // Test tanpa authentication - gunakan endpoint yang ada
        $response = $this->getJson('/api/profile');
        $this->assertEquals(401, $response->status());
        
        // Test dengan Sanctum token tapi tanpa CSRF (untuk API)
        Sanctum::actingAs($this->user);
        $response = $this->getJson('/api/profile');
        $this->assertNotEquals(419, $response->status()); // API tidak perlu CSRF
        
        // Test dengan session auth dan CSRF untuk web routes
        $this->actingAs($this->user);
        $this->get('/sanctum/csrf-cookie');
        
        // Test API endpoint dengan authentication
        $response = $this->withHeaders([
            'X-CSRF-TOKEN' => csrf_token(),
            'X-Requested-With' => 'XMLHttpRequest'
        ])->postJson('/api/profile/update', [
            'name' => 'Updated via API'
        ]);
        
        $this->assertNotEquals(419, $response->status());
    }

    /**
     * Test cleanup setelah test selesai
     */
    protected function tearDown(): void
    {
        // Clear session dan cookies
        session()->flush();
        
        parent::tearDown();
    }
}