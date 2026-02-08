<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - Asset Management System</title>
    <link href="{{ asset('assets/css/bootstrap.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/font-awesome.css') }}" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" />
    
    <style>
        body { background-color: #f8f8f8; }
        .login-panel { margin-top: 100px; }
        .panel-heading { background-color: #214761 !important; color: white !important; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row text-center">
            <div class="col-md-12">
                <br /><br />
                <h2>Asset Management : Login</h2>
                <br />
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-10 col-xs-offset-1">
                <div class="panel panel-default login-panel">
                    <div class="panel-heading">
                        <strong>Enter Details To Login</strong>
                    </div>
                    <div class="panel-body">
                        <form action="{{ url('/login') }}" method="POST" role="form">
                            @csrf
                            
                            <br />
                            @if($errors->any())
                                <div class="alert alert-danger">
                                    {{ $errors->first() }}
                                </div>
                            @endif

                            <div class="form-group input-group">
                                <span class="input-group-addon"><i class="fa fa-tag"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="Email (admin@gmail.com)" required />
                            </div>
                            
                            <div class="form-group input-group">
                                <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                <input type="password" name="password" class="form-control" placeholder="Password (admin123)" required />
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">Login Now</button>
                            <hr />
                            Not registered? <a href="#">Click here </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/jquery-1.10.2.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
</body>
</html>