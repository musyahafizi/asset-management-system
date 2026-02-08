<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Portal - NSG Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root { --primary: #214761; --accent: #3498db; }
        body { background-color: #f4f7f6; }
        .navbar { background-color: var(--primary); border-bottom: 3px solid var(--accent); }
        .nav-tabs .nav-link { color: var(--primary); font-weight: 500; border: none; }
        .nav-tabs .nav-link.active { background-color: var(--primary) !important; color: white !important; border-radius: 5px; }
        .card { border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border-radius: 10px; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark py-2 mb-4">
    <div class="container-fluid">
        <span class="navbar-brand fw-bold">NSG Staff Portal</span>
        <div class="d-flex align-items-center text-white">
            <span class="me-3 small">Welcome, <strong>{{ Auth::user()->name }}</strong></span>
            <a href="{{ route('logout') }}" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <ul class="nav nav-tabs mb-4 border-0" id="staffTabs">
        <li class="nav-item">
            <button class="nav-link active me-2 shadow-sm" id="tab-history" onclick="switchTab('history')">
                <i class="fa-solid fa-clock-rotate-left me-1"></i> My History
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link shadow-sm" id="tab-available" onclick="switchTab('available')">
                <i class="fa-solid fa-list me-1"></i> Available Items
            </button>
        </li>
    </ul>

    <div id="history-panel" class="card">
        <div class="card-header bg-white fw-bold py-3">Request Status History</div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Status</th>
                        <th>Request Date</th>
                        <th>Return Deadline</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($myRequests as $req)
                    <tr>
                        <td class="fw-bold">{{ $req['item_name'] }}</td>
                        <td>{{ $req['quantity'] }}</td>
                        <td>
                            @php
                                $badge = match($req['status']) {
                                    'Approved' => 'bg-success',
                                    'Returned' => 'bg-info text-white',
                                    'Rejected' => 'bg-danger',
                                    default => 'bg-warning text-dark',
                                };
                            @endphp
                            <span class="badge {{ $badge }}">{{ $req['status'] }}</span>
                        </td>
                        <td class="text-muted small">{{ $req['request_date'] }}</td>
                        <td class="text-muted small">{{ $req['return_deadline'] ?? 'N/A' }}</td>
                        <td>
                            @if($req['status'] == 'Approved')
                                <form action="{{ route('staff.return') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="request_id" value="{{ $req['id'] }}">
                                    <input type="hidden" name="item_id" value="{{ $req['item_id'] }}">
                                    <input type="hidden" name="quantity" value="{{ $req['quantity'] }}">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                        <i class="fa-solid fa-rotate-left me-1"></i> Return
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="fa-solid fa-folder-open fa-2x text-light mb-2"></i><br>
                            You haven't made any requests yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="available-panel" class="card" style="display:none;">
        <div class="card-header bg-white fw-bold py-3">Available for Borrowing</div>
        <div class="card-body p-0">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Item Name</th>
                        <th>Remaining Qty</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['qtty'] }}</td>
                        <td>
                            <button class="btn btn-sm btn-primary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#borrowModal" 
                                    data-id="{{ $item['id'] }}" 
                                    data-name="{{ $item['name'] }}">
                                <i class="fa-solid fa-hand-holding me-1"></i> Request
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center py-5">No items available.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="borrowModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('staff.borrow') }}" method="POST" class="modal-content border-0 shadow-lg">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Request Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="item_id" id="m_item_id">
                <input type="hidden" name="item_name" id="m_item_name_hidden">
                <div class="mb-3">
                    <label class="form-label small text-muted">Item Name</label>
                    <input type="text" id="m_item_name" class="form-control bg-light" readonly>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Quantity</label>
                        <input type="number" name="quantity" class="form-control" min="1" value="1" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Return Date</label>
                        <input type="date" name="return_date" class="form-control" required min="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Reason</label>
                    <textarea name="reason" class="form-control" rows="3" required placeholder="Why do you need this?"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Submit Request</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function switchTab(type) {
        document.getElementById('history-panel').style.display = type === 'history' ? 'block' : 'none';
        document.getElementById('available-panel').style.display = type === 'available' ? 'block' : 'none';
        
        document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
        document.getElementById('tab-' + type).classList.add('active');
    }

    const borrowModal = document.getElementById('borrowModal');
    borrowModal.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;
        document.getElementById('m_item_id').value = btn.getAttribute('data-id');
        document.getElementById('m_item_name').value = btn.getAttribute('data-name');
        document.getElementById('m_item_name_hidden').value = btn.getAttribute('data-name');
    });
</script>
</body>
</html>