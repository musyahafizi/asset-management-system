<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Batch Laptops</title>
    <link href="{{ asset('assets/css/bootstrap.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/font-awesome.css') }}" rel="stylesheet" />
    <style>
        body { background: #f4f7f6; padding: 20px; }
        .card { background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin: auto; }
        h3 { color: #214761; margin-top: 0; }
        .table-batch input { border: none; background: transparent; width: 100%; }
        .table-batch input:focus { background: #f1f1f1; outline: none; }
    </style>
</head>
<body>
    <div class="container" style="padding-top: 20px;">
        <div class="card" style="max-width: 900px;">
            <h3><i class="fa fa-layer-group"></i> Add New Batch</h3>
            <p class="text-muted">Register multiple laptops under one batch ID.</p>
            <hr>
            
            <form action="{{ route('admin.batch.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Batch Number / ID</label>
                        <input type="text" name="batchno" class="form-control" placeholder="e.g. BATCH-001" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Batch Description</label>
                        <input type="text" name="description" class="form-control" placeholder="New Hiring Feb 2026" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Date Received</label>
                        <input type="date" name="date_received" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <h4 class="mt-4">Laptop List</h4>
                <table class="table table-bordered table-batch" id="laptopTable">
                    <thead>
                        <tr class="bg-light">
                            <th>Asset Tag</th>
                            <th>Brand</th>
                            <th>Model</th>
                            <th>Serial Number</th>
                            <th width="50"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" name="laptops[0][asset_tag]" required placeholder="LPT-001"></td>
                            <td><input type="text" name="laptops[0][brand]" required placeholder="Dell"></td>
                            <td><input type="text" name="laptops[0][model]" required placeholder="Latitude"></td>
                            <td><input type="text" name="laptops[0][serial_no]" required placeholder="SN12345"></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-sm btn-info" onclick="addRow()"><i class="fa fa-plus"></i> Add Another Row</button>

                <hr>
                <div class="text-right">
                    <a href="{{ route('admin.laptop') }}" class="btn btn-default">Cancel</a>
                    <button type="submit" class="btn btn-primary" style="background: #214761;">Save Entire Batch</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let rowCount = 1;
        function addRow() {
            const table = document.getElementById('laptopTable').getElementsByTagName('tbody')[0];
            const newRow = table.insertRow();
            newRow.innerHTML = `
                <td><input type="text" name="laptops[${rowCount}][asset_tag]" required></td>
                <td><input type="text" name="laptops[${rowCount}][brand]" required></td>
                <td><input type="text" name="laptops[${rowCount}][model]" required></td>
                <td><input type="text" name="laptops[${rowCount}][serial_no]" required></td>
                <td><button type="button" class="btn btn-xs btn-danger" onclick="this.parentElement.parentElement.remove()"><i class="fa fa-times"></i></button></td>
            `;
            rowCount++;
        }
    </script>
</body>
</html>