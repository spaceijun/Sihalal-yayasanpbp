<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataBank;

class DataBankSeeder extends Seeder
{
    public function run(): void
    {
        $banks = [
            // BANK BUMN
            ["name" => "BANK BRI", "code" => "002"],
            ["name" => "BANK MANDIRI", "code" => "008"],
            ["name" => "BANK BNI", "code" => "009"],
            ["name" => "BANK TABUNGAN NEGARA (BTN)", "code" => "200"],

            // BANK SWASTA BESAR
            ["name" => "BANK BCA", "code" => "014"],
            ["name" => "BANK PERMATA", "code" => "013"],
            ["name" => "BANK CIMB NIAGA", "code" => "022"],
            ["name" => "BANK DANAMON", "code" => "011"],
            ["name" => "BANK OCBC NISP", "code" => "028"],
            ["name" => "BANK MAYBANK INDONESIA", "code" => "016"],
            ["name" => "BANK PANIN", "code" => "019"],
            ["name" => "BANK MEGA", "code" => "426"],
            ["name" => "BANK SINARMAS", "code" => "153"],
            ["name" => "BANK BTPN", "code" => "213"],
            ["name" => "BANK DBS INDONESIA", "code" => "046"],
            ["name" => "BANK UOB INDONESIA", "code" => "023"],
            ["name" => "BANK HSBC INDONESIA", "code" => "087"],

            // BANK SYARIAH
            ["name" => "BANK SYARIAH INDONESIA (BSI)", "code" => "451"],
            ["name" => "BANK MUAMALAT", "code" => "147"],
            ["name" => "BANK BCA SYARIAH", "code" => "536"],

            // DIGITAL BANK
            ["name" => "BANK JAGO", "code" => "542"],
            ["name" => "BANK NEO COMMERCE", "code" => "490"],
            ["name" => "SEABANK", "code" => "535"],

            // BANK DAERAH JAWA & NASIONAL
            ["name" => "BANK BJB", "code" => "110"],
            ["name" => "BANK DKI", "code" => "111"],
            ["name" => "BANK JATENG", "code" => "113"],
            ["name" => "BANK JATIM", "code" => "114"],

            // BANK DAERAH LAIN
            ["name" => "BPD DIY", "code" => "112"],
            ["name" => "BPD JAMBI", "code" => "115"],
            ["name" => "BPD ACEH", "code" => "116"],
            ["name" => "BANK SUMUT", "code" => "117"],
            ["name" => "BANK NAGARI", "code" => "118"],
            ["name" => "BANK RIAU KEPRI", "code" => "119"],
            ["name" => "BANK SUMSEL BABEL", "code" => "120"],
            ["name" => "BANK LAMPUNG", "code" => "121"],
            ["name" => "BPD KALSEL", "code" => "122"],
            ["name" => "BPD KALIMANTAN BARAT", "code" => "123"],
            ["name" => "BPD KALTIMTARA", "code" => "124"],
            ["name" => "BPD KALTENG", "code" => "125"],
            ["name" => "BPD SULSEL BAR", "code" => "126"],
            ["name" => "BANK SULUTGO", "code" => "127"],
            ["name" => "BPD NTB SYARIAH", "code" => "128"],
            ["name" => "BPD BALI", "code" => "129"],
            ["name" => "BANK NTT", "code" => "130"],
            ["name" => "BANK MALUKU MALUT", "code" => "131"],
            ["name" => "BPD PAPUA", "code" => "132"],
            ["name" => "BANK BENGKULU", "code" => "133"],
            ["name" => "BPD SULAWESI TENGAH", "code" => "134"],
            ["name" => "BANK SULTRA", "code" => "135"],

            // LAINNYA
            ["name" => "BANK ARTA NIAGA KENCANA", "code" => "020"],
            ["name" => "BANK NUSANTARA PARAHYANGAN", "code" => "145"],
            ["name" => "BANK GANESHA", "code" => "161"],
        ];

        foreach ($banks as $bank) {
            DataBank::updateOrCreate(
                ['code' => $bank['code']],
                ['name' => $bank['name']]
            );
        }
    }
}
