<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard - NSG Inventory</title>
    <link href="{{ asset('assets/css/bootstrap.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/font-awesome.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .panel-requests { border-left: 5px solid #214761; }
        .div-square { padding:15px; background:#f9f9f9; border:1px solid #e1e1e1; margin-bottom:20px; border-radius:8px;}
        .div-square:hover { background: #fff; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .div-square a { text-decoration: none; color: #333; }
        .label-deadline { font-size: 0.85em; color: #d9534f; font-weight: bold; }
    </style>
</head>
<body>
<div id="wrapper">
    <div class="navbar navbar-inverse navbar-fixed-top">
        <div class="adjust-nav">
            <div class="navbar-header">
                <a class="navbar-brand" href="#">
                    <strong style="color:#fff;">NSG INVENTORY</strong>
                </a>
            </div>
            <span class="logout-spn">
                <a href="{{ route('logout') }}" style="color:#fff;">LOGOUT</a>
            </span>
        </div>
    </div>

    <nav class="navbar-default navbar-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav" id="main-menu">
                <li class="active-link">
                    <a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard fa-3x"></i>Dashboard</a>
                </li>
                <li>
                    <a href="{{ route('admin.items') }}"><i class="fa fa-desktop fa-3x"></i>Low Stock Items <span class="badge" style="background:red;">{{ $itemCount }}</span></a>
                </li>
                <li>
                    <a href="{{ route('admin.staff') }}"><i class="fa fa-users fa-3x"></i>Staff List</a>
                </li>
            </ul>
        </div>
    </nav>

    <div id="page-wrapper">
        <div id="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <h2>ADMIN DASHBOARD</h2>
                </div>
            </div>
            <hr />

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="alert alert-info">
                <strong>Welcome Back, {{ $username }}!</strong>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default panel-requests">
                        <div class="panel-heading"><b><i class="fa fa-bell"></i> Pending Staff Borrowing Requests</b></div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Staff Name</th>
                                            <th>Item Requested</th>
                                            <th>Qty</th>
                                            <th>Request Date</th>
                                            <th>Return Deadline</th> <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pendingRequests as $id => $req)
                                        <tr>
                                            <td>{{ $req['staff_name'] }}</td>
                                            <td><span class="label label-info">{{ $req['item_name'] }}</span></td>
                                            <td><b>{{ $req['quantity'] }}</b></td>
                                            <td>{{ $req['request_date'] }}</td>
                                            <td>
                                                <span class="label-deadline">
                                                    <i class="fa fa-calendar"></i> {{ $req['return_deadline'] ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                <form action="{{ route('admin.approve', $id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-xs">Approve</button>
                                                </form>
                                                <form action="{{ route('admin.reject', $id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-xs">Reject</button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="6" class="text-center">No pending borrowing requests.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row text-center pad-top">
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                    <div class="div-square">
                        <a href="{{ route('admin.items') }}"><i class="fa fa-eye fa-5x"></i><h4>View Items</h4></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                    <div class="div-square">
                        <a href="{{ route('admin.laptop') }}"><i class="fa fa-laptop fa-5x"></i><h4>View Laptop</h4></a>
                    </div>
                </div>
              
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                    <div class="div-square">
                        <a href="{{ route('admin.staff') }}"><i class="fa fa-users fa-5x"></i><h4>View Staff</h4></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                    <div class="div-square">
                        <a href="{{ route('admin.item.insert') }}"><i class="fa fa-plus-circle fa-5x"></i><h4>Insert Item</h4></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                    <div class="div-square">
                        <a href="{{ route('admin.laptop.insert') }}"><i class="fa fa-edit fa-5x"></i><h4>Insert Laptop</h4></a>
                    </div>
                </div>
              
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                    <div class="div-square">
                        <a href="{{ route('admin.batch.create') }}"><i class="fa fa-folder fa-5x"></i><h4>Insert Batch</h4></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                    <div class="div-square">
                        <a href="{{ route('admin.staff.insert') }}"><i class="fa fa-user-plus fa-5x"></i><h4>Insert Staff</h4></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('assets/js/jquery-1.10.2.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
</body>
</html>