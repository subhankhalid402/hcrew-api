<!DOCTYPE html>
<html>
<head>
<!--    <script type="text/javascript" src="--><?php //echo base_url('assets/global/plugins/jquery.min.js'); ?><!--"></script>-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <title>Date Wise Report</title>
    <style type="text/css">
        body {
            background: rgb(204, 204, 204);
            background: white;

        }

        * {
            margin: 0px auto;
            padding: 0px;
            font-family: "Calibri";
            font-size: 14px;
            font-weight: bold;
        }

        table {
            border-color: "#E6E6E6";
            font-size: 17px;
            border-top: 3px solid #000;
        }

        .header-title {
            width: 55%;
            text-align: left;
            float: left;
            padding-left: 20px
        }

        td {
            text-align: center;
        }

        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }

        .total {
            text-align: right;
            padding-right: 15px;
            font-weight: bold;
            font-size: 19px;
        }

        .totalprice {
            text-align: left;
            padding-left: 20px;
            width: 100px;
            font-weight: bold;
            font-size: 19px;
        }

        .p {
            font-weight: bold;
            font-size: 14px;

        }

        page {
            background: white;
            display: block;
            margin: 0 auto;
            margin-bottom: 0.5cm;
        }

        page[size="A4"] {
            width: 21cm;
            height: 29.7cm;
        }

        page[size="A4"][layout="landscape"] {
            width: 29.7cm;
            height: 21cm;
        }

        .grandtotal_amount {
            text-align: center;
            padding-left: 10px;
            font-size: 16px !important;
            font-weight: bold;
        }

        @media print {
            body, page {
                margin: 0;
                box-shadow: 0;
            }
        }

        tr {
            page-break-before: always;
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
<?php //echo '<pre>'; print_r($data); die(); ?>
<?php //extract($organization[0]); ?>
<page size="A4" layout="landscape">
    <div id="main_centerBody">
        <table id="tbl">
            <thead>
            <tr>
                <td colspan="2" style="text-align: left;border-right: none;padding-left: 80px;">
{{--                    <img src="<?php echo $setting['logo_url']; ?>"--}}
{{--                         style="width:115px;height:100px;">--}}
                </td>
                <td colspan="6" style="border-left: none;">
                    <div class="header-title">
                        <p style="font-weight:bold;font-size:16px;letter-spacing:4px"><?php echo $setting['company_name']; ?></p>
                        <p class="p"><?php echo $setting['address']; ?></p>
                        <p class="p"><?php echo $setting['phone_no'] . " / " . $setting['email']; ?></p>
                    </div>
                </td>
            </tr>

            <tr style="background-color:#7fffd4;color:#000000;">
                <td style="width: 20px;">#</td>
                <td style="width: 100px;text-align:center">Date</td>
                <td style="width: 250px;">Income Head</td>
                <td style="width: 300px;">Title</td>
                <td style="width: 100px;text-align:center">Client</td>
                <td style="width: 100px;text-align:center">Created By</td>
                <td style="width: 80px;">Amount</td>
                <td style="width: 80px;">Action</td>
            </tr>
            </thead>
            <tbody>
            <?php
            if (empty($income)) {
                ?>
                <tr style="background:#ffd700;">
                    <td colspan="8">
                        No record found.
                    </td>
                </tr>
                <?php
            } else {
                $GrandTotal = 0;
                foreach ($income as $income_value) {
                    $totalprice = $serielNumber = 0;
                    ?>
                    <tr>
                        <td colspan="8"
                            style="text-align: left !important; background-color:darkorange;">Date: <b><?php echo date($income_value[0]['income_date_formatted']); ?></b></td>
                    </tr>
                    <?php
                    foreach ($income_value as $in) {
                        $totalprice += $in['amount'];
                        $Amount = $in['amount'] + 0;
                        $GrandTotal += $Amount;
                        $id = $in['id'];
                        ?>
                        <tr>
                            <td><?php echo ++$serielNumber; ?></td>
                            <td><?php echo $in['income_date_formatted']; ?></td>
                            <td><?php echo $in['income_head']['name']; ?></td>
                            <td><?php echo $in['notes']; ?></td>
                            <td><?php echo $in['client']['name']; ?></td>
                            <td><?php echo $in['created_user']['username']; ?></td>
                            <td><?php echo $in['amount_formatted']; ?></td>
                            <?php echo "<td style='color:red' class='remove' in_id=$id>Delete</td>"; ?>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <td class="total" colspan="6">Total:</td>
                        <td class="totalprice" colspan="2"><?php echo number_format($totalprice, 2, '.', ',') ?>/-</td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td class="total" colspan="6">Grand Total:</td>
                    <td class="totalprice" colspan="2"><?php echo number_format($GrandTotal, 2, '.', ','); ?>/-</td>
                </tr>
                <?php
            }

            ?>
            </tbody>
        </table>
    </div>
</page>
<script type="text/javascript">
    api_url = "{{env('API_URL')}}";
    $(document).on('click', '.remove', function () {
        var r = confirm("Are you sure want to delete this Row?");
        if (r == true) {
            in_id = $(this).attr('in_id');
            alert(api_url+`incomes/${in_id}/delete`)
            thisRow = $(this);
            $.ajax({
                type: "POST",
                url: api_url+`incomes/${in_id}/delete`,
                // data: {"in_id": in_id},
                dataType: "JSON",
                success: function (returnData) {
                    if (returnData.status == 'true') {
                        $(thisRow).parents('tr').remove();
                    } else {
                        alert("Unable to delete the record");
                    }

                },
                error: function (returnData) {
                    alert("Unable to delete the record");
                },

            });
        }
    });

    window.onbeforeprint = function () {
        $('.remove').text('');
    };
</script>
</body>
</html>
