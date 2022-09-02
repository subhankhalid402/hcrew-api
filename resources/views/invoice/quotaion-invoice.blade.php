<!DOCTYPE html>
<!-- saved from url=(0108)http://localhost:8000/report-print/2124900004?header=true&footer=true&electronically_verified=true&tests=6_0 -->
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Mauzoun QT2021MMDDNN Client</title>
    <style>
        @font-face {
            font-family: 'Arialmt';
            src: url('vendor/mpdf/ttfonts/arial-mt/arialmt.ttf');
        }

        @font-face {
            font-family: 'Cambria';
            src: url('vendor/mpdf/ttfonts/cambria/Cambria.ttf');
        }

        @font-face {
            font-family: 'Helvetica';
            src: url('vendor/mpdf/ttfonts/Helvetica/Helvetica.ttf');
        }

        body {
            width: 100%;
            /*height: 100%;*/
            margin: 0;
            padding: 0;
            margin-left: -20px;
            background-color: #fff;
            font: 9pt "Helvetica";
        }

        * {
            box-sizing: border-box;
            -moz-box-sizing: border-box;
            /*font-family: 'Arialmt';*/
            /*font-family: 'Cambria';*/
            font-family: 'Helvetica';
            font-weight: 900;
            font-size: 12px !important;
        }

        .page {
            width: 190mm;
            /*height: 400mm;*/
            /*min-height: 297mm;*/
            /*padding: 5mm; /*20mm*/
            margin: 10mm auto;
            margin-top: 7px;
        }

        .subpage {
            /*height: 287mm;*/
            /*padding: 5px;*/
        }

        @page {
            header: page-header;
            footer: page-footer;
        }

        table {
            width: 100%;
            line-height: 16pt;
            text-align: left;
            /*border-spacing: 0;*/
            /*border-collapse: collapse;*/
        }

        .maintable {
            /*border: 1px solid #000;*/
        }

        .maintable th {
            /*border: 1px solid #000;*/
            line-height: 10px;
            /*font-weight: 500;*/
            text-align: left;
            padding: 8px;
            background-color: #5d7657;
            color: #fff;
            font-family: "Helvetica", sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 11pt;
        }

        /*.client-text{*/
        /*    !*padding: 150px !important;*!*/
        /*    !*margin-bottom: 5px !important;*!*/
        /*    !*font-weight: 800;*!*/
        /*    font-size: 24px !important;*/

        /*}*/

        .maintable td {
            /*border: 1px solid #000;*/
            padding-left: 8px;
            background-color: #f9f1dd;
            font-family: "Helvetica", sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 10pt;
            /*text-align: left;*/
        }

        .client-info-wrapper {
            /*display: flex;*/
            width: 100%;
            /*justify-content: space-between;*/
        }

        .client-info-left {
            width: 40%;
            float: left;
        }

        .client-info-right {
            width: 50%;
            float: right;
        }

        .text-right {
            text-align: right !important;
        }

        .text-left {
            text-align: left !important;
        }

        .m-0 {
            margin-top: 0px !important;
            margin-bottom: 0px !important;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            width: 900px;
            margin-top: 100px !important;
        }

        p {
            margin: 0px !important;
            color: black;
            font-family: Helvetica, serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 10pt !important;
        }


        ol li {
            font-weight: 100;
        }

        .fs-10 {
            font-size: 10px !important;
        }

        .w-110 {
            width: 110px;
        }

        .w-100 {
            width: 150px !important;
        }

        .w-60 {
            width: 60px !important;
        }

        .w-520 {
            width: 520px !important;
        }

        .invoice {
            font-size: 20px !important;
            text-align: right !important;
        }

        .contract-title {
            font-size: 40px !important;
            border-bottom: 3px solid #9c27b0;
            margin-top: 60px !important;
            margin-bottom: 60px !important;
        }

        .empty-td {
            height: 20px !important;
            background-color: #fff !important;
        }
    </style>
</head>
<body>

@php
    function amount_formatted($currency, $amount){
    if ($currency['position'] == 'left'){
        return $currency['symbol'] . ' ' . round($amount, 2);
    }else{
        return round($amount , 2) . ' ' . $currency['symbol'];
    }
}

