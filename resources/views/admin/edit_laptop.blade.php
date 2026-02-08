<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Laptop | Admin</title>
    <link href="{{ asset('assets/css/bootstrap.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/font-awesome.css') }}" rel="stylesheet" />
    <style>
        body { background: #f4f7f6; padding: 50px; }
        .form-container { background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .header-title { color: #214761; font-weight: bold; border-bottom: 2px solid #e9ecef; padding-bottom: 10px; margin-bottom: 20px; }
        .btn-update { background-color: #214761; color: white; border: none; transition: 0.3s; }
        .btn-update:hover { background-color: #1a384d; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2 form-container">
                <h3 class="header-title"><i class="fa fa-edit"></i> EDIT LAPTOP DETAILS</h3>
                
                <form action="{{ route('admin.laptop.update', $laptop['id']) }}" method="POST">
                    @csrf
                    @method('PUT') <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Agreement No</label>
                            <input type="text" name="agreement_no" class="form-control" value="{{ $laptop['agreement_no'] }}" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Batch No</label>
                            <input type="text" name="batchno" class="form-control" value="{{ $laptop['batchno'] }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>First Name</label>
                            <input type="text" name="firstname" class="form-control" value="{{ $laptop['firstname'] }}" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Last Name</label>
                            <input type="text" name="lastname" class="form-control" value="{{ $laptop['lastname'] }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Department</label>
                            <input type="text" name="department" class="form-control" value="{{ $laptop['department'] }}" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Location</label>
                            <input type="text" name="location" class="form-control" value="{{ $laptop['location'] }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Lease Start</label>
                            <input type="text" name="lease_start_date" class="form-control" value="{{ $laptop['lease_start_date'] }}">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Lease End</label>
                            <input type="text" name="lease_end_date" class="form-control" value="{{ $laptop['lease_end_date'] }}">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Leasing Period</label>
                            <input type="text" name="leasing_period" class="form-control" value="{{ $laptop['leasing_period'] }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Model</label>
                            <input type="text" name="model" class="form-control" value="{{ $laptop['model'] }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Availability Status</label>
                            <select name="availability_status" class="form-control">
                                <option value="Available" {{ $laptop['availability_status'] == 'Available' ? 'selected' : '' }}>Available</option>
                                <option value="In Use" {{ $laptop['availability_status'] == 'In Use' ? 'selected' : '' }}>In Use</option>
                                <option value="Under Repair" {{ $laptop['availability_status'] == 'Under Repair' ? 'selected' : '' }}>Under Repair</option>
                            </select>
                        </div>
                    </div>

                    <hr>
                    <div class="form-group">
                        <button type="submit" class="btn btn-update btn-block btn-lg">UPDATE LAPTOP DATA</button>
                        <a href="{{ route('admin.laptop') }}" class="btn btn-default btn-block">CANCEL</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>