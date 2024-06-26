<?php

namespace App\Http\Controllers;

use App\Models\HarvestSchedule;
use App\Models\Month;
use App\Models\MonthlySchedule;
use App\Models\User;
use App\Models\Year;
use App\Models\Quote;
use DateInterval;
use DatePeriod;
use DateTime;

class MessageController extends Controller
{
    public function index()
    {
        $randQuoteNumMax = count(Quote::all());

        $transformedData = [
            'monthly' => [],
            'harvest' => [],
        ];

        $breakpoints = [];

        $today = date("Y-m-d", strtotime('+7 hours'));
        // $today = '2024-03-08';

        $firstOfNextMonth = (new DateTime($today))->modify('+7 hours first day of next month')->format('Y-m-d');

        $firstbreakpoints = (new DateTime($today))->modify('first day of next month')->modify('+7 hours -1 week')->format('Y-m-d');
        $breakpoints[$firstbreakpoints] = 'next';

        $begin = (new DateTime($today))->modify('+7 hours first day of this month');
        $end = (new DateTime($today))->modify('+7 hours last day of this month');

        $interval = DateInterval::createFromDateString('7 day');
        $period = new DatePeriod($begin, $interval, $end);

        // breakpoint mulai tanggal 1 setiap seminggu sekali
        $i = 1;
        foreach ($period as $dt) {
            $breakpoints[$dt->format("Y-m-d")] = 'current';
            $i++;
            if ($i == 4) {
                break;
            }
        }

        // tambahan breakpoint tanggal 20
        $breakpoints[(new DateTime($today))->setDate((new DateTime($today))->format('Y'), (new DateTime($today))->format('m'), 20)->format('Y-m-d')] = 'current';

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
                    $scheduleIds = [];
                    foreach ($schedules as $schedule) {
                        if ($schedule->commodity->id == 1) {
                            $samples[] = "🚩" . $schedule->bs->fullnamesegment() . " " . $schedule->bs->fullcodesegment() . sprintf('%02d', $schedule->segment) . $schedule->subSegment->code . " (*" . $schedule->commodity->name . "*) ";
                        } else {
                            $samples[] = "🚩" . $schedule->bs->fullname() . " " . $schedule->name . " (*" . $schedule->commodity->name . "*) ";
                        }
                        $scheduleIds[] = $schedule->id;
                    }
                    $ppl = User::find($pplId);
                    $prefixppl = '';
                    if ($selectedBreakpoint[1] == 'next') {
                        $prefixppl = "Halo, *Ubinan Fighters….!!!*🤩🤩🤩 \r\n\r\nBulan depan (*" . $month->name . "*) Anda punya *" . count($schedules) . " sampel ubinan*. Berikut daftar sampel ubinan bulan depan 💪💪💪 \r\n \r\n";
                    } else {
                        $prefixppl = "Halo, *Ubinan Fighters….!!!*🤩🤩🤩 \r\n\r\nMengingaatkan kembali, bulan ini (*" . $month->name . "*) Anda masih punya *" . count($schedules) . " sampel ubinan yang belum diisi tanggal perkiraan panennya*. Berikut daftar sampel ubinan tersebut 💪💪💪 \r\n \r\n";
                    }
                    $pplMessage = implode("\r\n", $samples);
                    $rand = rand(1, $randQuoteNumMax);
                    $quote = Quote::find($rand)->quote;
                    $suffixppl = "\r\n\r\nMohon segera melakukan *input perkiraan tanggal panen* untuk sampel tersebut melalui link➡️ \r\n\r\n" . url("/jadwal-ubinan?month=" . $month->id) . " \r\n\r\n*Semangat Ubinan*, Huha…😸😸\r\n\r\nNb: Pesan 💚 Khusus untuk: *" . $ppl->name . "*\r\nBonus kata-kata mutiara:\r\n" . $quote;
                    $message[] = ["message" => $prefixppl . $pplMessage . $suffixppl, "phone_number" => "+62" . $ppl->phone_number, "type" => 'monthly', "sent_to" => $ppl->name, "ids" => $scheduleIds, "role" => $ppl->roles->first()->name];

                    $pplmap[] = ['name' => $ppl->name, 'total_sample' => count($schedules)];
                }
                $prefixpml = '';
                if ($selectedBreakpoint[1] == 'next') {
                    $prefixpml = "Halo, *PML Ubinan Fighters….!!!*🤩🤩🤩 \r\n\r\nBerikut adalah jumlah sampel ubinan bulan depan (*" . $month->name . "*) untuk PPL Anda 💪💪💪 \r\n \r\n";
                } else {
                    $prefixpml = "Halo, *PML Ubinan Fighters….!!!*🤩🤩🤩 \r\n\r\nBerikut adalah jumlah sampel ubinan bulan ini (*" . $month->name . "*) yang belum diinput tanggal perkiraan panennya oleh PPL 💪💪💪 \r\n \r\n";
                }
                $pmlMessage = '';
                foreach ($pplmap as $map) {
                    $pmlMessage = $pmlMessage . "🚩*" . $map['name'] . "* : *" . $map['total_sample'] . "* sampel" . "\r\n\r\n";
                }
                $rand = rand(1, $randQuoteNumMax);
                $quote = Quote::find($rand)->quote;
                $suffixpml = "Mohon untuk mengingatkan kembali PPL untuk menginput perkiraan tanggal panen. Sampel selengkapnya dan perkiraan tanggal panen bisa diakses melalui link berikut➡️ \r\n\r\n" . url("/jadwal-panen") . " \r\n\r\nNb: Pesan 💚 Khusus untuk: *" . $pml->name . "*\r\nBonus kata-kata mutiara:\r\n" . $quote;

