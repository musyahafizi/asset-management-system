<!DOCTYPE html>
<html>
<head>
    <title>Laptop Asset Report</title>
    <style>
        body { font-family: sans-serif; }
        .header { text-align: center; border-bottom: 2px solid #214761; padding-bottom: 10px; }
        .details { margin-top: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laptop Asset Details</h1>
        <p>Generated on: {{ $date }}</p>
    </div>

    <div class="details">
        <table>
            <tr> <th>Asset Tag</th> <td>{{ $asset_tag }}</td> </tr>
            <tr> <th>Model</th> <td>{{ $model }}</td> </tr>
            <tr> <th>Serial Number</th> <td>{{ $serial_no }}</td> </tr>
            <tr> <th>Assigned To</th> <td>{{ $userid }}</td> </tr>
        </table>
    </div>
</body>
</html>