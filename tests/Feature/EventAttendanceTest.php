<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EventAttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected Event $event;

    protected User $superadmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superadmin = User::create([
            'name' => 'Bang Nahan',
            'email' => 'bangnahan@gmail.com',
            'password' => Hash::make('pekanbaru12'),
            'role' => 'superadmin',
        ]);

        $this->event = Event::create([
            'title' => 'Seminar Transformasi Digital 2026',
            'slug' => 'seminar-digital-2026',
            'description' => 'Seminar nasional kepemimpinan dan inovasi digital.',
            'location' => 'Hotel Grand Central Pekanbaru',
            'event_date' => now()->addDays(5)->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '16:00:00',
            'access_password' => Hash::make('rahasia123'),
            'is_active' => true,
            'allow_duplicate_submission' => false,
            'created_by' => $this->superadmin->id,
        ]);
    }

    public function test_can_render_attendance_form_for_active_event(): void
    {
        $response = $this->get(route('attendance.form', ['slug' => $this->event->slug]));

        $response->assertStatus(200);
        $response->assertSee('Seminar Transformasi Digital 2026');
        $response->assertSee('Instansi / Organisasi (Wajihah)');
        $response->assertSee('Jabatan / Peran (Amanah)');
    }

    public function test_participant_can_submit_attendance_and_gets_qr_code(): void
    {
        Http::fake([
            'api.mailketing.co.id/api/v2/send' => Http::response([
                'status' => 'success',
                'message' => 'Email queued',
                'message_id' => 'mailketing-v2-12345',
            ], 200),
        ]);

        $response = $this->post(route('attendance.store', ['slug' => $this->event->slug]), [
            'full_name' => 'Muhammad Rizky Pratama',
            'email' => 'rizky.pratama@gmail.com',
            'phone_number' => '081234567899',
            'institution' => 'Dinas Pendidikan Provinsi Riau',
            'position' => 'Kepala Seksi Kurikulum',
            'attendance_status' => 'hadir',
            'notes' => 'Akan hadir bersama 2 staf',
        ]);

        $attendance = Attendance::where('email', 'rizky.pratama@gmail.com')->first();
        $this->assertNotNull($attendance);
        $this->assertEquals('hadir', $attendance->attendance_status);
        $this->assertNotEmpty($attendance->qr_code_token);
        $this->assertTrue($attendance->email_sent);
        $this->assertEquals('mailketing-v2-12345', $attendance->mailketing_message_id);

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://api.mailketing.co.id/api/v2/send' &&
                   $request->hasHeader('X-Api-Token', '308b31d3313311776744479fa8fd7eb3') &&
                   $request['from_email'] === 'hi@jelatix.com' &&
                   $request['from_name'] === 'KULWIL RIAU' &&
                   $request['recipient'] === 'rizky.pratama@gmail.com';
        });

        $response->assertRedirect(route('attendance.success', [
            'slug' => $this->event->slug,
            'uuid' => $attendance->uuid,
        ]));

        // Check success page
        $successPage = $this->get(route('attendance.success', [
            'slug' => $this->event->slug,
            'uuid' => $attendance->uuid,
        ]));
        $successPage->assertStatus(200);
        $successPage->assertSee('Muhammad Rizky Pratama');
        $successPage->assertSee($attendance->qr_code_token);
    }

    public function test_participant_cannot_duplicate_attendance_when_disabled(): void
    {
        Attendance::create([
            'event_id' => $this->event->id,
            'full_name' => 'Budi Santoso',
            'email' => 'budi.santoso@gmail.com',
            'phone_number' => '081122334455',
            'institution' => 'Kementerian Agama',
            'position' => 'Analis Kepegawaian',
            'attendance_status' => 'hadir',
        ]);

        // Attempt second submission with same email
        $response = $this->post(route('attendance.store', ['slug' => $this->event->slug]), [
            'full_name' => 'Budi Santoso Duplicate',
            'email' => 'budi.santoso@gmail.com',
            'phone_number' => '089988776655',
            'institution' => 'Kementerian Agama',
            'position' => 'Analis',
            'attendance_status' => 'hadir',
        ]);

        // Should redirect to previous existing success ticket
        $this->assertEquals(1, Attendance::where('email', 'budi.santoso@gmail.com')->count());
        $response->assertSessionHas('info');
    }

    public function test_committee_can_authenticate_with_event_password(): void
    {
        $response = $this->post(route('event.checkin.auth', ['slug' => $this->event->slug]), [
            'password' => 'rahasia123',
        ]);

        $response->assertRedirect(route('event.checkin.panel', ['slug' => $this->event->slug]));
        $this->assertTrue(session()->has('event_passcode_'.$this->event->id));
    }

    public function test_committee_can_scan_qr_code_for_checkin(): void
    {
        $attendance = Attendance::create([
            'event_id' => $this->event->id,
            'full_name' => 'dr. Hendra Wijaya, Sp.PD',
            'email' => 'dr.hendra@rsud.go.id',
            'phone_number' => '081399887711',
            'institution' => 'RSUD Arifin Achmad',
            'position' => 'Dokter Spesialis',
            'attendance_status' => 'hadir',
            'qr_code_token' => 'EVT-TEST-123456',
        ]);

        // Authenticate session for committee
        $this->withSession(['event_passcode_'.$this->event->id => true]);

        // Scan QR Token
        $response = $this->postJson(route('event.checkin.scan', ['slug' => $this->event->slug]), [
            'token' => 'EVT-TEST-123456',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status' => 'checkin_success',
        ]);

        $attendance->refresh();
        $this->assertTrue($attendance->is_checked_in);
        $this->assertNotNull($attendance->checked_in_at);

        // Attempt scanning second time should trigger already_checked_in warning
        $secondScan = $this->postJson(route('event.checkin.scan', ['slug' => $this->event->slug]), [
            'token' => 'EVT-TEST-123456',
        ]);

        $secondScan->assertStatus(200);
        $secondScan->assertJson([
            'success' => false,
            'status' => 'already_checked_in',
        ]);
    }

    public function test_superadmin_can_login_with_provided_credentials_and_view_dashboard(): void
    {
        $response = $this->post(route('admin.login.submit'), [
            'email' => 'bangnahan@gmail.com',
            'password' => 'pekanbaru12',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->superadmin);

        $dashboard = $this->get(route('admin.dashboard'));
        $dashboard->assertStatus(200);
        $dashboard->assertSee('Dashboard Ringkasan');
        $dashboard->assertSee('Seminar Transformasi Digital 2026');
    }

    public function test_admin_routes_are_obfuscated_to_adminganteng_and_default_admin_returns_404(): void
    {
        // 1. Default /admin and /admin/login must return 404
        $this->get('/admin')->assertStatus(404);
        $this->get('/admin/login')->assertStatus(404);
        $this->get('/admin/events')->assertStatus(404);

        // 2. Obfuscated /adminganteng/login must return 200
        $this->get('/adminganteng/login')->assertStatus(200);

        // 3. Public attendance page must not reveal "Admin Login", "Panel Panitia", or "Rekap Data"
        $publicForm = $this->get(route('attendance.form', ['slug' => $this->event->slug]));
        $publicForm->assertStatus(200);
        $publicForm->assertDontSee('Admin Login');
        $publicForm->assertDontSee('Panel Panitia');
        $publicForm->assertDontSee('Rekap Data');
    }

    public function test_can_export_report_as_csv_xlsx_and_pdf(): void
    {
        // Session passcode verified
        $this->withSession(['event_passcode_'.$this->event->id => true]);

        // 1. Export CSV
        $csvResponse = $this->get(route('event.report.export', [
            'slug' => $this->event->slug,
            'format' => 'csv',
        ]));
        $csvResponse->assertStatus(200);
        $this->assertStringContainsString('text/csv', $csvResponse->headers->get('Content-Type'));

        // 2. Export XLSX
        $xlsxResponse = $this->get(route('event.report.export', [
            'slug' => $this->event->slug,
            'format' => 'xlsx',
        ]));
        $xlsxResponse->assertStatus(200);
        $this->assertStringContainsString('spreadsheetml', $xlsxResponse->headers->get('Content-Type'));

        // 3. Export PDF
        $pdfResponse = $this->get(route('event.report.export', [
            'slug' => $this->event->slug,
            'format' => 'pdf',
        ]));
        $pdfResponse->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $pdfResponse->headers->get('Content-Type'));
    }

    public function test_can_set_google_maps_url_and_render_navigation_link(): void
    {
        $this->actingAs($this->superadmin);

        // Update event with Google Maps URL
        $mapsUrl = 'https://maps.app.goo.gl/example123';
        $response = $this->put(route('admin.events.update', ['event' => $this->event->id]), [
            'title' => $this->event->title,
            'slug' => $this->event->slug,
            'location' => 'Hotel Grand Central Pekanbaru',
            'maps_url' => $mapsUrl,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.events.index'));
        $this->assertDatabaseHas('events', [
            'id' => $this->event->id,
            'maps_url' => $mapsUrl,
        ]);

        // Public attendance form should display the Google Maps link
        $formResponse = $this->get(route('attendance.form', ['slug' => $this->event->slug]));
        $formResponse->assertStatus(200);
        $formResponse->assertSee($mapsUrl, false);
        $formResponse->assertSee('Buka Google Maps');
    }
}
