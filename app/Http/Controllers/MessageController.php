<?php

namespace App\Http\Controllers;

use App\Models\Month;
use App\Models\MonthlySchedule;
use App\Models\User;
use App\Models\Year;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        $transformedData = [
            'monthly' => [],
            'harvest' => [],
        ];

        $breakpoints = [];

        $today = date("Y-m-d");
        $today = '2023-12-01';

        $firstOfNextMonth = date("Y-m-d", strtotime("first day of next month"));
        $firstbreakpoints = date("Y-m-d", strtotime("-7 day", strtotime($firstOfNextMonth)));
        $breakpoints[$firstbreakpoints] = 'next';

        $firstOfCurrentMonth = date("Y-m-01");
        $begin = new DateTime($firstOfCurrentMonth);
        $end = new DateTime(date("Y-m-t"));

        $interval = DateInterval::createFromDateString('7 day');
        $period = new DatePeriod($begin, $interval, $end);

        $i = 1;
        foreach ($period as $dt) {
            $breakpoints[$dt->format("Y-m-d")] = 'current';
            $i++;
            if ($i == 4) {
                break;
            }
        }

        $message = [];

        $selectedBreakpoint = null;
        foreach ($breakpoints as $key => $value) {
            if ($today == $key) {
                $selectedBreakpoint = [$key, $value];
            }
        }

        if ($selectedBreakpoint != null) {

            //send monthly reminder
            $year = null;
            $month = null;
            $schedules = null;

            if ($selectedBreakpoint[1] == 'next') {
                $year = Year::where(['name' => date('Y', strtotime($firstOfNextMonth))])->first();
                $month = Month::find(intval(date("m", strtotime($firstOfNextMonth))));
                $schedules = MonthlySchedule::where('year_id', $year->id)->where('month_id', $month->id)->get();
            } else {
                $year = Year::where(['name' => date('Y', strtotime($today))])->first();
                $month = Month::find(intval(date("m", strtotime($today))));
                $schedules = MonthlySchedule::where('year_id', $year->id)->where('month_id', $month->id)->get();
                $schedules = $schedules->filter(function ($q) {
                    return $q->harvestSchedule == null;
                });
            }

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
                    $prefixppl = '';
                    if ($selectedBreakpoint[1] == 'next') {
                        $prefixppl = "Selamat pagi, *Ubinan Fighters….!!!*🤩🤩🤩 \r\n\r\nBulan depan (*" . $month->name . "*) Anda punya *" . count($schedules) . " sampel ubinan*. Berikut daftar sampel ubinan bulan depan 💪💪💪 \r\n \r\n";
                    } else {
                        $prefixppl = "Selamat pagi, *Ubinan Fighters….!!!*🤩🤩🤩 \r\n\r\nMengingaatkan kembali, bulan ini (*" . $month->name . "*) Anda masih punya *" . count($schedules) . " sampel ubinan yang belum diisi tanggal perkiraan panennya*. Berikut daftar sampel ubinan tersebut 💪💪💪 \r\n \r\n";
                    }
                    $pplMessage = implode("\r\n", $samples);
                    $suffixppl = "\r\n\r\nMohon segera melakukan *input perkiraan tanggal panen* untuk sampel tersebut melalui link➡️ \r\n\r\n" . url("/jadwal-ubinan?month=" . $month->id) . " \r\n\r\n*Semangat Ubinan*, Huha…😸😸\r\n\r\nNb: Pesan 💚 Khusus untuk: *" . $ppl->name . "*";
                    $message[] = ["message" => $prefixppl . $pplMessage . $suffixppl, "phone_number" => "+62" . $ppl->phone_number];

                    $pplmap[] = ['name' => $ppl->name, 'total_sample' => count($schedules)];
                }
                $prefixpml = '';
                if ($selectedBreakpoint[1] == 'next') {
                    $prefixpml = "Selamat pagi, *PML Ubinan Fighters….!!!*🤩🤩🤩 \r\n\r\nBerikut adalah jumlah sampel ubinan bulan depan (*" . $month->name . "*) untuk PPL Anda 💪💪💪 \r\n \r\n";
                } else {
                    $prefixpml = "Selamat pagi, *PML Ubinan Fighters….!!!*🤩🤩🤩 \r\n\r\nBerikut adalah jumlah sampel ubinan bulan ini (*" . $month->name . "*) yang belum diinput tanggal perkiraan panennya oleh PPL 💪💪💪 \r\n \r\n";
                }
                $pmlMessage = '';
                foreach ($pplmap as $map) {
                    $pmlMessage = $pmlMessage . "🚩*" . $map['name'] . "* : *" . $map['total_sample'] . "* sampel" . "\r\n";
                }
                $suffixpml = "Mohon untuk mengingatkan kembali PPL untuk menginput perkiraan tanggal panen. Sampel selengkapnya dan perkiraan tanggal panen bisa diakses melalui link berikut➡️ \r\n\r\n" . url("/jadwal-ubinan?month=" . $month->id) . " \r\n\r\nNb: Pesan 💚 Khusus untuk: *" . $pml->name . "*";

                $message[] = ["message" => $prefixpml . $pmlMessage . $suffixpml, "phone_number" => "+62" . $pml->phone_number];
            }

            $admin = User::find(1);
            $prefixadmin = "Selamat pagi *Admin Survei Ubinan* 🤩🤩🤩\r\n\r\n";
            $adminMessage = '';
            if ($selectedBreakpoint[1] == 'next') {
                $adminMessage = "Hari ini telah dikirimkan Pengingat Bulanan untuk bulan *" . $month->name . "* kepada petugas Survei Ubinan. Secara keseluruhan ada total *" . $totalSample . " sampel*. Selanjutnya PPL akan mengisi jadwal panen untuk setiap sampel. Selengkapnya bisa dilihat di➡️ \r\n\r\n"
                    . url("/") . "\r\n\r\ndengan menggunakan akun admin";
            } else {
                $adminMessage = "Hari ini telah dikirimkan Pengingat Bulanan untuk bulan *" . $month->name . "* kepada petugas Survei Ubinan yang belum menginput tanggal perkiraan panen. Secara keseluruhan ada total *" . $totalSample . " sampel yang belum diinput tanggal perkiraan panennya*. Selanjutnya PPL akan mengisi jadwal panen untuk setiap sampel. Selengkapnya bisa dilihat di➡️ \r\n\r\n"
                    . url("/") . "\r\n\r\ndengan menggunakan akun admin";
            }

            $suffixadmin = "\r\n\r\n*Terima kasih...*💪💪💪";

            $message[] = ["message" => $prefixadmin . $adminMessage . $suffixadmin, "phone_number" => "+62" . $admin->phone_number];
        }

        $gapDay = ['+1' => 'Besok', '+3' => '3 Hari Lagi'];
        $mapGapDay = [];
        $year = Year::where(['name' => date('Y', strtotime($today))])->first();
        $month = Month::find(intval(date("m", strtotime($today))));
        $schedules = MonthlySchedule::where('year_id', $year->id)->where('month_id', $month->id)->get();
        foreach ($gapDay as $gap => $alias) {
            $mapGapDay[$gap] = ['date' => date("Y-m-d", strtotime("+" . $gap . " day", strtotime($today))), 'alias' => $alias];
        }

        foreach ($schedules as $schedule) {
            if ($schedule->harvestSchedule != null) {
                foreach ($mapGapDay as $key => $map) {
                    if ($map['date'] == $schedule->harvestSchedule->date) {
                        $transformedData['harvest'][$schedule->user->getPML->id][$schedule->user->id][$key][] = $schedule;
                    }
                }
            }
        }

        foreach ($transformedData['harvest'] as $pmlId => $pplschedules) {
            $pml = User::find($pmlId);
            $pplmap = [];
            foreach ($pplschedules as $pplId => $gap) {
                $ppl = User::find($pplId);
                $prefixppl = "Halo Ubinan Fighters BPS Kabupaten Probolinggo!!!\r\nSekedar mengingatkan kalau dalam waktu dekat ada panen untuk sampel ubinan berikut:\r\n\r\n";
                $suffixppl = "Pastikan tidak terlewat ya…., siapkan juga alat ubinannya mulai dari sekarang serta terakhir jangan lupa berdoa…\r\nSemangat, Fighting….\r\nBonus kata-kata mutiara:";
                $pplMessage = '';
                foreach ($gap as $gapkey => $transformedSchedules) {
                    $pplMessage = $pplMessage . strtoupper($gapDay[$gapkey]) . "\r\n";
                    $samples = [];
                    foreach ($transformedSchedules as $transformedSchedule) {
                        $samples[] = "🚩" . $transformedSchedule->bs->fullname() . " " . $transformedSchedule->name . " (*" . $transformedSchedule->commodity->name . "*) ";
                    }
                    $pplMessage = $pplMessage . implode("\r\n", $samples);
                    $pplMessage = $pplMessage . "\r\n\r\n";
                }

                $message[] = ["message" => $prefixppl . $pplMessage . $suffixppl, "phone_number" => "+62" . $ppl->phone_number];
                // $pplmap[] = ['name' => $ppl->name, 'total_sample' => count($schedules)];
            }
            // $prefixpml = '';
            // if ($selectedBreakpoint[1] == 'next') {
            //     $prefixpml = "Selamat pagi, *PML Ubinan Fighters….!!!*🤩🤩🤩 \r\n\r\nBerikut adalah jumlah sampel ubinan bulan depan (*" . $month->name . "*) untuk PPL Anda 💪💪💪 \r\n \r\n";
            // } else {
            //     $prefixpml = "Selamat pagi, *PML Ubinan Fighters….!!!*🤩🤩🤩 \r\n\r\nBerikut adalah jumlah sampel ubinan bulan ini (*" . $month->name . "*) yang belum diinput tanggal perkiraan panennya oleh PPL 💪💪💪 \r\n \r\n";
            // }
            // $pmlMessage = '';
            // foreach ($pplmap as $map) {
            //     $pmlMessage = $pmlMessage . "🚩*" . $map['name'] . "* : *" . $map['total_sample'] . "* sampel" . "\r\n";
            // }
            // $suffixpml = "Mohon untuk mengingatkan kembali PPL untuk menginput perkiraan tanggal panen. Sampel selengkapnya dan perkiraan tanggal panen bisa diakses melalui link berikut➡️ \r\n\r\n" . url("/jadwal-ubinan?month=" . $month->id) . " \r\n\r\nNb: Pesan 💚 Khusus untuk: *" . $pml->name . "*";

            // $message[] = ["message" => $prefixpml . $pmlMessage . $suffixpml, "phone_number" => "+62" . $pml->phone_number];
        }

        return $message;
    }
}

// Halo Ubinan Fighters BPS Kabupaten Probolinggo!!!
// Sekedar mengingatkan kalua besok/3hari lagi ada panen untuk sampel ubinan berikut:
// A
// B
// Pastikan kamu tidak terlewat ya…., siapkan juga alat ubinannya mulai dari sekarang serta terakhir jangan lupa berdoa…
// Semangat, Fighting….
// Bonus kata-kata mutiara:
