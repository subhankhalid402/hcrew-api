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
            margin-top: 40px !important;
            margin-bottom: 40px !important;
            border-top: 1px solid #000000;
            padding-top: 30px !important;
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
                        <td align="left" style="width: 50%; margin-top: 20%;">
                            <p class="purchase" style="margin-bottom: 20px !important;">QUOTATION</p>
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
                                        TRN:
                                    </td>
                                    <td>
                                        232342342412
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Email:
                                    </td>
                                    <td>
                                        {{$data->email}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Phone No:
                                    </td>
                                    <td>
                                        {{$data->phone_no}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Requested by:
                                    </td>
                                    <td>
                                        {{ $data->requested_by ?? '' }}
                                    </td>
                                </tr>


                            </table>
                        </td>
                        <td style="width: 50%;" align="right">
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
                                        Tax Number: <p>{{$data->setting->tax_number}}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p>{{$data->created_at}}</p>
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
                        <th class="text-left">Sr#</th>
                        <th class="text-left">Description</th>
                        <th class="text-right">Quantity</th>
                        <th class="text-right">Unit Price</th>
                        <th class="text-right">Tax</th>
                        <th class="text-right">Amount AED</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php $count = 1; @endphp
                    @foreach($data->quotation_details as $quotation_detail)
                        <tr>
                            <td style="width: 7%">
                                {{ $count++ }}
                            </td>
                            <td style="width: 30%">
                                {!! $quotation_detail->description !!}
                            </td>
                            <td class="text-right">
                                {{$quotation_detail->quantity}}
                            </td>
                            <td class="text-right">
                                {{$quotation_detail->unit_price}}
                            </td>
                            <td class="text-right">
                                {{$quotation_detail->vat}} %
                            </td>
                            <td class="text-right">
                                {{$quotation_detail->amount}}
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
                                {{$data->sub_total}}
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
                                {{ $data->quotation_details->sum('vat_amount') }}
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
                            <h3 style="font-size: 16px;">TOTAL AED</h3>
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
                    <p style="font-size: 20px !important;margin-bottom: 10px !important;">MORE DETAILS</p>
                    <table>
                        <tbody>
                        <tr>
                            <td class="w-40">
                                <p>Bank Account Detail:</p>
                                <p>Bank: Emirates NBD Bank PJSC</p>
                                <p>Account No: 101 451 802 620 1</p>
                                <p>IBAN: AE 450 260 001 014 518 026 201</p>
                                <p>Swift: EBILAEAD</p>
                                <p>Account: White Horizon Technical Works LLC</p>
                            </td>
                            <td class="br-left">
                                <p class="fw-700">Attention</p>
                                <p class="fw-700">Telephone</p>
                                <p>
                                    {{$data->setting->phone_no}}
                                </p>
                            </td>
                            <td class="br-left">
                                <p class="fw-700">Delivery Instructions</p>
                                <p>Yas Circuit Abu Dhabi</p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <br>
                <br>

                <div class="end">
                    @if($data->terms_and_conditions)
                        <p>{!! $data->terms_and_conditions !!} sd</p>
                    @else
                        <p>{!! $data->setting->terms_and_conditions !!} aw</p>
                    @endif
                </div>
                <br>
                <br>
            </htmlpagefooter>

            <!--End of subpage-->
        </div>
    </div>
</div>
</body>
</html>
