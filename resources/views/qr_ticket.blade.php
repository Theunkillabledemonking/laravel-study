<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <style>
        @page { size: 148mm 210mm; margin: 0; }

        @font-face {
            font-family: 'NotoSansJP', sans-serif;
            src: url('{{ storage_path('fonts/NotoSansJP-Regular.ttf') }}') format('truetype');
        }

        body { font-family: 'NotoSansJP', sans-serif; margin:0; padding:0; }

        .page.sheet {
            width:148mm; height:210mm;
            page-break-after: always;
            position: relative; box-sizing: border-box; margin:0 !important;
        }

        /* 카드 공통 (기본 그리드용; 슬롯 배치는 아래에서 별도 처리) */
        .barcodecard, .qrcodecard, .card.empty {
            width:111.1mm; height:154mm; position:absolute; box-sizing:border-box;
        }
        .barcodecard {
            background:#000; color:#fff; display:flex; flex-direction:column; align-items:center; justify-content:center;
        }
        /* .debug * { outline: 0.2mm dotted rgba(0,0,0,.2); } */
    </style>
</head>
<body class="">
@php
    // ===== 상수 =====
    $PAGE_W = 148;              // A5(mm)
    $PAGE_H = 210;
    $CARD_W = 111.1;            // 기본 그리드(비-슬롯)용 A6 카드 크기
    $CARD_H = 154;
    $START_X = 12;              // 기본 그리드 시작 위치
    $START_Y = 5;

    // ★ QR 슬롯 좌표(앞 5칸만 사용). type==='qr' && idx 0~4 일 때만 이 좌표가 적용됨
    $QR_SLOTS = [
        ['x'=>16, 'y'=>45,  'size'=>33, 'code_off'=>7, 'name_off'=>6, 'fs_code'=>2.2, 'fs_name'=>2.6], // 좌상
        ['x'=>93, 'y'=>55,  'size'=>35, 'code_off'=>8, 'name_off'=>7, 'fs_code'=>2.3, 'fs_name'=>2.8], // 우상
        ['x'=>22, 'y'=>152, 'size'=>27, 'code_off'=>6, 'name_off'=>5, 'fs_code'=>1.9, 'fs_name'=>2.2], // 좌중(작게)
        ['x'=>93, 'y'=>105, 'size'=>35, 'code_off'=>8, 'name_off'=>7, 'fs_code'=>2.3, 'fs_name'=>2.8], // 우중
        ['x'=>93, 'y'=>155, 'size'=>35, 'code_off'=>8, 'name_off'=>7, 'fs_code'=>2.3, 'fs_name'=>2.8], // 우하
    ];
@endphp

@foreach ($pages as $page)
@php
    $sizeType = $page['size_type'] ?? 'sheet';   // 보통 'sheet'
    $pageType = $page['type'] ?? '';             // 'cover' or ''
@endphp

