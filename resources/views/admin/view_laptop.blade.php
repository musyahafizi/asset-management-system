<!DOCTYPE html>
<html lang="en">
<head>
    <title>View Laptops</title>
    <link href="{{ asset('assets/css/bootstrap.css') }}" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
    <style>
        body { background: #f4f7f6; padding: 20px; }
        .table-container { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .header-title { color: #214761; margin-bottom: 20px; }
        .label-success { background-color: #5cb85c; }
        .label-info { background-color: #5bc0de; }
        
        /* Fixed Action Button Styles */
        .action-buttons {
            display: flex;
            gap: 5px;
            justify-content: center;
        }
        .btn-xs {
            width: 30px;
            height: 30px;
            line-height: 28px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="table-container">
            <div class="row">
                <div class="col-md-4">
                    <h3 class="header-title"><i class="fa fa-laptop"></i> LAPTOP INVENTORY</h3>
                </div>
                
                <div class="col-md-4">
                    <form action="{{ route('admin.laptop') }}" method="GET">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search First Name or Batch..." value="{{ request('search') }}">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="submit" style="background: #214761; color: white;">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                        </div>
                    </form>
                </div>

                <div class="col-md-4 text-right">
                    <button type="button" onclick="submitExportForm()" class="btn btn-danger">
                        <i class="fa fa-file-pdf-o"></i> EXPORT SELECTED
                    </button>
                    <a href="{{ route('admin.batch.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus"></i> ADD BATCH
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-default">BACK</a>
                </div>
            </div>
            <hr>

            <form id="exportForm" action="{{ route('admin.laptop.pdf.selected') }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead style="background: #214761; color: white;">
                            <tr>
                                <th width="30"><input type="checkbox" id="selectAll"></th>
                                <th>Model</th>
                                <th>Serial Number</th>
                                <th>Batch No</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Department</th>
                                <th>Status</th>
                                <th style="width: 120px; text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($staffList as $laptop)
                            <tr>
                                <td>
                                    <input type="checkbox" name="selected_ids[]" value="{{ $laptop['id'] }}" class="laptop-checkbox">
                                </td>
                                <td>{{ $laptop['model'] ?? 'N/A' }}</td>
                                <td>{{ $laptop['serial_number'] ?? 'N/A' }}</td>
                                <td>{{ $laptop['batchno'] ?? 'N/A' }}</td>
                                <td>{{ $laptop['firstname'] ?? 'N/A' }}</td>
                                <td>{{ $laptop['lastname'] ?? 'N/A' }}</td>
                                <td>{{ $laptop['department'] ?? 'N/A' }}</td>
                                <td>
                                    @php $status = $laptop['status'] ?? 'In Stock'; @endphp
                                    <span class="label {{ $status == 'In Use' ? 'label-info' : 'label-success' }}">
                                        {{ $status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        @if(isset($laptop['id']))
                                            <a href="{{ route('admin.laptop.edit', $laptop['id']) }}" class="btn btn-xs btn-warning" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            
                                            <a href="{{ route('admin.laptop.qr', $laptop['id']) }}" class="btn btn-xs btn-info" title="QR Code">
                                                <i class="fa fa-qrcode"></i>
                                            </a>

                                            <button type="button" class="btn btn-xs btn-danger" onclick="deleteItem('{{ $laptop['id'] }}')" title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="9" class="text-center">No laptop records found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>

            <form id="delete-form" action="" method="POST" style="display:none;">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>

    <script>
        document.getElementById('selectAll').onclick = function() {
            var checkboxes = document.querySelectorAll('.laptop-checkbox');
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        }

        function submitExportForm() {
            var selected = document.querySelectorAll('.laptop-checkbox:checked').length;
            if (selected === 0) {
                alert('Please select at least one laptop to export.');
                return;
            }
            document.getElementById('exportForm').submit();
        }

        function deleteItem(id) {
            if (confirm('Are you sure you want to delete this record?')) {
                var form = document.getElementById('delete-form');
                form.action = "{{ route('admin.laptop.delete', ':id') }}".replace(':id', id);
                form.submit();
            }
        }
    </script>
</body>
</html>