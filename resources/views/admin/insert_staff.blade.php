<!DOCTYPE html>
<html lang="en">
<head>
    <title>Insert Staff</title>
    <link href="{{ asset('assets/css/bootstrap.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/font-awesome.css') }}" rel="stylesheet" />
    <style>
        body { background-color: #f4f7f6; padding: 50px; }
        .form-container { background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .btn-dark { background-color: #214761; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3 form-container">
                <h3 class="text-center" style="color:#214761;"><i class="fa fa-user-plus"></i> ADD NEW STAFF</h3>
                <hr>
                <form action="{{ route('admin.staff.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>User ID</label>
                        <input type="text" name="UserID" class="form-control" placeholder="Ex: AA80209" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>First Name</label>
                            <input type="text" name="FirstName" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Last Name</label>
                            <input type="text" name="LastName" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="EmailAddress" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Company</label>
                        <input type="text" name="Company" class="form-control">
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Account Disabled</label>
                            <select name="AccountDisabled" class="form-control">
                                <option value="FALSE">FALSE</option>
                                <option value="TRUE">TRUE</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Locked Out</label>
                            <select name="AccountLockedOut" class="form-control">
                                <option value="FALSE">FALSE</option>
                                <option value="TRUE">TRUE</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-dark btn-block btn-lg">INSERT STAFF</button>
                    <a href="{{ route('admin.staff') }}" class="btn btn-default btn-block">CANCEL</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>