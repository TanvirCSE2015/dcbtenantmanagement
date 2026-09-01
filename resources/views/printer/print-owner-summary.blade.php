<!DOCTYPE html>
<html lang="bn">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        মালিকানা হস্তান্তরের ইতিহাস -
        Flat {{ $flat->flat_no }}
    </title>


    <style>

        /*
        |--------------------------------------------------------------------------
        | Page
        |--------------------------------------------------------------------------
        */

        @page {
            size: A4 portrait;
            margin: 12mm;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
        }

        body {
            font-family:
                "Noto Sans Bengali",
                "SolaimanLipi",
                "Kalpurush",
                Arial,
                sans-serif;

            font-size: 12px;
            line-height: 1.5;
            color: #111;
            background: #fff;
        }


        /*
        |--------------------------------------------------------------------------
        | Print Container
        |--------------------------------------------------------------------------
        */

        .print-container {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
        }


        /*
        |--------------------------------------------------------------------------
        | Header
        |--------------------------------------------------------------------------
        */

        .header {
            text-align: center;
            margin-bottom: 12px;
        }

        .header h1 {
            margin: 0;
            font-size: 21px;
            font-weight: 700;
        }

        .header h2 {
            margin: 3px 0 0;
            font-size: 16px;
            font-weight: 600;
        }

        .header .subtitle {
            margin-top: 3px;
            font-size: 11px;
        }


        /*
        |--------------------------------------------------------------------------
        | Section Title
        |--------------------------------------------------------------------------
        */

        .section-title {
            margin-top: 12px;
            margin-bottom: 6px;

            padding: 6px 8px;

            background: #f1f1f1;

            border: 1px solid #999;

            font-size: 13px;
            font-weight: 700;
        }


        /*
        |--------------------------------------------------------------------------
        | Property Information
        |--------------------------------------------------------------------------
        */

        .property-table {
            width: 100%;
            border-collapse: collapse;
        }

        .property-table td {
            border: 1px solid #999;
            padding: 5px 7px;
            vertical-align: middle;
        }

        .property-table .label {
            width: 16%;
            font-weight: 600;
            background: #f7f7f7;
        }

        .property-table .value {
            width: 34%;
        }


        /*
        |--------------------------------------------------------------------------
        | Current Owners
        |--------------------------------------------------------------------------
        */

        .owner-table {
            width: 100%;
            border-collapse: collapse;
        }

        .owner-table th,
        .owner-table td {
            border: 1px solid #777;
            padding: 5px 6px;
            vertical-align: middle;
        }

        .owner-table th {
            background: #eeeeee;
            text-align: center;
            font-weight: 700;
        }

        .owner-table td.center {
            text-align: center;
        }


        /*
        |--------------------------------------------------------------------------
        | Transfer History
        |--------------------------------------------------------------------------
        */

        .transfer-block {
            margin-bottom: 10px;

            page-break-inside: avoid;
            break-inside: avoid;
        }

        .transfer-header {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }

        .transfer-header td {
            border: 1px solid #777;
            padding: 5px 7px;
        }

        .transfer-header .label {
            width: 15%;
            background: #f3f3f3;
            font-weight: 600;
        }

        .transfer-header .value {
            width: 18%;
        }


        /*
        |--------------------------------------------------------------------------
        | Owner Transfer Table
        |--------------------------------------------------------------------------
        */

        .transfer-owner-table {
            width: 100%;
            border-collapse: collapse;
        }

        .transfer-owner-table th,
        .transfer-owner-table td {
            border: 1px solid #777;
            padding: 5px 6px;
            vertical-align: middle;
        }

        .transfer-owner-table th {
            background: #eeeeee;
            text-align: center;
            font-weight: 700;
        }

        .transfer-owner-table td.center {
            text-align: center;
        }


        /*
        |--------------------------------------------------------------------------
        | Remarks
        |--------------------------------------------------------------------------
        */

        .remarks {
            margin-top: 4px;
            border: 1px solid #777;
            padding: 6px;
        }

        .remarks strong {
            margin-right: 5px;
        }


        /*
        |--------------------------------------------------------------------------
        | Empty
        |--------------------------------------------------------------------------
        */

        .empty {
            text-align: center;
            padding: 15px;
            border: 1px solid #999;
        }


        /*
        |--------------------------------------------------------------------------
        | Footer / Signature
        |--------------------------------------------------------------------------
        */

        .signature-area {
            margin-top: 35px;

            display: table;
            width: 100%;
            table-layout: fixed;
        }

        .signature {
            display: table-cell;
            text-align: center;
            vertical-align: bottom;
            padding: 0 15px;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-bottom: 5px;
            padding-top: 3px;
        }


        .generated-info {
            margin-top: 15px;

            font-size: 10px;

            display: flex;
            justify-content: space-between;
        }


        /*
        |--------------------------------------------------------------------------
        | Print Button
        |--------------------------------------------------------------------------
        */

        .print-button-wrapper {
            margin-bottom: 15px;
            text-align: right;
        }

        .print-button {
            border: none;
            background: #333;
            color: white;

            padding: 7px 14px;

            border-radius: 4px;

            cursor: pointer;

            font-size: 12px;
        }


        /*
        |--------------------------------------------------------------------------
        | Print CSS
        |--------------------------------------------------------------------------
        */

        @media print {

            .print-button-wrapper {
                display: none;
            }

            body {
                background: white;
            }

            .print-container {
                width: 100%;
                max-width: none;
            }

            .section-title {
                background: #f1f1f1 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .property-table .label,
            .owner-table th,
            .transfer-owner-table th,
            .transfer-header .label {
                background: #eeeeee !important;

                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

        }

    </style>

</head>


<body>


<div class="print-container">


    {{-- ============================================================
        HEADER
    ============================================================= --}}

    <div class="header">
        <img src="{{ asset('images/brands/logo.png') }}" alt="Logo" style="height: 80px; margin-bottom: 5px;">
        <h1>
            মালিকানা হস্তান্তরের ইতিহাস
        </h1>

        <h2>
            ফ্ল্যাট মালিকানা সংক্রান্ত বিবরণ
        </h2>

        {{-- <div class="subtitle">
            Ownership Transfer History
        </div> --}}

    </div>


    {{-- ============================================================
        PROPERTY INFORMATION
    ============================================================= --}}

    <div class="section-title">
        ফ্ল্যাটের বিবরণ
    </div>


    <table class="property-table">

        <tr>

            <td class="label">
                এলাকা
            </td>

            <td class="value">
                {{ $flat->floor?->building?->plot?->area?->area_name ?? '-' }}
            </td>

            <td class="label">
                প্লট নং
            </td>

            <td class="value">
                {{ $flat->floor?->building?->plot?->plot_no ?? '-' }}
            </td>

        </tr>


        <tr>

            <td class="label">
                ভবন
            </td>

            <td class="value">
                {{ $flat->floor?->building?->building_name ?? '-' }}
            </td>

            <td class="label">
                তলা
            </td>

            <td class="value">
                {{ $flat->floor?->floor_no ?? '-' }}
            </td>

        </tr>


        <tr>

            <td class="label">
                ফ্ল্যাট নং
            </td>

            <td class="value">
                {{ $flat->flat_no }}
            </td>

            <td class="label">
                দিক
            </td>

            <td class="value">

                @php

                    $sideLabels = [
                        'north' => 'উত্তর',
                        'south' => 'দক্ষিণ',
                        'east'  => 'পূর্ব',
                        'west'  => 'পশ্চিম',
                    ];

                @endphp

                {{ $sideLabels[$flat->flat_side] ?? $flat->flat_side ?? '-' }}

            </td>

        </tr>


        <tr>

            <td class="label">
                ফ্ল্যাটের আয়তন
            </td>

            <td class="value">
                {{ $flat->flat_area ?? '-' }}
                {{ $flat->flat_area ? 'বর্গফুট' : '' }}
            </td>

            <td class="label">
                মোট হস্তান্তর
            </td>

            <td class="value">
                {{ $transfers->count() }} বার
            </td>

        </tr>

    </table>


    {{-- ============================================================
        CURRENT OWNERS
    ============================================================= --}}

    <div class="section-title">
        বর্তমান মালিকগণ
    </div>


    @if($flat->currentOwners->count())

        <table class="owner-table">

            <thead>

                <tr>

                    <th style="width: 8%;">
                        ক্রমিক
                    </th>

                    <th>
                        মালিকের নাম
                    </th>

                    <th style="width: 22%;">
                        মোবাইল
                    </th>

                    <th style="width: 18%;">
                        মালিকানার হার
                    </th>

                    <th style="width: 18%;">
                        শুরুর তারিখ
                    </th>

                </tr>

            </thead>


            <tbody>

                @foreach($flat->currentOwners as $index => $owner)

                    <tr>

                        <td class="center">
                            {{ $index + 1 }}
                        </td>

                        <td>
                            {{ $owner->user?->name ?? '-' }}
                        </td>

                        <td class="center">
                            {{ $owner->user?->mobile ?? '-' }}
                        </td>

                        <td class="center">
                            {{ number_format($owner->ownership_percent, 2) }}%
                        </td>

                        <td class="center">
                            {{ $owner->start_date
                                ? \Carbon\Carbon::parse($owner->start_date)->format('d/m/Y')
                                : '-'
                            }}
                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    @else

        <div class="empty">
            বর্তমানে কোনো মালিকের তথ্য পাওয়া যায়নি।
        </div>

    @endif


    {{-- ============================================================
        TRANSFER HISTORY
    ============================================================= --}}

    <div class="section-title">
        মালিকানা হস্তান্তরের বিস্তারিত ইতিহাস
    </div>


    @forelse($transfers as $transfer)

        <div class="transfer-block">


            {{-- Transfer Header --}}

            <table class="transfer-header">

                <tr>

                    <td class="label">
                        হস্তান্তর নং
                    </td>

                    <td class="value">
                        {{ $loop->iteration }}
                    </td>

                    <td class="label">
                        তারিখ
                    </td>

                    <td class="value">
                        {{ $transfer->transfer_date?->format('d/m/Y') ?? '-' }}
                    </td>

                    <td class="label">
                        ধরন
                    </td>

                    <td class="value">
                        {{ $transferTypes[$transfer->transfer_type]
                            ?? $transfer->transfer_type
                            ?? '-'
                        }}
                    </td>

                </tr>


                <tr>

                    <td class="label">
                        দলিল নং
                    </td>

                    <td colspan="2">
                        {{ $transfer->document_no ?? '-' }}
                    </td>

                    <td class="label">
                        তৈরি করেছেন
                    </td>

                    <td colspan="2">
                        {{ $transfer->createdBy?->name ?? '-' }}
                    </td>

                </tr>

            </table>


            {{-- Owners --}}

            <table class="transfer-owner-table">

                <thead>

                    <tr>

                        <th style="width: 8%;">
                            ক্রমিক
                        </th>

                        <th style="width: 20%;">
                            মালিকের নাম
                        </th>

                        <th style="width: 18%;">
                            মোবাইল
                        </th>

                        <th style="width: 16%;">
                            ধরণ
                        </th>

                        <th style="width: 16%;">
                            মালিকানা
                        </th>

                        <th>
                            মালিকানা শুরুর তারিখ
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach($transfer->items as $item)

                        <tr>

                            <td class="center">
                                {{ $loop->iteration }}
                            </td>

                            <td>
                                {{ $item->owner?->user?->name ?? '-' }}
                            </td>

                            <td class="center">
                                {{ $item->owner?->user?->mobile ?? '-' }}
                            </td>

                            <td class="center">

                                @if($item->direction === 'from')

                                    পূর্বের মালিক

                                @else

                                    নতুন মালিক

                                @endif

                            </td>

                            <td class="center">
                                {{ number_format($item->ownership_percent, 2) }}%
                            </td>

                            <td class="center">

                                @if(
                                    $item->owner &&
                                    $item->owner->start_date
                                )

                                    {{ \Carbon\Carbon::parse(
                                        $item->owner->start_date
                                    )->format('d/m/Y') }}

                                @else

                                    -

                                @endif

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>


            {{-- Remarks --}}

            @if($transfer->remarks)

                <div class="remarks">

                    <strong>
                        মন্তব্য:
                    </strong>

                    {{ $transfer->remarks }}

                </div>

            @endif


        </div>

    @empty

        <div class="empty">

            এই ফ্ল্যাটের কোনো মালিকানা হস্তান্তরের ইতিহাস পাওয়া যায়নি।

        </div>

    @endforelse


    {{-- ============================================================
        SIGNATURE
    ============================================================= --}}

    <div class="signature-area">


        <div class="signature">

            <div style="height: 35px;"></div>

            <div class="signature-line">
                প্রস্তুতকারীর স্বাক্ষর
            </div>

        </div>


        <div class="signature">

            <div style="height: 35px;"></div>

            <div class="signature-line">
                যাচাইকারীর স্বাক্ষর
            </div>

        </div>


        <div class="signature">

            <div style="height: 35px;"></div>

            <div class="signature-line">
                অনুমোদনকারী
            </div>

        </div>


    </div>


    {{-- ============================================================
        FOOTER
    ============================================================= --}}

    <div class="generated-info">

        <span>
            রিপোর্ট তৈরি:
            {{ now()->format('d/m/Y h:i A') }}
        </span>

        <span>
            মোট হস্তান্তর:
            {{ $transfers->count() }} টি
        </span>

    </div>


</div>


<script>
    window.onload = function () {
        window.print();
        window.onafterprint = function () {
            window.close();
        };

    }

</script>


</body>

</html>