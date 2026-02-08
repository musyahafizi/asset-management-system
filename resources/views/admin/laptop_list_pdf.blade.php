<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #214761; color: white; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $title }}</h2>
        <p>Generated on: {{ $date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Model</th>
                <th>Serial Number</th>
                <th>Assigned User</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laptops as $index => $laptop)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $laptop['model'] }}</td>
                <td>{{ $laptop['serial_number'] }}</td>
                <td>{{ $laptop['firstname'] }}</td>
                <td>{{ $laptop['status'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Page printed by Asset Management System
    </div>
</body>
</html>