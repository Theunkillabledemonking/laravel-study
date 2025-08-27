<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function pdfTest(Request $request, $task_id)
{
    $totalQty = 3; // 수량

    // 표지(첫 장)
    $pages = [[
        'type'  => '', // 'cover' && ''
        'data'  => [
            'page_counter' => '0015/0015',
            'order_code'   => '5006803562368DEFAULT001',
            'customer'     => 'にくだ',
            'barcode_img'  => null,
            'barcode_text' => 'a565793503166001a',
            'units_label'  => '15 枚',
            'dot'          => '■'
        ],
        'items' => [
            ['type' => 'qr'],
            ['type' => 'qr'],
            ['type' => 'qr'],
            ['type' => 'qr'],
            ['type' => 'qr'],
        ] // ← 빈 배열이라도 넣어주면 안전
    ]];

    // 이후 장들: 페이지당 QR 5개
    for ($i = 0; $i < $totalQty; $i++) {
        $items = [];
        for ($k = 0; $k < 5; $k++) {
            $items[] = ['qr_image' => null]; // 지금은 placeholder
        }
        $pages[] = [
            'type'  => 'qr_page',
            'items' => $items,
        ];
    }

    return view('super.qr_ticket', compact('pages'));

    // ここからQRコード
    $shipments = SuperShipment::where('タスクid', $task_id)->get();
    if ($shipments->isEmpty()) {
        return response()->json(['error' => '出荷ヘッダーが見つかりません'], 404);
    }
    $details = SuperDetail::whereIn('ヘッダーID', $shipments->pluck('id'))->get();
    if ($details->isEmpty()) {
        return response()->json(['error' => '明細が見つかりません'], 404);
    }

    // $writer = new PngWriter();
    $writer = new SvgWriter();
    $logoPath = storage_path('app/public/R_logo.svg');
    $result_pages = [];
    $page_buffer = [];
    $all_cards = [];

    foreach ($details as $detail) {
        $shipment = $shipments->where('id', $detail->ヘッダーID)->first();
        if (!$shipment) continue;

        $scene_name = $detail->シーン名 ?? '';
        $serial = str_pad($detail->同梱連番 ?? 1, 3, '0', STR_PAD_LEFT);
        $number = $shipment->問い合わせ番号 ?? '';
        $barcode_value = $number . $serial;
        $quantity = (int)($detail->数量 ?? 1);

        $back_code = ($shipment->ショップコード ?? '');
        $back_info = [
            'back_code' => $back_code,
            'scene_name' => $scene_name,
        ];

        try {
            $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
            $barcode_img = base64_encode($generator->getBarcode($barcode_value, $generator::TYPE_CODE_128, 2, 50));
        } catch (\Throwable $ex) {
            $barcode_img = null;
        }

        // 큐알 코드 로직
        $merchant = $shipment->店舗コード;
        $scene = $detail->シーンコード;
        $item = $detail->アイテムコード;
        if (!$merchant || !$scene || !$item) continue;

        $merchantCrc32 = crc32($merchant);
        $sumCrc32 = crc32($scene . $item);
        $crc = sprintf("%'.08x", $merchantCrc32) . sprintf("%'.08x", $sumCrc32);
        $qr_url = config('app.super.qr_url') . '?c=' . $crc . '&m=' . $merchant;

        $qr = new QrCode(
            data: $qr_url,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 100,
            margin: 8
        );
        $logo = new Logo(
            $logoPath,
            resizeToWidth: 45,
            resizeToHeight: 45,
        );
        $qrResult = $writer->write($qr, $logo);
        $qr_image = base64_encode($qrResult->getString());
        $back_code = ($shipment->ショップコード ?? '') . ($detail->シーンコード ?? '');
        $back_name = $shipment->店舗名 ?? '';

        // $all_cards[] = [
        //     'type' => 'atamagami',
        //     'scene_name' => $scene_name,
        //     'barcode_label' => "a{$barcode_value}a",
        //     'quantity_label' => "納品数量：{$quantity} 枚",
        //     'barcode_img' => $barcode_img,
        //     'size_type' => 'a6',
        //     'qr_back_info' => [
        //         'back_code' => $back_code,
        //         'back_name' => $back_name,
        //     ],
        //     'tsubushi' => $detail->つぶし,
        // ];

        for ($i = 0; $i < $quantity; $i++) {
            $all_cards[] = [
                'type' => 'qr',
                'qr_image' => $qr_image,
                '店舗宛名' => $shipment->店舗宛名 ?? '',
                '店舗名' => $shipment->店舗名 ?? '',
                'size_type' => 'a6',
                'qr_back_info' => [
                    'back_code' => $back_code,
                    'back_name' => $back_name,
                ],
                'tsubushi' => $detail->つぶし,
            ];
        }
    }

    $cards_per_page = 5;
    $total_cards = count($all_cards);
    $total_pages = (int) ceil($total_cards / $cards_per_page);

    $result_pages = [];
    for ($i = 0; $i < $total_pages; $i++) {
        $page_items = array_slice($all_cards, $i * $cards_per_page, $cards_per_page);

        while (count($page_items) < $cards_per_page) {
            $page_items[] = ['type' => 'qr', 'size_type' => 'a5'];
        }
        $result_pages[] = [
            'type' => '',
            'size_type' => 'a6',
            'items' => "sheet",
            
        ];
    }

    $html = view('taxi.qr_ticket', [
        'pages' => $result_pages
    ])->render();

    try {
        $pdfBinary = Browsershot::html($html)
            ->setChromePath('/usr/bin/chromium')
            // ->noSandbox()
            ->setOption('args', ['--no-sandbox', '--disable-setuid-sandbox'])
            ->paperSize(468, 318, 'mm')
            ->margins(0, 0, 0, 0)
            ->showBackground()
            ->waitUntilNetworkIdle()
            ->pdf();

        return response($pdfBinary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="qr_result.pdf"'
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'PDF生成に失敗しました', 'message' => $e->getMessage()], 500);
    }

    return view('taxi.qr_ticket', [
        'pages' => $result_pages
    ]);
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
