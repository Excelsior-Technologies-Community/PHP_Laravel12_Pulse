<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Pulse Export</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: "Segoe UI", sans-serif;
        }

        .export-card {
            width: 100%;
            max-width: 700px;
            background: #fff;
            border-radius: 20px;
            padding: 45px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, .25);
            text-align: center;
        }

        .icon-box {
            width: 90px;
            height: 90px;
            margin: auto;
            border-radius: 50%;
            background: #198754;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 40px;
            margin-bottom: 25px;
        }

        h1 {
            font-weight: bold;
            color: #212529;
        }

        p {
            color: #6c757d;
            font-size: 17px;
        }

        .btn-export {
            padding: 14px 45px;
            font-size: 18px;
            border-radius: 50px;
            font-weight: 600;
            transition: .3s;
        }

        .btn-export:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(25, 135, 84, .35);
        }

        .info-box {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 18px;
            margin-top: 35px;
            text-align: left;
        }

        .info-box ul {
            margin: 0;
            padding-left: 18px;
        }

        .footer {
            margin-top: 30px;
            color: gray;
            font-size: 14px;
        }
    </style>

</head>

<body>

    <div class="export-card">

        <div class="icon-box">
            <i class="fas fa-file-csv"></i>
        </div>

        <h1>Laravel Pulse Report Export</h1>

        <p>
            Export your Laravel Pulse monitoring data into a CSV file with one click.
            This report can be used for analysis, auditing, and record keeping.
        </p>

        <div class="mt-4">

            <a href="{{ route('pulse.export.csv') }}"
                class="btn btn-success btn-lg btn-export">

                <i class="fas fa-download me-2"></i>

                Export CSV Report

            </a>

        </div>

        <div class="info-box">

            <h5 class="mb-3">
                <i class="fas fa-circle-info text-primary"></i>
                Export Information
            </h5>

            <ul>
                <li>Exports all Pulse monitoring records.</li>
                <li>Downloads the report in CSV format.</li>
                <li>Easy to open in Excel or Google Sheets.</li>
                <li>One-click export with no additional configuration.</li>
            </ul>

        </div>

        <div class="footer">

            Laravel Pulse Monitoring System

        </div>

    </div>

</body>

</html>