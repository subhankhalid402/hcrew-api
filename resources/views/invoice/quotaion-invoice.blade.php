<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Sale Invoice</title>
    <style>
        body {
            width: 100%;
            /*height: 100%;*/
            margin: 0;
            padding: 0;
            margin-left: -20px;
            background-color: #fff;
            font: 9pt 'arial', sans-serif;
        }

        * {
            box-sizing: border-box;
            -moz-box-sizing: border-box;
            /*font-family: 'Arialmt';*/
            /*font-family: 'Cambria';*/
            font-family: 'arial', sans-serif;
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
            /*text-align: left;*/
            /*border-spacing: 0;*/
            border-collapse: collapse;
            text-align: start;
        }

        .maintable {
            /*border: 1px solid #000;*/
        }

        .maintable th {
            /*border-top: 1px solid #000 !important;*/
            border-bottom: 1px solid #000 !important;
        }

        .maintable th {
            line-height: 10px;
            color: #000000;
            font-family: "arialmt", sans-serif;
            font-style: normal;
            text-decoration: none;
            font-size: 11pt !important;
            font-weight: bold !important;
            padding-bottom: 8px !important;
        }

        .maintable td {
            /*border: 1px solid #000;*/
            border-bottom: 1px solid #ccc;
            padding-left: 8px;
            font-family: "arialmt", sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 10pt;
            /*text-align: left;*/
        }

        p {
            margin: 0 !important;
            color: black;
            font-family: 'arial', sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 10pt !important;
        }

        .text-right {
            text-align: right !important;
        }

        .text-left {
            text-align: left !important;
        }


        .text-center {
            text-align: center !important;
        }


        h5 {
            margin-top: 0px !important;
            margin-bottom: 5px !important;
        }

        .purchase-order-td p {
            margin-bottom: 10px !important;
        }

        .purchase {
            font-size: 35px !important;
        }

        hr {
            width: 59% !important;
            text-align: right;
            border: 1px dotted #cccccc;
            float: right;
        }

        h3 {
            margin: 0 !important;
        }

        .pur-order {
            margin-top: 30px !important;
            margin-bottom: 30px !important;
        }

        .fw-700 {
            font-weight: bold !important;
        }

        .footer {
            /*margin-top: 40px !important;*/
            /*margin-bottom: 40px !important;*/
            border-top: 1px solid #000000;
            /*padding-top: 30px !important;*/
        }

        .para {
            /*margin: 1px !important;*/
            /*padding: 1px !important;*/
            font-size: 0.7rem !important;
        }

        .footer .w-40 {
            width: 40% !important;
        }

        .footer .br-left {
            border-left: 1px solid #000000;
            padding-left: 12px !important;
        }
    </style>
