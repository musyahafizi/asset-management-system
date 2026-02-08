<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Item | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; }
        .sidebar { min-height: 100vh; background: #212529; color: white; }
        .sidebar .nav-link { color: #c2c7d0; }
        .sidebar .nav-link:hover { background: #495057; color: white; }
        .card-header { background-color: #343a40; color: white; }
        .btn-save { background-color: #198754; color: white; border: none; }
        .btn-save:hover { background-color: #157347; color: white; }
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
            <div class="mb-4">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.items') }}">Inventory Items</a></li>
                        <li class="breadcrumb-item active">Add New Item</li>
                    </ol>
                </nav>
                <h2><i class="fas fa-plus-circle me-2"></i>Add New Stock Item</h2>
                <p class="text-muted">Fill in the details below to add a new item to your Firestore inventory.</p>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header border-0 p-3">
                    <h6 class="mb-0">Item Specifications</h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.items.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-bold">Item Name</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Wireless Mouse, HDMI Cable" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Stock Quantity</label>
                                <input type="number" name="quantity" class="form-control" placeholder="e.g. 100" required min="0">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Item Description</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Enter details about the item (brand, color, etc.)"></textarea>
                        </div>

                        <div class="d-flex gap-2 border-top pt-4">
                            <button type="submit" class="btn btn-save px-4">
                                <i class="fas fa-save me-1"></i> SAVE TO INVENTORY
                            </button>
                            <a href="{{ route('admin.items') }}" class="btn btn-outline-secondary px-4">
                                CANCEL
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>