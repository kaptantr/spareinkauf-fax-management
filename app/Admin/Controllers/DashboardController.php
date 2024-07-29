<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FaxKayit;
use App\Models\MusteriTanim;
use App\Models\Siparis;
use Encore\Admin\Layout\Content;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Client;

class DashboardController extends Controller
{
    public function index(Content $content)
    {
        //$musteri_fakslari = $this->getAllMusteriFaxs();

        //$this->fakslariEsitle($musteri_fakslari);

        return redirect()->route('admin.siparisler');

    }

    public function faks_esitle()
    {
        $musteri_fakslari = $this->getAllMusteriFaxs();

        $this->fakslariEsitle($musteri_fakslari);

        return true;

    }


    public function getAllMusteriFaxs() {
        $musteri_fakslar = MusteriTanim::where('fax', 'like', '%/%')
            ->orWhere('fax', 'like', '+49%')
            ->orWhere('fax', 'regex', '^[0-9]*$')
            ->pluck('fax', 'id')
            ->toArray();

        foreach ($musteri_fakslar as $id=>$faks) {
            $extract_fax = '';

            if (str_starts_with($faks, '+49')) {
                if (strlen($faks) >= 14) {
                    preg_match('/\+49([\d]{11})(\d*)/i', $faks, $output);
                    $extract_fax = "+49{$output[1]}";
                }
                if (strlen($faks) == 13) {
                    preg_match('/\+49([\d]{10})(\d*)/i', $faks, $output);
                    $extract_fax = "+490{$output[1]}";
                }
            }
            elseif (strstr($faks, '/')) {
                $faks = str_ireplace('/', '', $faks);
                if (strlen($faks) >= 11) {
                    preg_match('/([\d]{11})(\d*)/i', $faks, $output);
                    $extract_fax = "+49{$output[1]}";
                }
                if (strlen($faks) == 10) {
                    preg_match('/([\d]{10})(\d*)/i', $faks, $output);
                    $extract_fax = "+490{$output[1]}";
                }
            }
            elseif (is_numeric($faks)) {
                if (strlen($faks) >= 11) {
                    preg_match('/([\d]{11})(\d*)/i', $faks, $output);
                    $extract_fax = "+49{$output[1]}";
                }
                if (strlen($faks) == 10) {
                    preg_match('/([\d]{10})(\d*)/i', $faks, $output);
                    $extract_fax = "+490{$output[1]}";
                }
            }

            $musteri_fakslar[$id] = $extract_fax;
        }

        return $musteri_fakslar;
    }


    public function fakslariEsitle($musteri_fakslari) {
        $imapConfig = __DIR__.'/../../../config/imap.php';
        $client = new ClientManager($imapConfig);
        $client->connect();
        $folders = $client->getFolders();

        $subjects = [];

        foreach($folders as $folder){
            $messages = $folder->messages()->all()->get();
            $upload_path =  __DIR__.'/../../../public/uploads/';

            foreach($messages as $message) {
                $konu = $message->getSubject()[0] ?? '';
                $attachment = $message->getAttachments()[0] ?? null;

                if(!empty($attachment)) {
                    $mime = $attachment->getMimeType();
                    $filename = $attachment->getName();
                }

                //gelen mailin dosyası varmı
                if(!empty($attachment) && !empty($mime) && !empty($filename)) {
                    //gelen yeni pdf maili varmı
                    if(strstr($konu, 'Neues Fax von:') && $mime == 'application/pdf') {
                        //pdf dosyası upload klasöründe varmı
                        if(!file_exists($upload_path . $filename)) {
                            //mail pdf dosyasını upload klasörüne kaydet
                            $save_status = $attachment->save($upload_path, $filename);
                        }

                        $body = $message->getHTMLBody();
                        preg_match_all('/<td class="(?:.*?)">\s*<table class="(?:.*?)" style="(?:.*?)">\s*<tr>\s*<td class="left-text-pad">([\d\+\. \:]+)<\/td>\s*<td class="expander"><\/td>\s*<\/tr>\s*<\/table>\s*<\/td>/i', $body, $output);
                        $gonderici_fax = $output[1][0] ?? '';
                        //$alici_fax = $output[1][1] ?? '';
                        $gonderi_tarihi = $output[1][2] ?? date('d.m.Y H:i:s');
                        $musteri_id = array_search($gonderici_fax, $musteri_fakslari);


                        $siparis = Siparis::firstOrCreate(
                            [
                                'tarih' => date('Y-m-d H:i:s', strtotime($gonderi_tarihi)),
                                'pdf_adi' => $filename,
                            ],
                            [
                                'musteri_id' => $musteri_id,
                                'siparis_islendimi' => '0',
                                'tarih' => date('Y-m-d H:i:s', strtotime($gonderi_tarihi)),
                                'pdf_adi' => $filename,
                                'listelensin' => '1',
                            ]
                        );

                        $fax_kayit = FaxKayit::updateOrCreate(
                            [
                                'gonderen_faks' => $gonderici_fax,
                                'tarih' => date('Y-m-d H:i:s', strtotime($gonderi_tarihi)),
                                'pdf_adi' => $filename,
                            ],
                            [
                                'gonderen_faks' => $gonderici_fax,
                                'tarih' => date('Y-m-d H:i:s', strtotime($gonderi_tarihi)),
                                'esitleme_tarihi' => date('Y-m-d H:i:s', strtotime('+3 hours')),
                                'pdf_adi' => $filename,
                            ]
                        );
                    }
                }

            }
        }
    }
}
