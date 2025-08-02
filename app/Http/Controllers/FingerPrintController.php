<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Symfony\Component\DomCrawler\Crawler;

class FingerPrintController extends Controller
{
    // public function index()
    // {
    //     $client = new \GuzzleHttp\Client();
    //     // $response = $client->request('GET', 'http://solutioncloud.co.id/');

    //     $response = $client->request('POST', 'http://solutioncloud.co.id/sc_pro.asp', [
    //         'form_params' => [
    //             'sn' => 'AEWD200360337',
    //             'pass' => 'solution'
    //         ]
    //     ]);
    //     // $body = $response->getBody();
    //     // $html = (string) $body;
    //     // dd($html);
    //     if ($response->getStatusCode() == 200) {
    //         $data = $client->request('GET', 'http://solutioncloud.co.id/mesin.asp');
    //         $body = $data->getBody();
    //         $html = (string) $body;
    //         dd($html);
    //     }
    //     return view('fingerprint.index');
    // }


    public function index()
    {
        // URL
        $loginUrl = 'http://solutioncloud.co.id'; // atau URL action form jika berbeda
        $targetUrl = 'http://solutioncloud.co.id/mesin.asp';

        // Data login - sesuaikan dengan field yang ada di form
        $credentials = [
            'sn' => 'AEWD200360337', // ganti dengan field sebenarnya
            'pass' => 'solution', // ganti dengan field sebenarnya
            // Tambahkan field lain jika diperlukan
        ];

        // Buat client Guzzle dengan cookie handling
        $client = new Client([
            'cookies' => true,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            ],
            'allow_redirects' => true,
        ]);

        try {
            // 1. Ambil halaman login untuk analisis (jika diperlukan)
            $loginPageResponse = $client->get($loginUrl);
            $loginPageContent = $loginPageResponse->getBody()->getContents();

            // Jika diperlukan, ekstrak parameter tambahan dari form
            // Contoh untuk ASP mungkin ada __VIEWSTATE, __EVENTVALIDATION, dll.
            $viewState = '';
            $eventValidation = '';

            $crawler = new Crawler($loginPageContent);
            $viewState = $crawler->filter('input[name="__VIEWSTATE"]')->attr('value');
            $eventValidation = $crawler->filter('input[name="__EVENTVALIDATION"]')->attr('value');

            // Tambahkan ke credentials jika ditemukan
            if ($viewState) {
                $credentials['__VIEWSTATE'] = $viewState;
            }
            if ($eventValidation) {
                $credentials['__EVENTVALIDATION'] = $eventValidation;
            }

            // 2. Kirim permintaan login
            $loginResponse = $client->post($loginUrl, [
                'form_params' => $credentials,
            ]);
            dd($loginResponse);
            // 3. Verifikasi login berhasil (opsional)
            $loginContent = $loginResponse->getBody()->getContents();
            if (strpos($loginContent, 'Login failed') !== false) {
                throw new \Exception('Login gagal. Periksa kredensial Anda.');
            }

            // 4. Akses halaman target
            $targetResponse = $client->get($targetUrl);
            $targetContent = $targetResponse->getBody()->getContents();

            // 5. Proses konten yang didapat
            $targetCrawler = new Crawler($targetContent);

            // Contoh ekstraksi data dari halaman mesin.asp
            $data = [];

            // Ekstrak tabel (contoh)
            $data['table_rows'] = $targetCrawler->filter('table tr')->each(function (Crawler $row) {
                return $row->filter('td')->each(function (Crawler $cell) {
                    return $cell->text();
                });
            });

            // Ekstrak elemen lain sesuai kebutuhan
            // $data['judul'] = $targetCrawler->filter('h1')->text();

            return $data;
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
}
