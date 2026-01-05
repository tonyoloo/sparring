<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }

        .report {
            width: 100%;
            max-width: 180mm;
            margin: 20px auto;
            padding: 12mm;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .header-table td {
            font-size: 12px;
            vertical-align: middle;
        }

        .header-table .logo {
            text-align: center;
        }

        .header-table .logo img {
            height: 80px;
            width: 80px;
        }

        .section_details {
            width: 100%;
            
            border-radius: 6px;
            border-collapse: collapse;
            overflow: hidden;
            margin-bottom: 20px;
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.15);
        }

        .section_details {
    width: 100%; /* Ensure full width */
    max-width: 800px; /* Adjust width to avoid excessive stretching */
    margin: auto; /* Center the table */
    border-collapse: collapse; /* Fix spacing issues */
}

        .section_details th {
            background-color:rgb(6, 136, 8);
            color: white;
            text-align: center;
            font-weight: bold;
            padding: 10px;
            font-size: 14px;
        }

        .section_details td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        .field_headings {
            background-color: #e9ecef;
            font-weight: bold;
        }

        .data_rows {
            background-color: #ffffff;
        }

        .data_rows:nth-child(even) {
            background-color: #f8f9fa;
        }

        @media print {
            .report {
                max-width: 190mm;
                margin: 0;
                padding: 5mm;
            }

            .section_details {
                box-shadow: none;
            }
        }
    </style>
</head>

<body>
    <table class="header-table">
        <tr>
            <td style="text-align: left;">
                <p>Anniversary Towers 18<sup>th</sup> Floor, University Way<br>
                    P.O. Box 69489-00400, NAIROBI, KENYA<br>
                    Telephone: +254 020 2278000<br>
                    Mobile: 0711052000<br>
                    Email: lending@helb.co.ke</p>
            </td>
            <td class="logo">
                <img src="./paper/img/logo.png" alt="HELB Logo">
                <h4 style="margin-top: 5px;">HIGHER EDUCATION LOANS BOARD</h4>
            </td>
            <td style="text-align: right;">
                <strong>HELB ACT (1995) CAP213A</strong>
            </td>
        </tr>
    </table>

    <div >
        @php
        $sections = [
        ['title' => 'Personal Details', 'data' => [['SurName', 'Middle Name', 'Last Name', ''], [$first_name, $mid_name, $last_name, ''], ['ID/No', 'Email', 'Portal Mobile No.', ''], [$id_no, $email_add, $cell_phone, '']]],

        ['title' => 'Application Details', 'data' => [['Serial', 'Date Created', 'Loan applied', 'Loan date'], [$serial_number, $date_created , $submittedloan_status, $date_loan_submit], ['Scholarship Applied', 'Scholarship Date', 'Disbursement Option', 'Disbursement Value'], [$submittedscholarship_status ,$date_sch_submit, $disbursementoption, $disbursementoptionvalue]]],

        ['title' => 'University Details', 'data' => [['University Name', 'Admission Number', 'Year of Admission', 'University Code'], [$InstitutionName, $ADMISSIONNUMBER, $ADMISSIONYEAR, $INSTITUTIONCODE]]],
        ['title' => 'Mobile App Details', 'data' => [['CellPhone', 'GSF', 'Network Used', 'Sim Operator Name'], [$androidcellphone, $androidgsf, $androidnetworkused, $androidsimoperatorname], ['Android OS Serial', 'IMEI', 'Device Info', 'Date Created'], [$oserial, $androidimei, $androideviceinfo, $androidtime]]],
        ['title' => 'USSD Details', 'data' => [['UserName', 'Date Created', 'Sim ID', 'User Status'], [$ussdphone, $ussdatecreated, $ussdsimid, $ussdstatus]]],
        ['title' => 'Mini App Details', 'data' => [['CellPhone', 'Platform', 'Brand', 'App Version'], [$miniphone, $miniphoneplatform, $miniphonebrand, $miniphoneappversion], ['System', 'Device Info', 'Date Created','System'], [$miniphonesystem, $minideviceinfo, $minitime_added,$miniphonesystem]]],
        ];
        @endphp

        @foreach ($sections as $section)
        <table class="section_details">
            <tr>
                <th colspan="4">{{ $section['title'] }}</th>
            </tr>
            @foreach ($section['data'] as $index => $row)
            <tr>
                @foreach ($row as $cell)
                <td class="{{ $index % 2 == 0 ? 'field_headings' : 'data_rows' }}">{{ $cell }}</td>
                @endforeach
            </tr>
            @endforeach
        </table>
        @endforeach
    </div>
</body>

</html>
