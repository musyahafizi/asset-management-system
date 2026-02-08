<!DOCTYPE html>
<html lang="en">
<head>
    <title>Asset Management - Staff</title>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <link href="{{ asset('assets/css/bootstrap.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('view.css') }}" />
    <script src="https://kit.fontawesome.com/92d70a2fd8.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css" />

    <style>
        .button {
            padding: 6px 24px;
            border: 2px solid #fff;
            background: transparent;
            border-radius: 6px;
            cursor: pointer;
            color: #fff;
            text-decoration: none;
            display: inline-block;
        }
        .header { background-color: #214761; padding: 10px; }
        .nav { display: flex; justify-content: space-between; align-items: center; }
        .nav_logo { color: #fff; font-size: 20px; font-weight: bold; }
        .tableCustomerData { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .tableCustomerData th, .tableCustomerData td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table { background: #fff; }
        .label-success { background-color: #5cb85c; padding: 2px 5px; color: white; border-radius: 3px; }
        .label-danger { background-color: #d9534f; padding: 2px 5px; color: white; border-radius: 3px; }
    </style>
</head>

<body>
   <header class="header">
    <nav class="nav">
        <a class="nav_logo">NSG</a>
        
        <form action="{{ route('admin.staff') }}" method="GET">
            <input type="text" name="search" placeholder="Search by name or ID..." value="{{ request('search') }}">
            <button class="button" type="submit">SEARCH</button>
            <a class="button" href="{{ route('admin.staff') }}" style="color: #fff">RESET</a>
        </form>

        <a class="button" href="{{ route('admin.dashboard') }}">BACK</a>
    </nav>
</header>

    <div class="customerdata">
        <div class="container-fluid" style="padding: 20px;">
            <h1>STAFF LIST</h1>
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email Address</th>
                        <th>Disabled</th>
                        <th>Locked</th>
                        <th>Company</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($staffList as $staff)
                    <tr>
                        <td>{{ $staff['userid'] }}</td>
                        <td>{{ $staff['first_name'] }}</td>
                        <td>{{ $staff['last_name'] }}</td>
                        <td>{{ $staff['email_address'] }}</td>
                        <td>
                            <span class="label {{ $staff['account_disabled'] == 'true' || $staff['account_disabled'] === true ? 'label-danger' : 'label-success' }}">
                                {{ $staff['account_disabled'] == 'true' || $staff['account_disabled'] === true ? 'YES' : 'NO' }}
                            </span>
                        </td>
                        <td>
                            <span class="label {{ $staff['account_locked_out'] == 'true' || $staff['account_locked_out'] === true ? 'label-danger' : 'label-success' }}">
                                {{ $staff['account_locked_out'] == 'true' || $staff['account_locked_out'] === true ? 'YES' : 'NO' }}
                            </span>
                        </td>
                        <td>{{ $staff['company'] }}</td>
                        <td>
                            <a href="{{ route('admin.staff.edit', $staff['id']) }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-edit"></i> Edit
                            </a>

                            <form action="{{ route('admin.staff.delete', $staff['id']) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this record?')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>