<div class="page {{ $sizeType }}" style="width:{{ $PAGE_W }}mm; height:{{ $PAGE_H }}mm;">
    {{-- 배경 템플릿 (필요 시 base64 인라인로 바꿔도 됨) --}}
    <img src="{{ asset('storage/a5_template.svg') }}"
         style="position:absolute; inset:0; width:{{ $PAGE_W }}mm; height:{{ $PAGE_H }}mm; z-index:0; pointer-events:none;">

    {{-- ========= 커버 페이지 ========= --}}
    @if ($pageType === 'cover')
        <!-- 검정 배경 (A5 안쪽) -->
        <div style="position:absolute; left:5.5mm; top:9mm; width:135mm; height:190mm; background:#1f1f1f; z-index:1;"></div>

        <!-- 우상단 패널 -->
        <div style="position:absolute; right:5.5mm; top:5.5mm; width:70mm; height:45mm; background:#fff; z-index:2; padding:3mm 4mm; box-sizing:border-box;">
            <!-- 헤더 -->
            <div style="position:relative; height:10mm;">
                <div style="text-align:right; line-height:1.2;">
                    <div style="font-size:2.8mm;">{{ $page['data']['dot'] ?? '■' }}</div>
                    <div style="font-size:2.8mm; margin-top:1mm;">{{ $page['data']['page_counter'] ?? '' }}</div>
                </div>
                <!-- 고객명 -->
                <div style="position:absolute; right:0; top:7mm; text-align:center; font-size:4.5mm; font-weight:700; letter-spacing:0.25mm;">
                    {{ $page['data']['customer'] ?? '' }}
                </div>
            </div>

            <!-- 본문 -->
            <div style="position:absolute; right:9mm; top:16mm;">
                <div style="font-size:2.8mm; margin-bottom:2mm; margin-left:4mm;">{{ $page['data']['units_label'] ?? '' }}</div>
                <div style="margin-left:1mm;">
                    @if (!empty($page['data']['barcode_img']))
                        <img src="data:image/svg+xml;base64,{{ $page['data']['barcode_img'] }}" style="height:8mm; display:block;">
                    @else
                        <div style="width:37mm; height:8mm; background:#fff; border:0.15mm solid #000;"></div>
                    @endif
                </div>
                <div style="font-size:3.2mm; margin-top:1.5mm; margin-left:4mm;">{{ $page['data']['barcode_text'] ?? '' }}</div>
            </div>
        </div>

    {{-- ========= 일반 페이지 ========= --}}
    @else
        @phpf=
            // 우상단 헤더 패널(페이지 공통 표기 영역)
            $HDR_RIGHT = 5.5; $HDR_TOP = 5.5; $HDR_W = 68; $HDR_H = 23.46;
        @endphp

        <div style="position:absolute; right:{{ $HDR_RIGHT }}mm; top:{{ $HDR_TOP }}mm; width:{{ $HDR_W }}mm; height:{{ $HDR_H }}mm;
                    display:flex; flex-direction:column; justify-content:space-between; align-items:flex-end; z-index:10;">
            <div style="font-size:3mm;">{{ $page['data']['dot'] ?? '■' }}</div>
            <div style="display:flex; align-items:center;">
                <div style="font-size:2.6mm;">{{ $page['data']['page_counter'] ?? '' }}</div>
            </div>
            <div style="font-size:4mm;">{{ $page['data']['order_code'] ?? '' }}</div>
            <div style="font-size:4mm;">{{ $page['data']['customer'] ?? '' }}　様</div>
        </div>

        @php $items = $page['items'] ?? []; @endphp
        @foreach ($items as $idx => $item)
            @php
                // ★ QR 슬롯 분기: type==='qr' && idx 0~4 && 슬롯 존재
                $isQr = ($item['type'] ?? '') === 'qr' && $idx <= 4 && !empty($QR_SLOTS[$idx]);

                // 기본 그리드(비-슬롯) 좌표
                $col = $idx % 2; $row = intdiv($idx, 2);
                $left = $START_X + $col * $CARD_W;
                $top  = $START_Y + $row * $CARD_H;

                if ($isQr) {
                    $slot   = $QR_SLOTS[$idx];
                    $left   = $slot['x'];
                    $top    = $slot['y'];
                    $size   = $slot['size'];
                    $fsCode = $slot['fs_code'] ?? round($size * 0.07, 2);
                    $fsName = $slot['fs_name'] ?? round($size * 0.09, 2);
                    $offCode= $slot['code_off'] ?? round($size * 0.22, 2);
                    $offName= $slot['name_off'] ?? round($size * 0.18, 2);
                }
            @endphp

            @if (($item['type'] ?? '') === 'atamagami')
                <div class="barcodecard" style="left:{{ $left }}mm; top:{{ $top }}mm;"></div>

            @elseif ($isQr)
                <div class="qrcodecard" style="left:{{ $left }}mm; top:{{ $top }}mm; width:{{ $size }}mm; height:{{ $size }}mm;
                                                position:absolute; display:flex; align-items:center; justify-content:center;">
                    {{-- QR