<?php

namespace Tests\Unit;

use App\Console\Commands\BackfillTanggalLahirCommand;
use PHPUnit\Framework\TestCase;

class BackfillTanggalLahirTest extends TestCase
{
    /**
     * Test parsing NIK laki-laki (contoh dari gambar: 330221 060574 0005 -> 06-05-1974).
     */
    public function test_parse_nik_laki_laki_success()
    {
        $nik = '3302210605740005';
        $result = BackfillTanggalLahirCommand::parseTanggalLahirFromNik($nik);

        $this->assertEquals('1974-05-06', $result);
    }

    /**
     * Test parsing NIK perempuan (contoh tanggal 46 -> 46 - 40 = 06).
     */
    public function test_parse_nik_perempuan_success()
    {
        $nik = '3302214605740005';
        $result = BackfillTanggalLahirCommand::parseTanggalLahirFromNik($nik);

        $this->assertEquals('1974-05-06', $result);
    }

    /**
     * Test parsing NIK kelahiran tahun 2000-an (contoh 05 -> 2005).
     */
    public function test_parse_nik_tahun_2000an()
    {
        $nik = '3201121511050002';
        $result = BackfillTanggalLahirCommand::parseTanggalLahirFromNik($nik);

        $this->assertEquals('2005-11-15', $result);
    }

    /**
     * Test NIK dengan spasi atau karakter non-digit.
     */
    public function test_parse_nik_with_formatting()
    {
        $nik = '330221 060574 0005';
        $result = BackfillTanggalLahirCommand::parseTanggalLahirFromNik($nik);

        $this->assertEquals('1974-05-06', $result);
    }

    /**
     * Test NIK tidak valid (kurang dari 16 digit atau tanggal tidak sah).
     */
    public function test_parse_nik_invalid()
    {
        // Kurang digit
        $this->assertNull(BackfillTanggalLahirCommand::parseTanggalLahirFromNik('123456'));

        // Tanggal 31 Februari (tidak ada di kalender)
        $this->assertNull(BackfillTanggalLahirCommand::parseTanggalLahirFromNik('3302213102740005'));
    }
}
