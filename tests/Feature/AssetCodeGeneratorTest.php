<?php

namespace Tests\Feature;

use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Services\AssetCodeGenerator;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetCodeGeneratorTest extends TestCase
{
    use RefreshDatabase;

    private AssetCodeGenerator $generator;
    private AssetCategory $category;
    private Carbon $testDate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->generator = new AssetCodeGenerator();

        $this->category = AssetCategory::create([
            'name'         => 'Monitor',
            'abbreviation' => 'MON',
        ]);

        // Tetapkan tanggal spesifik untuk test yang deterministik
        $this->testDate = Carbon::create(2026, 3, 1);
    }

    /** @test */
    public function it_generates_correct_format(): void
    {
        $code = $this->generator->generate($this->category, $this->testDate);

        $this->assertEquals('ASTMON260301', $code);
    }

    /** @test */
    public function it_generates_sequential_codes_for_same_category_and_month(): void
    {
        $code1 = $this->generator->generate($this->category, $this->testDate);
        $this->assertEquals('ASTMON260301', $code1);

        // Simulasikan aset pertama sudah tersimpan
        Asset::create([
            'asset_code'        => $code1,
            'name'              => 'Monitor Test 1',
            'asset_category_id' => $this->category->id,
            'status'            => AssetStatus::Spare->value,
            'quantity'          => 1,
        ]);

        $code2 = $this->generator->generate($this->category, $this->testDate);
        $this->assertEquals('ASTMON260302', $code2);
    }

    /** @test */
    public function it_generates_different_codes_for_different_categories(): void
    {
        $laptopCategory = AssetCategory::create([
            'name'         => 'Laptop',
            'abbreviation' => 'LPT',
        ]);

        $monCode = $this->generator->generate($this->category, $this->testDate);
        $lptCode = $this->generator->generate($laptopCategory, $this->testDate);

        $this->assertStringStartsWith('ASTMON', $monCode);
        $this->assertStringStartsWith('ASTLPT', $lptCode);
    }

    /** @test */
    public function it_generates_different_codes_for_different_months(): void
    {
        $marchDate = Carbon::create(2026, 3, 1);
        $aprilDate = Carbon::create(2026, 4, 1);

        $marchCode = $this->generator->generate($this->category, $marchDate);
        $aprilCode = $this->generator->generate($this->category, $aprilDate);

        $this->assertStringContainsString('2603', $marchCode);
        $this->assertStringContainsString('2604', $aprilCode);
        $this->assertStringEndsWith('01', $marchCode);
        $this->assertStringEndsWith('01', $aprilCode);
    }

    /** @test */
    public function it_pads_short_abbreviations_to_3_characters(): void
    {
        $shortCategory = AssetCategory::create([
            'name'         => 'UPS',
            'abbreviation' => 'UP', // hanya 2 karakter
        ]);

        $code = $this->generator->generate($shortCategory, $this->testDate);

        // Harus menghasilkan "UPXXX" bukan "UPX" atau error
        $this->assertStringStartsWith('ASTUPX', $code);
    }

    /** @test */
    public function it_truncates_long_abbreviations_to_3_characters(): void
    {
        $longCategory = AssetCategory::create([
            'name'         => 'Server',
            'abbreviation' => 'SERVER', // lebih dari 3 karakter
        ]);

        $code = $this->generator->generate($longCategory, $this->testDate);

        // Hanya 3 karakter pertama yang dipakai: "SER"
        $this->assertStringStartsWith('ASTSER', $code);
    }

    /** @test */
    public function asset_code_is_auto_generated_via_observer(): void
    {
        // Bungkus dalam transaction seperti controller sebenarnya
        $asset = \Illuminate\Support\Facades\DB::transaction(function () {
            return Asset::create([
                'name'              => 'Monitor Test Observer',
                'asset_category_id' => $this->category->id,
                'status'            => AssetStatus::Spare->value,
                'quantity'          => 1,
            ]);
        });

        $this->assertNotNull($asset->asset_code);
        $this->assertStringStartsWith('ASTMON', $asset->asset_code);
        $this->assertMatchesRegularExpression('/^ASTMON\d{6,}$/', $asset->asset_code);
    }
}