                $message[] = ["message" => $prefixpml . $pmlMessage . $suffixpml, "phone_number" => "+62" . $pml->phone_number, "type" => "monthly", "sent_to" => $pml->name, "ids" => [], "role" => $pml->roles->first()->name];
            }

            $admin = User::find(1);
            $prefixadmin = "Halo *Admin Survei Ubinan* 🤩🤩🤩\r\n\r\n";
            $adminMessage = '';
            if ($selectedBreakpoint[1] == 'next') {
                $adminMessage = "Hari ini telah dikirimkan Pengingat Bulanan untuk bulan *" . $month->name . "* kepada petugas Survei Ubinan. Secara keseluruhan ada total *" . $totalSample . " sampel*. Selanjutnya PPL akan mengisi jadwal panen untuk setiap sampel. Selengkapnya bisa dilihat di➡️ \r\n\r\n"
                    . url("/") . "\r\n\r\ndengan menggunakan akun admin";
            } else {
                $adminMessage = "Hari ini telah dikirimkan Pengingat Bulanan untuk bulan *" . $month->name . "* kepada petugas Survei Ubinan yang belum menginput tanggal perkiraan panen. Secara keseluruhan ada total *" . $totalSample . " sampel yang belum diinput tanggal perkiraan panennya*. Selanjutnya PPL akan mengisi jadwal panen untuk setiap sampel. Selengkapnya bisa dilihat di➡️ \r\n\r\n"
                    . url("/") . "\r\n\r\ndengan menggunakan akun admin";
            }

            $suffixadmin = "\r\n\r\n*Terima kasih...*💪💪💪";

            $message[] = ["message" => $prefixadmin . $adminMessage . $suffixadmin, "phone_number" => "+62" . $admin->phone_number, "type" => "monthly", "sent_to" => $admin->name, "ids" => [], "role" => $admin->roles->first()->name];
            $message[] = ["message" => $prefixadmin . $adminMessage . $suffixadmin, "phone_number" => "+6285330166644", "type" => "monthly", "sent_to" => $admin->name, "ids" => [], "role" => $admin->roles->first()->name];
        }

        $gapDay = ['+1' => 'Besok', '+3' => '3 Hari Lagi'];
        $mapGapDay = [];
        $year = Year::where(['name' => date('Y', strtotime($today))])->first();
        // $month = Month::find(intval(date("m", strtotime($today))));
        $subround = (int) (floor((intval(date("m", strtotime($today))) - 1) / 4) + 1);
        $max = $subround * 4;
        $min = $max - 3;
        $months = range($min, $max);

        $schedules = MonthlySchedule::where('year_id', $year->id)->whereIn('month_id', $months)->orderBy(HarvestSchedule::select('date')->whereColumn('harvest_schedules.monthly_schedule_id', 'monthly_schedules.id'))->get();
        foreach ($gapDay as $gap => $alias) {
            $mapGapDay[$gap] = ['date' => date("Y-m-d", strtotime("+" . $gap . " day", strtotime($today))), 'alias' => $alias];
        }

        $messageCount = ['+1' => 0, '+3' => 0];
        if (count($schedules) > 0) {
            foreach ($schedules as $schedule) {
                if ($schedule->harvestSchedule != null) {
                    foreach ($mapGapDay as $key => $map) {
                        if ($map['date'] == $schedule->harvestSchedule->date) {
                            $messageCount[$key]++;
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
                    $prefixppl = "Halo *Ubinan Fighters* BPS Kabupaten Probolinggo🤩🤩!!!\r\nSekedar mengingatkan kalau dalam waktu dekat ada panen untuk sampel ubinan berikut:\r\n\r\n";
                    $rand = rand(1, $randQuoteNumMax);
                    $quote = Quote::find($rand)->quote;
                    $suffixppl = "Pastikan tidak terlewat ya…., siapkan juga alat ubinannya mulai dari sekarang serta terakhir jangan lupa berdoa…💪💪💪\r\nSemangat, Fighting….\r\nBonus kata-kata mutiara:\r\n" . $quote;
                    $pplMessage = '';
                    foreach ($gap as $gapkey => $transformedSchedules) {
                        $pplMessage = $pplMessage . strtoupper($gapDay[$gapkey]) . "\r\n";
                        $samples = [];
                        $scheduleIds = [];
                        foreach ($transformedSchedules as $transformedSchedule) {
                            if ($transformedSchedule->commodity->id == 1) {
                                $samples[] = "🚩" . $transformedSchedule->bs->fullnamesegment() . " " . $transformedSchedule->bs->fullcodesegment() . sprintf('%02d', $transformedSchedule->segment) . $transformedSchedule->subSegment->code . " (*" . $transformedSchedule->commodity->name . "*) ";
                                $pplmap[$gapkey][] = "🚩" . $transformedSchedule->bs->fullnamesegment() . " " . $transformedSchedule->bs->fullcodesegment() . sprintf('%02d', $transformedSchedule->segment) . $transformedSchedule->subSegment->code . " (*" . $transformedSchedule->commodity->name . "*) -- " . "*" . $transformedSchedule->user->name . "*";
                            } else {
                                $samples[] = "🚩" . $transformedSchedule->bs->fullname() . " " . $transformedSchedule->name . " (*" . $transformedSchedule->commodity->name . "*) ";
                                $pplmap[$gapkey][] = "🚩" . $transformedSchedule->bs->fullname() . " " . $transformedSchedule->name . " (*" . $transformedSchedule->commodity->name . "*) -- " . "*" . $transformedSchedule->user->name . "*";
                            }
                            $scheduleIds[] = $transformedSchedule->id;
                        }
                        $pplMessage = $pplMessage . implode("\r\n", $samples);
                        $pplMessage = $pplMessage . "\r\n\r\n";
                    }

                    $message[] = ["message" => $prefixppl . $pplMessage . $suffixppl, "phone_number" => "+62" . $ppl->phone_number, "type" => "harvest", "sent_to" => $ppl->name, "ids" => $scheduleIds, "role" => $ppl->roles->first()->name];
                }
                $prefixpml = "Halo *PML Ubinan Fighters…*🤩🤩🤩, \r\nMengingatkan kembali kalau dalam waktu dekat ada panen untuk sampel ubinan berikut:\r\n\r\n";
                $pmlMessage = '';
                foreach ($pplmap as $key => $array) {
                    $pmlMessage = $pmlMessage . strtoupper($gapDay[$key]) . "\r\n";
                    $pmlMessage = $pmlMessage . implode("\r\n", $array);
                    $pmlMessage = $pmlMessage . "\r\n\r\n";
                }
                $rand = rand(1, $randQuoteNumMax);
                $quote = Quote::find($rand)->quote;
                $suffixpml = "\r\nMohon untuk *mengingatkan* kembali PPL terkait jadwal panen tersebut agar tidak terlewat. Sampel selengkapnya dan perkiraan tanggal panen bisa diakses melalui link berikut➡️ \r\n\r\n" . url("/jadwal-panen") .
                    " \r\n\r\n*Semangat, Fighting*💪💪💪.\r\nBonus kata-kata mutiara:\r\n" . $quote;

                $message[] = ["message" => $prefixpml . $pmlMessage . $suffixpml, "phone_number" => "+62" . $pml->phone_number, "type" => "harvest", "sent_to" => $pml->name, "ids" => [], "role" => $pml->roles->first()->name];
            }

            if (count($transformedData['harvest']) > 0) {
                $admin = User::find(1);
                $prefixadmin = "Halo *Admin Survei Ubinan* 🤩🤩🤩\r\n\r\n";
                $adminMessage = "Hari ini telah dikirimkan reminder jadwal panen\r\n";
                foreach ($gapDay as $key => $day) {
                    $adminMessage = $adminMessage . "🚩 *" . strtoupper($day) . "* ada *" . $messageCount[$key] . "* sampel\r\n";
                }
                $suffixadmin = "\r\n\r\n*Terima kasih...*💪💪💪";

                $message[] = ["message" => $prefixadmin . $adminMessage . $suffixadmin, "phone_number" => "+62" . $admin->phone_number, "type" => "harvest", "sent_to" => $admin->name, "ids" => [], "role" => $admin->roles->first()->name];
                $message[] = ["message" => $prefixadmin . $adminMessage . $suffixadmin, "phone_number" => "+6285330166644", "type" => "harvest", "sent_to" => $admin->name, "ids" => [], "role" => $admin->roles->first()->name];
            }
        }
        if (count($transformedData['monthly']) == 0 && count($transformedData['harvest']) == 0) {
            $admin = User::find(1);

            $prefixadmin = "Halo *Admin Survei Ubinan* 🤩🤩🤩\r\n\r\n";
            $adminMessage = "Hari ini tidak ada reminder jadwal ubinan\r\n";
            $suffixadmin = "\r\n\r\n*Terima kasih...*💪💪💪";
            $message[] = ["message" => $prefixadmin . $adminMessage . $suffixadmin, "phone_number" => "+62" . $admin->phone_number, "type" => "harvest", "sent_to" => $admin->name, "ids" => [], "role" => $admin->roles->first()->name];
            $message[] = ["message" => $prefixadmin . $adminMessage . $suffixadmin, "phone_number" => "+6285330166644", "type" => "harvest", "sent_to" => $admin->name, "ids" => [], "role" => $admin->roles->first()->name];
        }

        return $message;
    }
}
