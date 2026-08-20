<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ভাড়াটিয়া নিবন্ধন ফরম</title>

    <style>
        /* =========================================
           বাংলা Font
        ========================================= */
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;500;600;700;800&display=swap');


        /* =========================================
           RESET
        ========================================= */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        /* =========================================
           A4 PRINT SETUP
        ========================================= */
        @page {
            size: A4 portrait;
            margin: 0;
        }


        html,
        body {
            width: 100%;
            margin: 0;
            padding: 0;
        }


        body {
            background: #e5e5e5;
            font-family: "Noto Sans Bengali", Arial, sans-serif;
            font-size: 11px;
            line-height: 1.25;
            color: #000;
        }


        /* =========================================
           PRINT BUTTON
        ========================================= */
        .print-area {
            text-align: center;
            padding: 15px;
        }

        .print-btn {
            padding: 10px 30px;
            border: 1px solid #222;
            background: #222;
            color: #fff;
            font-family: "Noto Sans Bengali", Arial, sans-serif;
            font-size: 15px;
            cursor: pointer;
            border-radius: 4px;
        }


        /* =========================================
           A4 PAGE
           
           পুরো Page = 210mm
           Padding দুই পাশে = 8mm
           Content Area = 194mm
        ========================================= */
        .page {
            width: 210mm;
            min-height: 297mm;

            margin: 10px auto;

            padding: 8mm;

            background: #fff;

            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);

            overflow: hidden;

            box-sizing: border-box;
        }


        /* =========================================
           HEADER
        ========================================= */
        .header {
            width: 100%;
            max-width: 100%;

            display: grid;

            grid-template-columns:
                35mm
                minmax(0, 1fr)
                45mm;

            gap: 4mm;

            align-items: start;

            margin-bottom: 2mm;
        }


        /* =========================================
           PHOTO BOX
        ========================================= */
        .photo-box {
            width: 35mm;
            height: 45mm;

            border: 1px solid #000;

            display: flex;
            align-items: center;
            justify-content: center;

            text-align: center;
            font-size: 8px;
            line-height: 1.5;

            overflow: hidden;
        }


        /* =========================================
           HEADER CENTER
        ========================================= */
        .header-center {
            min-width: 0;
            text-align: center;
        }


        .logo {
            width: 17mm;
            height: 17mm;

            border: 1px solid #000;
            border-radius: 50%;

            margin: 0 auto 1mm;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 6px;
        }


        .board-name {
            font-size: 17px;
            font-weight: 800;
            line-height: 1.2;
            white-space: nowrap;
        }


        .board-address {
            font-size: 9px;
            margin-top: 1mm;
        }


        .header-details {
            margin-top: 2mm;
            text-align: left;
            font-size: 9px;
        }


        .header-details div {
            min-height: 6mm;
            display: flex;
            align-items: flex-end;
            gap: 1mm;
        }


        /* =========================================
           FORM INFO BOX
        ========================================= */
        .form-info {
            width: 100%;
            min-width: 0;

            border: 1px solid #000;

            padding: 2mm;

            min-height: 38mm;
        }


        .form-info-row {
            display: grid;
            grid-template-columns: 18mm minmax(0, 1fr);

            gap: 1mm;

            min-height: 6.5mm;

            align-items: end;
        }


        /* =========================================
           TITLE
        ========================================= */
        .form-title {
            width: 100%;

            text-align: center;

            font-size: 16px;
            font-weight: 800;

            text-decoration: underline;

            margin: 2mm 0 4mm;
        }


        /* =========================================
           COMMON FIELD
        ========================================= */
        .section {
            width: 100%;
            max-width: 100%;
            margin-top: 1.5mm;
        }


        .field-row {
            width: 100%;
            max-width: 100%;

            display: grid;

            grid-template-columns:
                8mm
                38mm
                minmax(0, 1fr);

            gap: 1.5mm;

            min-height: 7mm;

            align-items: end;
        }


        .field-row.two-fields {
            grid-template-columns:
                8mm
                38mm
                minmax(0, 1fr)
                28mm
                minmax(0, 1fr);
        }


        .serial {
            font-weight: 700;
            white-space: nowrap;
        }


        .label {
            font-weight: 600;
            min-width: 0;
        }


        /* =========================================
           BLANK LINE
        ========================================= */
        .blank-line {
            width: 100%;
            min-width: 0;

            min-height: 5mm;

            border-bottom: 1px dotted #000;

            overflow: hidden;
        }


        /* =========================================
           SECTION TITLE
        ========================================= */
        .section-title {
            font-weight: 700;
            font-size: 11px;

            margin: 2mm 0 1mm;
        }


        /* =========================================
           FAMILY TABLE
        ========================================= */
        .family-table {
            width: 100%;
            max-width: 100%;

            border-collapse: collapse;

            table-layout: fixed;

            margin-top: 1mm;
        }


        .family-table th,
        .family-table td {
            border: 1px solid #000;

            padding: 1mm;

            height: 7mm;

            text-align: center;
            vertical-align: middle;

            word-wrap: break-word;
            overflow-wrap: break-word;
        }


        .family-table th {
            font-weight: 700;
        }


        /* =========================================
           TWO COLUMN INFORMATION
        ========================================= */
        .double-column {
            width: 100%;

            display: grid;

            grid-template-columns:
                minmax(0, 1fr)
                minmax(0, 1fr);

            gap: 4mm;

            margin-top: 1mm;
        }


        .small-field {
            width: 100%;

            display: grid;

            grid-template-columns:
                27mm
                minmax(0, 1fr);

            gap: 1mm;

            min-height: 6mm;

            align-items: end;
        }


        /* =========================================
           FULL FIELD
        ========================================= */
        .full-field {
            width: 100%;
            max-width: 100%;

            display: grid;

            grid-template-columns:
                8mm
                60mm
                minmax(0, 1fr);

            gap: 1.5mm;

            min-height: 7mm;

            align-items: end;
        }


        /* =========================================
           FOOTER
        ========================================= */
        .footer {
            width: 100%;

            display: flex;

            justify-content: space-between;
            align-items: flex-end;

            margin-top: 10mm;
        }


        .date-box {
            width: 50mm;
        }


        .signature-box {
            width: 50mm;

            text-align: center;
        }


        .signature-line {
            width: 100%;

            border-top: 1px solid #000;

            margin-bottom: 1mm;
        }


        /* =========================================
           SCREEN RESPONSIVE
        ========================================= */
        @media screen and (max-width: 800px) {

            .page {
                width: 100%;
                min-height: auto;

                margin: 0;

                padding: 15px;

                box-shadow: none;
            }


            .header {
                grid-template-columns: 1fr;
            }


            .photo-box {
                margin: auto;
            }


            .field-row,
            .field-row.two-fields,
            .full-field {
                grid-template-columns:
                    8mm
                    minmax(0, 1fr);
            }


            .field-row .blank-line,
            .full-field .blank-line {
                grid-column: 2;
            }


            .double-column {
                grid-template-columns: 1fr;
            }


            .footer {
                flex-direction: column;
                gap: 15mm;
                align-items: stretch;
            }


            .date-box,
            .signature-box {
                width: 100%;
            }

        }


        /* =========================================
           PRINT SETTINGS
        ========================================= */
        @media print {

            @page {
                size: A4 portrait;
                margin: 0;
            }


            html,
            body {
                width: 210mm !important;
                min-height: 297mm !important;

                margin: 0 !important;
                padding: 0 !important;

                background: #fff !important;
            }


            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }


            .print-area {
                display: none !important;
            }


            /* Main A4 Page */
            .page {
                width: 210mm !important;
                min-width: 210mm !important;
                max-width: 210mm !important;

                min-height: 297mm !important;

                margin: 0 !important;

                padding: 8mm !important;

                box-shadow: none !important;

                overflow: hidden !important;

                box-sizing: border-box !important;

                page-break-after: always;
                break-after: page;
            }


            /* সব element page-এর বাইরে যাবে না */
            .header,
            .section,
            .field-row,
            .double-column,
            .full-field,
            .footer,
            .family-table {
                max-width: 100% !important;
            }


            /* Grid children overflow বন্ধ */
            .header-center,
            .form-info,
            .blank-line,
            .label,
            .small-field {
                min-width: 0 !important;
            }


            /* Table অবশ্যই page width-এর মধ্যে থাকবে */
            table {
                width: 100% !important;
                max-width: 100% !important;
            }


            /* Content মাঝখানে ভেঙে নতুন page-এ যাবে না */
            .header,
            .family-table,
            .footer {
                break-inside: avoid;
                page-break-inside: avoid;
            }

        }

    </style>