</head>
<body>
<div class="book">
    <div class="page">
        <div class="subpage">
            <div class="header">
                <table>
                    <tbody>
                    <tr>
                        <td>
                            <div
                                style="font-size: 2px; padding-top:10px">@php echo str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', QrCode::size(100)->generate(env('BASE_URL') . 'public/uploads/invoice_pdf/quotation_invoice/QUT-INV-' . $data->id . '.pdf')); @endphp
                            </div>
                        </td>
                        <td align="right">
                            <img src="{{$data->setting->logo_url}}" alt="" width="100">
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="pur-order">
                <table>
                    <tbody>
                    <tr>
                        <td style="width: 50%; margin-top: 20%;">
                            <p class="purchase" style="margin-bottom: 20px !important;">
                                @if($is_invoice == 'YES')
                                    INVOICE
                                @else
                                    QUOTATION
                                @endif
                            </p>
                            <br>
                            <table>
                                <tr>
                                    <td>
                                        {{$data->name}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        {{$data->address}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        TRN: {{$data->trn}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Email: {{$data->email}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Phone No: {{$data->phone_no}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Requested by: {{ $data->requested_by ?? '' }}
                                    </td>
                                </tr>


                            </table>
                        </td>
                        <td style="width: 30%;" align="left">
                            <table>
                                <tr>
                                    <td>
                                        {{ $is_invoice == 'YES' ? 'Invoice' : 'Quote'}} Date : {{ $data->created_at_formatted }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        {{ $is_invoice == 'YES' ? 'Invoice' : 'Quote'}} No : {{ $is_invoice == 'YES' ? $data->invoice_no : $data->quotation_no }}
                                    </td>
                                </tr>
                                @if($is_invoice == 'YES' && isset($data->quotation))
                                    <tr>
                                        <td>
                                            Quote No : {{ $data->quotation->quotation_no }}
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </td>
                        <td style="width: 20%;">
                            <table>
                                <tbody>
                                <tr>
                                    <td>
                                        <p>{{$data->setting->company_name}}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p>{{$data->setting->address}}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        TRN: {{$data->setting->tax_number}}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <br>
            <br>
            <br>
            <br>
            <div>
                <table class="maintable">
                    <thead>
                    <tr>
                        <th class="text-left" style="width: 10%">Sr#</th>
                        <th class="text-left" style="width: 35%">Description</th>
                        <th class="text-right" style="width: 10%">Quantity</th>
                        <th class="text-right" style="width: 15%">Unit Price</th>
                        <th class="text-right" style="width: 10%">Tax</th>
                        <th class="text-right" style="width: 20%">Amount <br> <br> {{ $data->currency->name }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php $count = 1; @endphp
                    @foreach($data->details as $detail)
                        <tr>
                            <td style="width: 7%">
                                {{ $count++ }}
                            </td>
                            <td style="width: 30%">
                                {!! $detail->description !!}
                            </td>
                            <td class="text-right">
                                {{$detail->quantity}}
                            </td>
                            <td class="text-right">
                                {{$detail->unit_price}}
                            </td>
                            <td class="text-right">
                                @if(is_numeric($detail->vat) || is_float($detail->vat))
                                    {{$detail->vat}} %
                                @else
                                    {{$detail->vat}}
                                @endif
                            </td>
                            <td class="text-right">
                                {{ \App\Helpers\SiteHelper::amountFormatter($detail->amount) }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div>
                <table>
                    <tbody>
                    <tr>
                        <td align="right" style="width: 70%">
                            <p>SUB TOTAL</p>
                        </td>
                        <td style="width: 30%" class="text-right">
                            <p>
                                {{ \App\Helpers\SiteHelper::amountFormatter($data->sub_total) }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" style="width: 70%">
                            <p>DISCOUNT</p>
                        </td>
                        <td style="width: 30%" class="text-right">
                            <p>
                                {{$data->discount_amount}}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" style="width: 70%">
                            <p>TOTAL TAX ON PURCHASES </p>
                        </td>
                        <td style="width: 30%" class="text-right">
                            <p>
                                {{ $data->details->sum('vat_amount') }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <hr>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" style="width: 70%">
                            <h3 style="font-size: 16px;">TOTAL {{ $data->currency->name }}</h3>
                        </td>
                        <td style="width: 30%" class="text-right">
                            <h3 style="font-size: 16px;">
                                {{$data->total_amount_formatted}}
                            </h3>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <hr>
                        </td>
                    </tr>
                    </tbody>
                </table>


            </div>

            <htmlpagefooter name="page-footer">
                <div class="footer">
                    <table class="">
                        <tbody>
                        <tr>
                            <td style="width: 20%; font-size: 8px; line-height: 10px">
                                Bank Account Detail:
                                <br>
                                Bank: Emirates NBD Bank PJSC
                                <br>
                                Account No: 101 451 802 620 1
                                <br>
                                IBAN: AE 450 260 001 014 518 026 201
                                <br>
                                Swift: EBILAEAD
                                <br>
                                Account: White Horizon Technical Works LLC

                            </td>
                            <td style="width: 70%">
                                @if($data->terms_and_conditions)
                                    {{ $data->terms_and_conditions }}
                                @else
                                    {{ $data->setting->terms_and_conditions }}
                                @endif
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </htmlpagefooter>

            <!--End of subpage-->
        </div>
    </div>
</div>
</body>
</html>
