<!DOCTYPE html>
<html>
<head>
    <title>বসবাকারিদের তালিকা</title>

    <style>
        @font-face {
            font-family: 'NikoshBAN';
            src: url('/fonts/NikoshBAN.ttf') format('truetype');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }
        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm;
            }
        }

        body {
            font-family: 'NikoshBAN', sans-serif;
            font-size: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
        }

        th {
            background: #eee;
        }

         .header {
            position: relative;
            text-align: center;
        }

        .header-left {
            position: absolute;
            left: 0;
            top: 0;
        }
        .logo {
            height: 60px;
        }


        .title h2 {
            margin: 0;
            font-size: 22px;
        }

        .title p {
            margin: 2px 0;
            font-size: 18px;
        }
        .text-left {
            text-align: left;
        }
        
         .summary-box {
            position: absolute;
            right: 0;
            top: 0;
            border: 1px solid #000;
            padding: 3px 5px;
            font-size: 18px;
            text-align: left;
            min-width: 100px;
        }

        .summary-box p {
            margin: 2px 0;
            margin-bottom: 4px;
            /* border-bottom: 1px solid #000; */
        }
    </style>

    @php
        // use Rakibhstu\Banglanumber\NumberToBangla;
        // $numto = new NumberToBangla();

        function en2bn($number): string
        {
            $en = ['0','1','2','3','4','5','6','7','8','9','January','February','March','April','May','June','July','August','September','October','November','December'];
            $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯','জানুয়ারি','ফেব্রুয়ারি','মার্চ','এপ্রিল','মে','জুন','জুলাই','আগস্ট','সেপ্টেম্বর','অক্টোবর','নভেম্বর','ডিসেম্বর'];
            return str_replace($en, $bn, $number);
        }
        $statusText = [
            'pending' => 'পেন্ডিং',
            'in_progress' => 'চলমান',
            'completed' => 'সম্পন্ন',
        ];
    @endphp
</head>
<body>
<div class="header">
    <img src="{{ asset('images/brands/logo.png') }}" class="logo">

    <div class="title">
        <h2>ঢাকা ক্যান্টনমেন্ট বোর্ড</h2>
         <p>বসবাসকারীদের তথ্য </p>
       {{-- <p>
            <b style="text-decoration: underline;">অর্থ বছরঃ {{en2bn($fiscal)}} ইং</b>
        </p> --}}
    </div>
    <div class="summary-box">
        <table>
            <tr>
                <td>সর্বমোট প্লট:  </b></td>
                <td>{{ en2bn($totalPlots) }} টি</td>
            </tr>
            <tr>
                <td><b>সর্বমোট ফ্ল্যাট: </b></td>
                <td>{{ en2bn($totalFlats) }} টি</td> 
            </tr>
             <tr>
                <td><b>নিজ বসতি:</b></td>
                <td>{{ en2bn($ownerFlats) }} টি</td>
            </tr>
            <tr>
                <td><b>ভাড়াটিয়া:   </b></td>
                <td>{{ en2bn($tenantFlats) }} টি</td>
            </tr>
            {{-- <tr>
                <td><b>Total :   </b></td>
                <td>{{ $present + $late + $leave + $absent}}</td>
            </tr> --}}
        </table>
       
    </div>
</div>

{{-- <h3>Monthly Attendance Report</h3> --}}

<table style="margin-top: 22px;">
    <thead>
        <tr>
            <th>#.</th>
            <th>নাম</th>
            <th>পিতার নাম</th>
            <th>এন আইডি</th>
            <th>মোবাইল</th>
            <th>প্লট</th>
            <th>ফ্ল্যাট</th>
            {{-- <th>অবস্থা</th> --}}
            <th>বসবাসের সময়</th>
        </tr>
    </thead>

    <tbody>
       @foreach($records as $key => $record)
       
        
        <tr>
            <td>{{ en2bn($key + 1) }}</td>
            <td class="text-left">
               <b>{{ $record->tenant_name }}</b>
            </td>

            <td>{{ $record->father_name  }}</td>

            <td>{{ en2bn($record->nid_no)  }}</td>

            <td>{{ en2bn($record->mobile)  }}</td>

            <td>{{ en2bn($record->currentAgreement->occupancy->flat->floor->building->plot->plot_no)  }}</td>

            <td>{{ en2bn($record->currentAgreement->occupancy->flat->flat_no)  }}</td>

            <td><b>{{ en2bn(\Carbon\Carbon::parse($record->currentAgreement->occupancy->start_date)->format('d-m-Y')) . 
            ' ইং থেকে ' . en2bn(\Carbon\Carbon::parse($record->currentAgreement->occupancy->end_date)->format('d-m-Y')) . ' ইং' }}</b></td>

            {{-- <td><b>{{ $statusText[$record->status] ?? '-' }}</b></td> --}}

        </tr>
        @endforeach
    </tbody>
</table>

<script>
    window.onload = function () {
        window.print();
        // window.onafterprint = function () {
        //     window.close();
        // };

    }
</script>

</body>
</html>