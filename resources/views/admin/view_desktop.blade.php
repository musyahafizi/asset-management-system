<!DOCTYPE html>
<html lang="en">
<head>
    <title>View Desktops</title>
    <link href="{{ asset('assets/css/bootstrap.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/font-awesome.css') }}" rel="stylesheet" />
    <style>
        body { background: #f4f7f6; padding: 20px; }
        .table-container { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .header-title { color: #214761; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="table-container">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="header-title"><i class="fa fa-desktop"></i> DESKTOP INVENTORY</h3>
                </div>
                <div class="col-md-6 text-right">
                    <a href="{{ route('admin.batch.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add Batch</a>
                </div>
            </div>
            <hr>
            
            <table class="table table-hover table-striped">
                <thead style="background: #214761; color: white;">
                    <tr>
                        <th>Model</th>
                        <th>Serial No</th>
                        <th>OS</th>
                        <th>Username</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($desktopList as $desktop)
                    <tr>
                        <td>{{ $desktop['model'] }}</td>
                        <td>{{ $desktop['serial'] }}</td>
                        <td>{{ $desktop['os'] ?? 'N/A' }}</td>
                        <td>{{ $desktop['username'] }}</td>
                        <td>{{ $desktop['location'] }}</td>
                        <td>
                            <span class="label {{ $desktop['status'] == 'In Stock' ? 'label-success' : 'label-info' }}">
                                {{ $desktop['status'] }}
                            </span>
                        </td>
                        <td>
                            <a href="#" class="btn btn-xs btn-warning"><i class="fa fa-edit"></i></a>
                            <button class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center">No desktop records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>