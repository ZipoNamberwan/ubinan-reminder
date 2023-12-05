<?php

namespace App\Http\Controllers;

use App\Models\Month;
use App\Models\MonthlySchedule;
use App\Models\User;
use App\Models\Year;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        $transformedData = [
            'monthly' => [],
            'harvest' => [],
        ];

        // https://www.youtube.com/shorts/nndKHkNpiTA
        $today = date("Y-m-d");
        $today = '2023-12-25';

        $nextNDays = date("Y-m-d", strtotime("+7 day", strtotime($today)));
        $firstOfNextMonth = date("Y-m-d", strtotime("first day of next month"));

        $message = [];

        if ($nextNDays == $firstOfNextMonth) {
            //send monthly reminder
            $nextMonthYear = Year::where(['name' => date('Y', strtotime($firstOfNextMonth))])->first();
            $nextMonthMonth = Month::find(intval(date("m", strtotime($firstOfNextMonth))));
            $schedules = MonthlySchedule::where('year_id', $nextMonthYear->id)->where('month_id', $nextMonthMonth->id)->get();
            $totalSample = count($schedules);

            foreach ($schedules as $schedule) {
                $transformedData['monthly'][$schedule->user->getPML->id][$schedule->user->id][] = $schedule;
            }

            foreach ($transformedData['monthly'] as $pmlId => $pplschedules) {
                $pml = User::find($pmlId);
                $pplmap = [];
                foreach ($pplschedules as $pplId => $schedules) {
                    $samples = [];
                    foreach ($schedules as $schedule) {
                        $samples[] = "🚩" . $schedule->bs->fullname() . " " . $schedule->name . " (*" . $schedule->commodity->name . "*) ";
                    }
                    $ppl = User::find($pplId);
                    $prefix = "Selamat pagi, *Ubinan Fighters….!!!*🤩🤩🤩 \r\n\r\nBulan depan (*" . $nextMonthMonth->name . "*) Anda punya *" . count($schedules) . " sampel ubinan*. Berikut daftar sampel ubinan bulan depan 💪💪💪 \r\n \r\n";
                    $samplesString = implode("\r\n", $samples);
                    $suffix = "\r\n\r\nMohon segera melakukan *input perkiraan tanggal panen* untuk sampel tersebut melalui link➡️ \r\n\r\n" . url("/jadwal-ubinan?month=" . $nextMonthMonth->id) . " \r\n\r\n*Semangat Ubinan*, Huha…😸😸\r\n\r\nNb: Pesan 💚 Khusus untuk: *" . $ppl->name . "*";
                    $message[] = ["message" => $prefix . $samplesString . $suffix, "phone_number" => "+62" . $ppl->phone_number];

                    $pplmap[] = ['name' => $ppl->name, 'total_sample' => count($schedules)];
                }

                $prefix = "Selamat pagi, *PML Ubinan Fighters….!!!*🤩🤩🤩 \r\n\r\nBerikut adalah jumlah sampel ubinan bulan depan (*" . $nextMonthMonth->name . "*) untuk PPL Anda 💪💪💪 \r\n \r\n";
                $pmlsamplenumberstring = '';
                foreach ($pplmap as $map) {
                    $pmlsamplenumberstring = $pmlsamplenumberstring . "🚩*" . $map['name'] . "* : *" . $map['total_sample'] . "* sampel" . "\r\n";
                }
                $suffix = "Sampel selengkapnya dan perkiraan tanggal panen bisa diakses melalui link berikut➡️ \r\n\r\n" . url("/jadwal-ubinan?month=" . $nextMonthMonth->id) . " \r\n\r\nMari bersama-sama mengingatkan dan memonitor PPL tentang sampel ubinan bulan depan. *Semangat Ubinan*, Huha…😸😸\r\n\r\nNb: Pesan 💚 Khusus untuk: *" . $pml->name . "*";

                $message[] = ["message" => $prefix . $pmlsamplenumberstring . $suffix, "phone_number" => "+62" . $pml->phone_number];
            }

            $admin = User::find(1);
            $prefix = "Selamat pagi *Admin Survei Ubinan* 🤩🤩🤩\r\n\r\n";
            $adminMessage = "Hari ini telah dikirimkan Pengingat Bulanan untuk bulan *" . $nextMonthMonth->name . "* kepada petugas Survei Ubinan. Secara keseluruhan ada total *" . $totalSample . " sampel*. Selanjutnya PPL akan mengisi jadwal panen untuk setiap sampel. Selengkapnya bisa dilihat di➡️ \r\n\r\n"
                . url("/") . "\r\n\r\ndengan menggunakan akun admin";
            $suffix = "\r\n\r\n*Terima kasih...*💪💪💪";

            $message[] = ["message" => $prefix . $adminMessage . $suffix, "phone_number" => "+62" . $admin->phone_number];
        } else {
        }

        return $message;
    }
}


// Selamat pagi Admin Survei Ubinan
// Hari ini telah dikirimkan Pengingat Bulanan untuk bulan Januari kepada petugas Survei Ubinan. 
// Secara keseluruhan ada total 156 sampel. Selanjutnya PPL akan mengisi jadwal panen untuk setiap sampel.
// Selengkapnya bisa dilihat di www.com dengan menggunakan akun admin
// Terima kasih…
