<!DOCTYPE html>
<html lang="en">
<head>
    <title>Insert Single Laptop</title>
    <link href="{{ asset('assets/css/bootstrap.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/font-awesome.css') }}" rel="stylesheet" />
    <style>
        body { background: #f4f7f6; padding: 50px; }
        .form-container { background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .header-title { color: #214761; font-weight: bold; border-bottom: 2px solid #214761; padding-bottom: 10px; margin-bottom: 20px; }
        .btn-save { background-color: #214761; color: white; border: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2 form-container">
                <h3 class="header-title"><i class="fa fa-laptop"></i> REGISTER SINGLE LAPTOP</h3>
                
                <form action="{{ route('admin.laptop.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Agreement No</label>
                            <input type="text" name="agreement_no" class="form-control" required placeholder="5359603664MYS7">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Batch No</label>
                            <input type="text" name="batchno" class="form-control" required placeholder="30">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>First Name</label>
                            <input type="text" name="firstname" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Last Name</label>
                            <input type="text" name="lastname" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Department</label>
                            <input type="text" name="department" class="form-control" required placeholder="HR Overall">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Location</label>
                            <input type="text" name="location" class="form-control" required placeholder="SUNGAI BULOH">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Lease Start</label>
                            <input type="text" name="lease_start_date" class="form-control" placeholder="Apr-18">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Lease End</label>
                            <input type="text" name="lease_end_date" class="form-control" placeholder="Apr-21">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Leasing Period</label>
                            <input type="text" name="leasing_period" class="form-control" placeholder="3 Years">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Model</label>
                        <input type="text" name="model" class="form-control" placeholder="HP">
                    </div>

                    <hr>
                    <button type="submit" class="btn btn-save btn-block btn-lg">SAVE LAPTOP TO DATABASE</button>
                    <a href="{{ route('admin.laptop') }}" class="btn btn-default btn-block">CANCEL</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>