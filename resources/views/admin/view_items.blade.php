<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Items | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-size/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; }
        .sidebar { min-height: 100vh; background: #212529; color: white; }
        .sidebar .nav-link { color: #c2c7d0; }
        .sidebar .nav-link:hover { background: #495057; color: white; }
        .card-header { background-color: #343a40; color: white; }
        .badge-low { background-color: #dc3545; }
        .badge-good { background-color: #198754; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 sidebar p-0">
            <div class="p-3 text-center border-bottom border-secondary">
                <h5>Admin Panel</h5>
            </div>
            <ul class="nav flex-column p-2">
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.staff') }}"><i class="fas fa-users me-2"></i> Staff List</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.laptop') }}"><i class="fas fa-laptop me-2"></i> Laptops</a></li>
                <li class="nav-item"><a class="nav-link active bg-primary text-white" href="{{ route('admin.items') }}"><i class="fas fa-boxes me-2"></i> Inventory Items</a></li>
            </ul>
        </div>

        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-boxes me-2"></i>Inventory Stock Management</h2>
                <a href="{{ route('admin.items.create') }}" class="btn btn-success">
                    <i class="fas fa-plus me-1"></i> Add New Item
                </a>
            </div>

            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.items') }}" method="GET" class="row g-3">
                        <div class="col-md-9">
                            <input type="text" name="search" class="form-control" placeholder="Search item name..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-dark w-100">Search Inventory</button>
                        </div>
                    </form>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-header border-0">
                    <h6 class="mb-0"><i class="fas fa-table me-2"></i>Current Stock Levels</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Item ID</th>
                                <th>Item Name</th>
                                <th>Description</th>
                                <th>Quantity</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($staffList as $item)
                                <tr>
                                    <td class="ps-4"><code>{{ $item['id'] }}</code></td>
                                    <td class="fw-bold">{{ $item['name'] }}</td>
                                    <td class="text-muted">{{ Str::limit($item['description'], 50) }}</td>
                                    <td>
                                        <span class="badge {{ intval($item['qtty']) < 50 ? 'badge-low' : 'badge-good' }}">
                                            {{ $item['qtty'] }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(intval($item['qtty']) < 50)
                                            <span class="text-danger small fw-bold uppercase"><i class="fas fa-exclamation-triangle me-1"></i> Restock Needed</span>
                                        @else
                                            <span class="text-success small fw-bold">OK</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-secondary border-0"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-outline-danger border-0"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        No items found in your inventory.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>