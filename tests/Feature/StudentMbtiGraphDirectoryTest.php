<?php

namespace Tests\Feature;

use App\Livewire\StudentDirectory;
use App\Models\MbtiType;
use App\Models\Student;
use App\Models\StudentMbtiProfile;
use Database\Seeders\AuthSeeder;
use Database\Seeders\MbtiTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StudentMbtiGraphDirectoryTest extends TestCase
{
    use RefreshDatabase;

    protected string $sessionCookie;

    protected function setUp(): void
    {
        parent::setUp();

        config(['angkatan.passcode' => 'secret-pass']);
        $this->seed([AuthSeeder::class, MbtiTypeSeeder::class]);

        $cookie = $this->postJson('/api/auth/passcode', ['passcode' => 'secret-pass'])
            ->getCookie('angkatan_session', false);

        $this->sessionCookie = $cookie->getValue();
    }

    public function test_mbti_import_command_handles_typos_troll_duplicates_and_excludes_screenshot_from_public_scope(): void
    {
        $disa = Student::factory()->create([
            'nim' => '21120126120016',
            'name' => 'Disa Jordan Mathew Tampubolon',
            'class_name' => 'A',
        ]);
        $ayudya = Student::factory()->create([
            'nim' => '21120126120015',
            'name' => 'Ayudya Wiyat Sitaresmi',
            'class_name' => 'B',
        ]);
        $rasya = Student::factory()->create([
            'nim' => '21120126140200',
            'name' => 'Rasya Putra Pratama',
            'class_name' => 'C',
        ]);
        $hersa = Student::factory()->create([
            'nim' => '21120126140177',
            'name' => 'Hersa Prastyo',
            'class_name' => 'D',
        ]);

        $csvContent = implode("\n", [
            'Nama,NIM,Kelas,MBTI,E,I,N,S,T,F,J,P,Screenshot',
            'kak mau gak,26969696969,Kelas A,INTP,31,69,88,12,51,49,23,77,https://drive.google.com/open?id=troll',
            'Ayudya Wiyat Sitaresmi,21120126120016,Kelas B,INFJ,30,70,65,35,40,60,72,28,https://drive.google.com/open?id=ayudya-secret',
            'Rasya Putra Pratama,2112012614200,Kelas C,ENTJ,76,24,61,39,80,20,75,25,https://drive.google.com/open?id=rasya-secret',
            'Hersa Prastyo,21120126140177,Kelas C,ISTJ,20,80,30,70,75,25,85,15,https://drive.google.com/open?id=hersa-wrong-class',
            'Hersa Prastyo,21120126140177,Kelas D,ENTJ,68,32,64,36,78,22,74,26,https://drive.google.com/open?id=hersa-right-class',
        ]);

        $tempCsv = tempnam(sys_get_temp_dir(), 'mbti_test_');
        file_put_contents($tempCsv, $csvContent);

        try {
            $this->artisan('mbti:import', ['--path' => $tempCsv])->assertExitCode(0);
        } finally {
            @unlink($tempCsv);
        }

        // Disa keeps null profile; Ayudya & Rasya received remapped profiles.
        $this->assertNull($disa->fresh()->mbtiProfile);
        $this->assertSame('INFJ', $ayudya->fresh()->mbtiProfile->mbtiType->code);
        $this->assertSame('ENTJ', $rasya->fresh()->mbtiProfile->mbtiType->code);

        // Duplicate Hersa kept the Kelas D row matching DB class_name.
        $this->assertSame('ENTJ', $hersa->fresh()->mbtiProfile->mbtiType->code);
        $this->assertSame(3, StudentMbtiProfile::query()->count());

        // Public scope, directory HTML, and detail HTML must NEVER expose screenshot_url.
        $publicAyudya = Student::query()->public()->where('nim', '21120126120015')->firstOrFail();
        $this->assertNull($publicAyudya->mbtiProfile->screenshot_url);

        $directoryResponse = $this->call('GET', '/directory', [], ['angkatan_session' => $this->sessionCookie]);
        $directoryResponse->assertOk();
        $directoryResponse->assertDontSee('ayudya-secret');
        $directoryResponse->assertSee('INFJ');

        $detailResponse = $this->call('GET', '/students/21120126120015', [], ['angkatan_session' => $this->sessionCookie]);
        $detailResponse->assertOk();
        $detailResponse->assertDontSee('ayudya-secret');
        $detailResponse->assertSee('student-mbti-card');
        $detailResponse->assertSee('INFJ');
        $detailResponse->assertSee('Advocate');
        $detailResponse->assertSee('Diplomats');
        $detailResponse->assertSee('70%');
    }

    public function test_colony_graph_eight_aspect_filter_switches_opposing_poles_intersects_axes_and_toggles_off(): void
    {
        $intjType = MbtiType::query()->where('code', 'INTJ')->firstOrFail();
        $entjType = MbtiType::query()->where('code', 'ENTJ')->firstOrFail();
        $isfpType = MbtiType::query()->where('code', 'ISFP')->firstOrFail();

        $sIntj = Student::factory()->create([
            'nim' => '21120126120001',
            'name' => 'Alpha Intj',
            'nickname' => 'Alpha',
            'class_name' => 'A',
        ]);
        $sEntj = Student::factory()->create([
            'nim' => '21120126120002',
            'name' => 'Beta Entj',
            'nickname' => 'Beta',
            'class_name' => 'B',
        ]);
        $sIsfp = Student::factory()->create([
            'nim' => '21120126120003',
            'name' => 'Gamma Isfp',
            'nickname' => 'Gamma',
            'class_name' => 'C',
        ]);
        $sUnmapped = Student::factory()->create([
            'nim' => '21120126120004',
            'name' => 'Delta Unmapped',
            'nickname' => 'Delta',
            'class_name' => 'D',
        ]);

        StudentMbtiProfile::query()->create([
            'student_id' => $sIntj->id,
            'mbti_type_id' => $intjType->id,
            'pct_e' => 25, 'pct_i' => 75,
            'pct_n' => 80, 'pct_s' => 20,
            'pct_t' => 85, 'pct_f' => 15,
            'pct_j' => 70, 'pct_p' => 30,
        ]);
        StudentMbtiProfile::query()->create([
            'student_id' => $sEntj->id,
            'mbti_type_id' => $entjType->id,
            'pct_e' => 78, 'pct_i' => 22,
            'pct_n' => 70, 'pct_s' => 30,
            'pct_t' => 82, 'pct_f' => 18,
            'pct_j' => 74, 'pct_p' => 26,
        ]);
        StudentMbtiProfile::query()->create([
            'student_id' => $sIsfp->id,
            'mbti_type_id' => $isfpType->id,
            'pct_e' => 20, 'pct_i' => 80,
            'pct_n' => 35, 'pct_s' => 65,
            'pct_t' => 30, 'pct_f' => 70,
            'pct_j' => 28, 'pct_p' => 72,
        ]);

        $component = Livewire::test(StudentDirectory::class)
            // Idle: all 4 students in directory card list including unmapped.
            ->assertViewHas('students', fn ($students): bool => $students->pluck('name')->sort()->values()->all() === [
                'Alpha Intj',
                'Beta Entj',
                'Delta Unmapped',
                'Gamma Isfp',
            ])
            // Activate I: INTJ & ISFP in filtered cards; ENTJ & unmapped excluded.
            ->call('toggleAspect', 'I')
            ->assertSet('aspects', 'I')
            ->assertViewHas('students', fn ($students): bool => $students->pluck('name')->sort()->values()->all() === [
                'Alpha Intj',
                'Gamma Isfp',
            ])
            // Cross-axis AND with N: only INTJ stays in filtered cards.
            ->call('toggleAspect', 'N')
            ->assertSet('aspects', 'I,N')
            ->assertViewHas('students', fn ($students): bool => $students->pluck('name')->values()->all() === [
                'Alpha Intj',
            ])
            // Opposing pole click (E while I is active): switches I -> E while keeping N => ENTJ!
            ->call('toggleAspect', 'E')
            ->assertSet('aspects', 'E,N')
            ->assertViewHas('students', fn ($students): bool => $students->pluck('name')->values()->all() === [
                'Beta Entj',
            ])
            // Click active poles to toggle them back off => all 4 return.
            ->call('toggleAspect', 'E')
            ->call('toggleAspect', 'N')
            ->assertSet('aspects', '')
            ->assertViewHas('students', fn ($students): bool => $students->count() === 4);

        // Also ensure generated markup stays free of Tailwind arbitrary hex values.
        $this->assertDoesNotMatchRegularExpression(
            '/(?:bg|text|border|ring|fill|stroke)-\[#[0-9a-fA-F]+\]/',
            $component->html(),
        );
    }

    public function test_student_detail_renders_without_mbti_card_when_unmapped_and_with_spectrum_bars_when_mapped(): void
    {
        $unmapped = Student::factory()->create([
            'nim' => '21120126120091',
            'name' => 'Tanpa Mbti',
            'nickname' => 'Tanpa',
            'class_name' => 'A',
        ]);

        $resUnmapped = $this->call('GET', "/students/{$unmapped->nim}", [], ['angkatan_session' => $this->sessionCookie]);
        $resUnmapped->assertOk();
        $resUnmapped->assertDontSee('id="student-mbti-card"', false);

        $intpType = MbtiType::query()->where('code', 'INTP')->firstOrFail();
        $mapped = Student::factory()->create([
            'nim' => '21120126120092',
            'name' => 'Dengan Mbti',
            'nickname' => 'Dengan',
            'class_name' => 'A',
        ]);
        StudentMbtiProfile::query()->create([
            'student_id' => $mapped->id,
            'mbti_type_id' => $intpType->id,
            'pct_e' => 32, 'pct_i' => 68,
            'pct_n' => 86, 'pct_s' => 14,
            'pct_t' => 74, 'pct_f' => 26,
            'pct_j' => 42, 'pct_p' => 58,
        ]);

        $resMapped = $this->call('GET', "/students/{$mapped->nim}", [], ['angkatan_session' => $this->sessionCookie]);
        $resMapped->assertOk();
        $resMapped->assertSee('id="student-mbti-card"', false);
        $resMapped->assertSee('INTP');
        $resMapped->assertSee('Logician');
        $resMapped->assertSee('Analysts');
        $resMapped->assertSee('68%');
        $resMapped->assertSee('86%');
        $resMapped->assertSee('74%');
        $resMapped->assertSee('58%');
        $this->assertDoesNotMatchRegularExpression('/(?:bg|text|border|ring|fill|stroke)-\[#[0-9a-fA-F]+\]/', $resMapped->getContent());
    }

    public function test_graph_route_requires_passcode_and_renders_obsidian_workspace_without_sensitive_fields(): void
    {
        // Guest must be redirected to passcode gate.
        $this->get('/graph')->assertRedirect('/unlock');

        $entjType = MbtiType::query()->where('code', 'ENTJ')->firstOrFail();
        $mapped = Student::factory()->create([
            'nim' => '21120126120077',
            'name' => 'Zaky Commander',
            'nickname' => 'Zaky',
            'class_name' => 'B',
        ]);
        StudentMbtiProfile::query()->create([
            'student_id' => $mapped->id,
            'mbti_type_id' => $entjType->id,
            'pct_e' => 75, 'pct_i' => 25,
            'pct_n' => 70, 'pct_s' => 30,
            'pct_t' => 85, 'pct_f' => 15,
            'pct_j' => 80, 'pct_p' => 20,
            'screenshot_url' => 'https://drive.google.com/open?id=top-secret-screenshot',
        ]);
        Student::factory()->create([
            'nim' => '21120126120078',
            'name' => 'Raka Belum Isi',
            'nickname' => 'Raka',
            'class_name' => 'C',
        ]);

        // Directory shows compact Filter MBTI toggle + CTA link to /graph without locking document scroll.
        $dirResponse = $this->call('GET', '/directory', [], ['angkatan_session' => $this->sessionCookie]);
        $dirResponse->assertOk();
        $dirResponse->assertSee('Filter MBTI');
        $dirResponse->assertSee('Buka Graph View');
        $dirResponse->assertSee(route('graph.index'), false);
        $dirResponse->assertDontSee('h-dvh overflow-hidden overscroll-none');

        // /graph renders 200 OK, locks viewport height without scroll, pre-hydrates aspects, excludes screenshot_url, and has no arbitrary HEX.
        $graphResponse = $this->call('GET', '/graph', ['aspects' => 'E,N'], [
            'angkatan_session' => $this->sessionCookie,
        ]);
        $graphResponse->assertOk();
        $graphResponse->assertSee('h-dvh overflow-hidden overscroll-none');
        $graphResponse->assertSee('Konstelasi MBTI Tekkom 26');
        $graphResponse->assertSee('Graph Controls');
        $graphResponse->assertSee('Buka Profil Lengkap');
        $graphResponse->assertSee('Zaky Commander');
        $graphResponse->assertDontSee('top-secret-screenshot');
        $this->assertSame(['E', 'N'], $graphResponse->viewData('graphPayload')['activeAspects']);
        $this->assertSame(1, $graphResponse->viewData('graphPayload')['center']['mappedCount']);
        $this->assertSame(1, $graphResponse->viewData('graphPayload')['center']['unmappedCount']);
        $this->assertDoesNotMatchRegularExpression(
            '/(?:bg|text|border|ring|fill|stroke)-\[#[0-9a-fA-F]+\]/',
            $graphResponse->getContent(),
        );
    }

    public function test_directory_supports_dynamic_pagination_with_clamped_min_max_bounds(): void
    {
        Student::factory()
            ->count(65)
            ->sequence(fn ($sequence): array => [
                'nim' => '2112012612'.str_pad((string) ($sequence->index + 100), 4, '0', STR_PAD_LEFT),
            ])
            ->create();

        // 1. Default per_page = 15
        $defaultResponse = $this->call('GET', '/directory', [], ['angkatan_session' => $this->sessionCookie]);
        $defaultResponse->assertOk();
        $this->assertSame(15, $defaultResponse->viewData('students')->perPage());
        $this->assertSame(15, $defaultResponse->viewData('students')->count());

        // 2. Custom per_page = 24
        $customResponse = $this->call('GET', '/directory', ['per_page' => 24], ['angkatan_session' => $this->sessionCookie]);
        $customResponse->assertOk();
        $this->assertSame(24, $customResponse->viewData('students')->perPage());
        $this->assertSame(24, $customResponse->viewData('students')->count());

        // 3. Below minimum (per_page = 2) clamps up to 6
        $minResponse = $this->call('GET', '/directory', ['per_page' => 2], ['angkatan_session' => $this->sessionCookie]);
        $minResponse->assertOk();
        $this->assertSame(6, $minResponse->viewData('students')->perPage());
        $this->assertSame(6, $minResponse->viewData('students')->count());

        // 4. Above maximum (per_page = 999) clamps down to 60
        $maxResponse = $this->call('GET', '/directory', ['per_page' => 999], ['angkatan_session' => $this->sessionCookie]);
        $maxResponse->assertOk();
        $this->assertSame(60, $maxResponse->viewData('students')->perPage());
        $this->assertSame(60, $maxResponse->viewData('students')->count());

        // 5. Livewire setPerPage resets back to page 1 and enforces clamping + keeps per_page control visible on 1-page results
        $component = Livewire::test(StudentDirectory::class)
            ->call('nextPage')
            ->assertViewHas('students', fn ($students): bool => $students->currentPage() === 2)
            ->call('setPerPage', 60)
            ->assertSet('perPage', 60)
            ->assertViewHas('students', fn ($students): bool => $students->currentPage() === 1 && $students->count() === 60)
            ->assertSee('directory-per-page-input')
            ->call('setPerPage', 1)
            ->assertSet('perPage', 6)
            ->call('setPerPage', 500)
            ->assertSet('perPage', 60)
            ->call('setPerPage', 20)
            ->assertSet('perPage', 20);

        $this->assertDoesNotMatchRegularExpression(
            '/(?:bg|text|border|ring|fill|stroke)-\[#[0-9a-fA-F]+\]/',
            $component->html(),
        );
    }

    public function test_directory_orders_students_by_4bit_hex_mbti_index_and_syncs_observer(): void
    {
        $entj = MbtiType::query()->where('code', 'ENTJ')->firstOrFail(); // 0x0 (0)
        $intj = MbtiType::query()->where('code', 'INTJ')->firstOrFail(); // 0x8 (8)
        $isfp = MbtiType::query()->where('code', 'ISFP')->firstOrFail(); // 0xF (15)

        $sNull = Student::factory()->create(['nim' => '21120126120101', 'name' => 'Aaaa Unmapped', 'class_name' => 'A']);
        $sIsfp = Student::factory()->create(['nim' => '21120126120102', 'name' => 'Bbbb Isfp', 'class_name' => 'A']);
        $sIntj = Student::factory()->create(['nim' => '21120126120103', 'name' => 'Cccc Intj', 'class_name' => 'B']);
        $sEntjB = Student::factory()->create(['nim' => '21120126120104', 'name' => 'Zara Entj', 'class_name' => 'A']);
        $sEntjA = Student::factory()->create(['nim' => '21120126120105', 'name' => 'Aldi Entj', 'class_name' => 'A']);

        $profileIsfp = StudentMbtiProfile::query()->create([
            'student_id' => $sIsfp->id, 'mbti_type_id' => $isfp->id,
            'pct_e' => 20, 'pct_i' => 80, 'pct_n' => 30, 'pct_s' => 70, 'pct_t' => 40, 'pct_f' => 60, 'pct_j' => 25, 'pct_p' => 75,
        ]);
        StudentMbtiProfile::query()->create([
            'student_id' => $sIntj->id, 'mbti_type_id' => $intj->id,
            'pct_e' => 20, 'pct_i' => 80, 'pct_n' => 70, 'pct_s' => 30, 'pct_t' => 80, 'pct_f' => 20, 'pct_j' => 75, 'pct_p' => 25,
        ]);
        StudentMbtiProfile::query()->create([
            'student_id' => $sEntjB->id, 'mbti_type_id' => $entj->id,
            'pct_e' => 70, 'pct_i' => 30, 'pct_n' => 75, 'pct_s' => 25, 'pct_t' => 80, 'pct_f' => 20, 'pct_j' => 75, 'pct_p' => 25,
        ]);
        StudentMbtiProfile::query()->create([
            'student_id' => $sEntjA->id, 'mbti_type_id' => $entj->id,
            'pct_e' => 70, 'pct_i' => 30, 'pct_n' => 75, 'pct_s' => 25, 'pct_t' => 80, 'pct_f' => 20, 'pct_j' => 75, 'pct_p' => 25,
        ]);

        // Verify observer synced mbti_hex_index onto students table
        $this->assertNull($sNull->fresh()->mbti_hex_index);
        $this->assertSame(0, $sEntjA->fresh()->mbti_hex_index);
        $this->assertSame(8, $sIntj->fresh()->mbti_hex_index);
        $this->assertSame(15, $sIsfp->fresh()->mbti_hex_index);

        $response = $this->call('GET', '/directory', [], ['angkatan_session' => $this->sessionCookie]);
        $response->assertOk();
        $response->assertSee('0x0 · ENTJ');
        $response->assertSee('0x8 · INTJ');
        $response->assertSee('0xF · ISFP');

        $orderedNames = collect($response->viewData('students')->items())->pluck('name')->all();
        $this->assertSame([
            'Aldi Entj',     // 0x0 Kelas A (A before Z)
            'Zara Entj',     // 0x0 Kelas A
            'Cccc Intj',     // 0x8
            'Bbbb Isfp',     // 0xF
            'Aaaa Unmapped', // NULL always last even though name starts with A
        ], $orderedNames);

        // Updating and deleting profile stays synchronized on students.mbti_hex_index
        $profileIsfp->update(['mbti_type_id' => $entj->id]);
        $this->assertSame(0, $sIsfp->fresh()->mbti_hex_index);

        $profileIsfp->delete();
        $this->assertNull($sIsfp->fresh()->mbti_hex_index);
    }
}
