<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Edit Staff Profile</title>
    <link href="{{ asset('assets/css/bootstrap.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/font-awesome.css') }}" rel="stylesheet" />
    <style>
        body { background-color: #f4f7f6; padding-top: 50px; }
        .card { background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .header-title { border-bottom: 2px solid #214761; margin-bottom: 20px; padding-bottom: 10px; color: #214761; }
        .readonly-id { background-color: #eee; cursor: not-allowed; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2 card">
                <h2 class="header-title"><i class="fa fa-user-edit"></i> Edit Staff: {{ $staff['id'] }}</h2>
                
                <form action="{{ route('admin.staff.update', $staff['id']) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="form-group">
                        <label>User ID (Fixed)</label>
                        <input type="text" name="userid" class="form-control readonly-id" value="{{ $staff['userid'] }}" readonly>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>First Name</label>
                            <input type="text" name="first_name" class="form-control" value="{{ $staff['first_name'] }}" required>
                        </div>
                        
                        <div class="col-md-6 form-group">
                            <label>Last Name</label>
                            <input type="text" name="last_name" class="form-control" value="{{ $staff['last_name'] }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email_address" class="form-control" value="{{ $staff['email_address'] }}" required>
                    </div>

                    <div class="form-group">
                        <label>Company</label>
                        <input type="text" name="company" class="form-control" value="{{ $staff['company'] }}">
                    </div>

                    <input type="hidden" name="account_disabled" value="{{ $staff['account_disabled'] ?? 'false' }}">
                    <input type="hidden" name="account_locked_out" value="{{ $staff['account_locked_out'] ?? 'false' }}">

                    <div style="margin-top: 20px;">
                        <button type="submit" class="btn btn-success btn-lg"><i class="fa fa-save"></i> Update Changes</button>
                        <a href="{{ route('admin.staff') }}" class="btn btn-danger btn-lg">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>