</head>

<body onload="window.print()">


    <!-- ===============================
         PRINT BUTTON
    ================================ -->
    {{-- <div class="print-area">
        <button
            type="button"
            class="print-btn"
            onclick="window.print()"
        >
            🖨️ প্রিন্ট করুন
        </button>
    </div> --}}


    <!-- ===============================
         A4 PAGE
    ================================ -->
    <div class="page">


        <!-- ===============================
             HEADER
        ================================ -->
        <div class="header">


            <!-- PHOTO -->
            <div class="photo-box">
                {{-- ভাড়াটিয়ার<br>
                পাসপোর্ট সাইজ<br>
                ছবি --}}
                <img src="{{asset('/images/r.jpg')}}" alt="" srcset="" style="height: 170px">
            </div>


            <!-- CENTER HEADER -->
            <div class="header-center">

                <div class="logo">
                    <img src="{{asset('/images/brands/logo.png')}}" alt="" srcset="" height="64mm">
                </div>

                <div class="board-name">
                    ঢাকা ক্যান্টনমেন্ট বোর্ড
                </div>

                <div class="board-address">
                    ঢাকা সেনানিবাস, ঢাকা
                </div>


                <div class="header-details">

                    <div>
                        বিভাগঃ
                        <span class="blank-line"></span>
                    </div>

                    <div>
                        থানাঃ
                        <span class="blank-line"></span>
                    </div>

                </div>

            </div>


            <!-- RIGHT INFORMATION -->
            <div class="form-info">

                <div class="form-info-row">
                    <span>ফর্ম নং</span>
                    <span class="blank-line"></span>
                </div>

                <div class="form-info-row">
                    <span>বাড়ি/হোল্ডিং</span>
                    <span class="blank-line"></span>
                </div>

                <div class="form-info-row">
                    <span>রাস্তা</span>
                    <span class="blank-line"></span>
                </div>

                <div class="form-info-row">
                    <span>এলাকা</span>
                    <span class="blank-line"></span>
                </div>

                <div class="form-info-row">
                    <span>পোস্ট কোড</span>
                    <span class="blank-line"></span>
                </div>

            </div>

        </div>


        <!-- ===============================
             TITLE
        ================================ -->
        <div class="form-title">
            ভাড়াটিয়া নিবন্ধন ফরম
        </div>


        <!-- ===============================
             PERSONAL INFORMATION
        ================================ -->
        <div class="section">


            <div class="field-row">
                <div class="serial">১।</div>
                <div class="label">ভাড়াটিয়ার নাম</div>
                <div class="blank-line">{{$tenant->tenant_name}}</div>
            </div>


            <div class="field-row">
                <div class="serial">২।</div>
                <div class="label">পিতার নাম</div>
                <div class="blank-line">{{$tenant->father_name}}</div>
            </div>


            <div class="field-row two-fields">
                <div class="serial">৩।</div>

                <div class="label">জন্ম তারিখ</div>
                <div class="blank-line">{{$tenant->date_of_birth}}</div>

                <div class="label">বৈবাহিক অবস্থা</div>
                <div class="blank-line"></div>
            </div>


            <div class="field-row">
                <div class="serial">৪।</div>
                <div class="label">জন্মস্থান</div>
                <div class="blank-line"></div>
            </div>


            <div class="field-row">
                <div class="serial">৫।</div>
                <div class="label">পৈতৃক ও বর্তমান ঠিকানা</div>
                <div class="blank-line"></div>
            </div>


            <div class="field-row two-fields">
                <div class="serial">৬।</div>

                <div class="label">ধর্ম</div>
                <div class="blank-line"></div>

                <div class="label">শিক্ষাগত যোগ্যতা</div>
                <div class="blank-line"></div>
            </div>


            <div class="field-row two-fields">
                <div class="serial">৭।</div>

                <div class="label">মোবাইল নম্বর</div>
                <div class="blank-line">{{$tenant->mobile}}</div>

                <div class="label">ই-মেইল</div>
                <div class="blank-line"></div>
            </div>


            <div class="field-row">
                <div class="serial">৮।</div>
                <div class="label">জাতীয় পরিচয়পত্র নম্বর</div>
                <div class="blank-line">{{$tenant->nid_no}}</div>
            </div>


            <div class="field-row">
                <div class="serial">৯।</div>
                <div class="label">পাসপোর্ট নম্বর (যদি থাকে)</div>
                <div class="blank-line">{{$tenant->passport_no}}</div>
            </div>


            <div class="field-row">
                <div class="serial">১০।</div>
                <div class="label">স্থায়ী পেশা</div>
                <div class="blank-line"></div>
            </div>


            <div class="field-row two-fields">
                <div></div>

                <div class="label">(ক) অফিসের নাম</div>
                <div class="blank-line"></div>

                <div class="label">পদবী</div>
                <div class="blank-line"></div>
            </div>


            <div class="field-row two-fields">
                <div></div>

                <div class="label">(খ) অফিসের ঠিকানা</div>
                <div class="blank-line"></div>

                <div class="label">মোবাইল নম্বর</div>
                <div class="blank-line"></div>
            </div>

        </div>


        <!-- ===============================
             FAMILY MEMBERS
        ================================ -->
        <div class="section">

            <div class="section-title">
                ১১। পরিবারের সদস্যদের বিবরণ
            </div>


            <table class="family-table">

                <thead>
                    <tr>
                        <th style="width: 8%;">ক্রমিক</th>
                        <th style="width: 28%;">নাম</th>
                        <th style="width: 10%;">বয়স</th>
                        <th style="width: 18%;">সম্পর্ক</th>
                        <th style="width: 18%;">পেশা</th>
                        <th style="width: 18%;">মোবাইল নম্বর</th>
                    </tr>
                </thead>


                <tbody>
                @foreach ($tenant->tenantFamilyMembers as $key => $item)
                    
                
                    <tr>
                        <td>{{$key+1}}</td>
                        <td>{{$item->name}}</td>
                        <td></td>
                        <td>{{$item->relation}}</td>
                        <td></td>
                        <td>{{$item->mobile}}</td>
                    </tr>
                @endforeach
                    {{-- <tr>
                        <td>২</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr> --}}

                    <tr>
                        <td>৩</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td>৪</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                </tbody>

            </table>

        </div>


        <!-- ===============================
             HOUSE OWNER
        ================================ -->
        <div class="section">

            <div class="section-title">
                ১২। বাড়ির মালিকের তথ্য
            </div>


            <div class="double-column">

                <div>

                    <div class="small-field">
                        <div class="label">মালিকের নাম</div>
                        <div class="blank-line"></div>
                    </div>

                    <div class="small-field">
                        <div class="label">মোবাইল নম্বর</div>
                        <div class="blank-line"></div>
                    </div>

                </div>


                <div>

                    <div class="small-field">
                        <div class="label">জাতীয় পরিচয়পত্র নং</div>
                        <div class="blank-line"></div>
                    </div>

                    <div class="small-field">
                        <div class="label">স্থায়ী ঠিকানা</div>
                        <div class="blank-line"></div>
                    </div>

                </div>

            </div>

        </div>


        <!-- ===============================
             DRIVER
        ================================ -->
        <div class="section">

            <div class="section-title">
                ১৩। ড্রাইভারের তথ্য (যদি থাকে)
            </div>


            <div class="double-column">

                <div>

                    <div class="small-field">
                        <div class="label">ড্রাইভারের নাম</div>
                        <div class="blank-line"></div>
                    </div>

                    <div class="small-field">
                        <div class="label">মোবাইল নম্বর</div>
                        <div class="blank-line"></div>
                    </div>

                </div>


                <div>

                    <div class="small-field">
                        <div class="label">জাতীয় পরিচয়পত্র নং</div>
                        <div class="blank-line"></div>
                    </div>

                    <div class="small-field">
                        <div class="label">স্থায়ী ঠিকানা</div>
                        <div class="blank-line"></div>
                    </div>

                </div>

            </div>

        </div>


        <!-- ===============================
             OTHER INFORMATION
        ================================ -->
        <div class="section">


            <div class="full-field">
                <div class="serial">১৪।</div>
                <div class="label">
                    বর্তমান বাসায় বসবাসের তারিখ
                </div>
                <div class="blank-line"></div>
            </div>


            <div class="full-field">
                <div class="serial">১৫।</div>
                <div class="label">
                    পূর্বের বাসস্থানের ঠিকানা
                </div>
                <div class="blank-line"></div>
            </div>


            <div class="full-field">
                <div class="serial">১৬।</div>
                <div class="label">
                    পূর্বের বাড়ির মালিকের নাম ও মোবাইল নম্বর
                </div>
                <div class="blank-line"></div>
            </div>


            <div class="full-field">
                <div class="serial">১৭।</div>
                <div class="label">
                    কোন মামলা/অপরাধে পূর্বে গ্রেফতার বা দণ্ডপ্রাপ্ত হয়েছেন কিনা
                </div>
                <div class="blank-line"></div>
            </div>


            <div class="full-field">
                <div class="serial">১৮।</div>
                <div class="label">
                    কোন অভিযোগে বর্তমানে মামলা বিচারাধীন আছে কিনা
                </div>
                <div class="blank-line"></div>
            </div>

        </div>


        <!-- ===============================
             FOOTER
        ================================ -->
        <div class="footer">

            <div class="date-box">
                <strong>তারিখঃ</strong>
                ....................................
            </div>


            <div class="signature-box">

                <div class="signature-line"></div>

                ভাড়াটিয়ার স্বাক্ষর

            </div>

        </div>


    </div>

</body>

</html>