@endphp
<div class="book">
    <div class="page">
        <div class="subpage">
            <table>
                <tbody>
                <tr>
                    <td>
                        <img src="{{$data->setting->logo_url}}" class="w-100">
                        <br>
                        <br>
                        <div class="company-info">
                            <p>Company Name: [{{$data->setting->company_name}}]</p>
                            <p>Address: [{{$data->setting->address}}]</p>
                            <p>Phone No: [{{$data->setting->phone_no}}]</p>
                            <p>Email: [{{$data->setting->email}}] | Website: [{{$data->setting->website}}]</p>
                        </div>
                    </td>
                    <td align="right">
                        <p class="invoice">QUOTATION</p>
                        <br>
                        <br>
                        <div class="quatition-info">
                            <p>Qutotation No: [{{$data->quotation_no}}]</p>
                            <p>Reference No: [{{$data->reference_no}}]</p>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>

            <br>
            <br>
            <div class="client-info-wrapper">
                <div class="client-info-left">
                    <table class="maintable">
                        <thead>
                        <tr>
                            <th>
                                <h4 class="client-text">Quotation Information:</h4>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="empty-td"></td>
                        </tr>
                        <tr>
                            <td>
                                <p>Name: [{{$data->name}}]</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p>Address: [{{$data->address}}]</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p>Contact No: [{{$data->phone_no}}]</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p>Email: [{{$data->email}}]</p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="client-info-right">
                    <table class="maintable">
                        <thead>
                        <tr>
                            <th>
                                <h4>Quotation Notes:</h4>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                       <tr>
                           <td class="empty-td"></td>
                       </tr>
                        <tr>
                            <td>
                                <p>Notes: [{{ strip_tags(htmlspecialchars_decode($data->customer_notes)) }}]</p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    {{--                    <table class="maintable">--}}
                    {{--                        <thead>--}}
                    {{--                        <tr>--}}
                    {{--                            <th>--}}
                    {{--                                <h4>Quotation Notes:</h4>--}}
                    {{--                            </th>--}}
                    {{--                        </tr>--}}
                    {{--                        </thead>--}}
                    {{--                        <tbody>--}}
                    {{--                        <tr>--}}
                    {{--                            <td class="empty-td"></td>--}}
                    {{--                        </tr>--}}
                    {{--                        <tr>--}}
                    {{--                            <td>--}}
                    {{--                                <p>Notes : [{!! $data->customer_notes !!}]</p>--}}
                    {{--                            </td>--}}
                    {{--                        </tr>--}}
                    {{--                        </tbody>--}}
                    {{--                    </table>--}}
                </div>
            </div>
            <div style="clear: both"></div>
            <br>
            <div class="contract-title-wrapper">
                <h3>Construction Materials:</h3>
            </div>
            <table class="maintable">
                <thead>
                <tr>
                    <th>#</th>
                    <th>From Date</th>
                    <th>To Date</th>
                    <th>Description</th>
                    <th>Amount</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="empty-td" colspan="5"></td>
                </tr>
                @php $count = 0; @endphp
                @foreach($data->quotation_details as $quotation_detail)
                    @php $count++; @endphp
                    <tr>
                        <td class="w-60">{{$count}}</td>
                        <td class="w-110">
                            <p id="notes">{{$quotation_detail->from_date_formatted}}</p>
                        </td>
                        <td class="w-110">
                            <p id="notes">{{$quotation_detail->to_date_formatted}}</p>
                        </td>
                        <td class="w-320">
                            <p id="notes">{!! $quotation_detail->description !!}</p>
                        </td>

                        <td class="w-110">
                            <p>{{amount_formatted($data->currency, $quotation_detail->amount)}}</p>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <br>
            <br>

            <div class="client-info-wrapper">
                <div class="client-info-left">
                    {{--                    <table class="maintable">--}}
                    {{--                        <thead>--}}
                    {{--                        <tr>--}}
                    {{--                            <th colspan="2">--}}
                    {{--                                <h4 class="client-text">Tax:</h4>--}}
                    {{--                            </th>--}}
                    {{--                        </tr>--}}
                    {{--                        </thead>--}}
                    {{--                        <tbody>--}}
                    {{--                        <tr>--}}
                    {{--                            <td>--}}
                    {{--                                <p>GST</p>--}}
                    {{--                            </td>--}}
                    {{--                            <td>--}}
                    {{--                                <p> 17%</p>--}}
                    {{--                            </td>--}}
                    {{--                        </tr>--}}
                    {{--                        </tbody>--}}
                    {{--                    </table>--}}
                    <br>
                    <br>
                    <div class="signature">
                        <h4>Signature: <span style="border-bottom: 1px solid #000"><img
                                    src="{{$data->setting->signature_url}}" class="w-100" alt=""></span></h4>
                        {{--                        <h4>--}}
                        {{--                            Date:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>_________________________</span>--}}
                        {{--                        </h4>--}}
                    </div>
                </div>
                <div class="client-info-right">
                    <table class="maintable">
                        <thead>
                        <tr>
                            <th colspan="2">
                                <h4>Amount Due:</h4>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <p>Subtotal</p>
                            </td>
                            <td>
                                <p>{{amount_formatted($data->currency, $data->sub_total)}}</p>
                            </td>
                        </tr>
                        {{--                        <tr>--}}
                        {{--                            <td>--}}
                        {{--                                <p>Tax @ Total</p>--}}
                        {{--                            </td>--}}
                        {{--                            <td>--}}
                        {{--                                <p>$2860</p>--}}
                        {{--                            </td>--}}
                        {{--                        </tr>--}}
                        <tr>
                            <td>
                                <p>Total</p>
                            </td>
                            <td>
                                <p>{{amount_formatted($data->currency, $data->sub_total)}}</p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div style="clear: both"></div>

            <!--  -->
            <!--End of subpage-->
        </div>
    </div>
</div>
</body>
</html>
