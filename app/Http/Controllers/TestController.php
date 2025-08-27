<?php

// 컨트롤러 상단 use 예시
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Logo\Logo;
use Spatie\Browsershot\Browsershot;
use Illuminate\Http\Request;
use App\Http\Controller;

class TestController extends Controller 
{
    public function createPdf(Request $request, $task_id) 
    {
        $shipments = SuperShipment::where('タスクid', $task_id)->get();
        if ($shipments->isEmpty()) {
            return response()->json(['error' => '出荷ヘッダーが見つかりません'], 404);
        }
        $details = SuperDetail::whereIn('ヘッダーid', $shipments->pluck('id'))->get();
        if ($details->isEmpty()) {
            return response()->json(['error' => '明細が見つかりません'], 404);
        }

        $detailsByHeader = $details->groupBy('ヘッダーid');

        // QR 생성 주비
        $writer = new SvgWriter();
        $logoPath = storage_path('app/public/R_logo.svg');
        $logo = is_file($logoPath) ? new Logo($logoPath, resizeToWidth:45, resizeToHeight:45) : null;

        $result_pages = [];
        $totalLabels = 0;

        foreach($detailsByHeader as $headerId => $rows) {
            $shipment = $shipments->firstWhere('id', (int)$headerId);
            if (!$shipment) continue;

            $order_code = $shipment->問い合わせ番号 ?? '';
            $customer = $shipment->店舗宛名 ?: ($shipment->店舗名 ?? '');

            $all_cards = [];

            foreach ($rows as $detail) {
                $merchant = $shipment->店舗コード ?: $shipment->ショップコード;
                $scene = $detail->シーンコード ?: ($detail->シーンCD ?? null);
                $item = $detail->アイテムコード ?: ($detail->商品コード ?? null);
                if (!$shipment || !$scene || !$item) continue;
                // QR URL(기존 규격 유지)
                $crc    = sprintf("%'.08x", crc32($merchant)) . sprintf("%'.08x", crc32($scene.$item));
                $qr_url = config('app.super.qr_url') . '?c=' . $crc . '&m=' . $merchant;

                $qr = new QrCode(
                    data: $qr_url,
                    encoding: new Encoding('UTF-8'),
                    ErrorCorrectionLevel: ErrorCorrectionLevel::High,
                    size: 100,
                    margin: 8
                );
                $qr_image = base64_encode($writer->write($qr, $logo)->getString());

                $quantity = max(1, (int)($detail->数量 ?? 1));
                for ($i = 0; $i < $quantity; $i++) {
                    $all_cards[] = ['type' => 'qr', 'qr_image' => $qr_image];
                    $totalLabels++;
                }
            }

            if (empty($all_cards)) {
                $result_pages[] = [
                    'type' => '',
                    'size_type' => 'sheet',
                    'data' => ['dot' => '', 'order_code' => $order_code, 'customer' => $customer],
                    'items' => array_fill(0, 5, ['type' => 'qr']),
                ];
                continue;
            }
        }

        // 전체가 비는 경우(방어)
        if (empty($result_pages)) {
            $result_pages[] = [
                'type'      => '',
                'size_type' => 'sheet',
                'data'      => ['dot' => '■', 'order_code' => '', 'customer' => ''],
                'items'     => array_fill(0, 5, ['type' => 'qr']),
            ];
        }

         // 페이지 카운터(0001/NNNN)
    $total_pages = count($result_pages);
    foreach ($result_pages as $i => &$page) {
        $page['data']['page_counter'] = sprintf('%04d/%04d', $i + 1, $total_pages);
    }
    unset($page);

    // 4) Blade 렌더
    $html = view('super.qr_ticket', ['pages' => $result_pages])->render();

    // 5) PDF (A5) — 크롬 경로 자동 탐색
    $chromeCandidates = [
        '/usr/bin/chromium',
        '/usr/bin/chromium-browser',
        '/usr/bin/google-chrome-stable',
        '/usr/bin/google-chrome',
        '/opt/homebrew/bin/chromium',
        '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
    ];

    try {
        $b = Browsershot::html($html)
            ->setOption('args', ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage'])
            ->paperSize(148, 210, 'mm')   // A5
            ->margins(0, 0, 0, 0)
            ->showBackground()
            ->waitUntilNetworkIdle();

        foreach ($chromeCandidates as $p) {
            if (is_file($p) || is_executable($p)) { $b->setChromePath($p); break; }
        }

        $pdfBinary = $b->pdf();

        return response($pdfBinary, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="super_qr.pdf"'
        ]);
    } catch (\Throwable $e) {
        return response()->json(
            ['error' => 'PDF生成に失敗しました', 'message' => $e->getMessage()],
            500,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    }

}