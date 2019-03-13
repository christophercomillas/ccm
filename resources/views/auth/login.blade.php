<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'CCM') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/bootstrap4.css') }}" rel="stylesheet">
    <link href="{{ asset('css/login.css') }}" rel="stylesheet">
</head>
<body>
    <form class="form-horizontal" role="form" method="POST" action="{{ route('login') }}">
        {{ csrf_field() }}
        <div class="container mt-5">
            <div class="row">
                <div class="col-md-5 d-flex align-items-center">
                    <h1>Check Clearing And Monitoring System</h1>
                </div>
                <div class="col-md-6 offset-md-1">
                
                    <div class="form-group">
                        <label for="name">Username</label>
                        <input class="form-control" type="text" name="username" id="username" value="{{ old('username') }}" required />
                        @if ($errors->has('username'))
                            <span class="help-block">
                                <strong>{{ $errors->first('username') }}</strong>
                            </span>
                         @endif
                    </div>
                    <div class="form-group">
                        <label for="surname">Password</label>
                        <input class="form-control" type="password" name="password" id="password" value="" required />
                        @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="response">
                        {{-- <div class="alert alert-danger" role="alert">
                            This is a danger alert with <a href="#" class="alert-link">an example link</a>. Give it a click if you like.
                        </div> --}}
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                    </div>

                </div>
            </div>
        </div>
    </form>
    <!-- Scripts -->
    <script src="{{ asset('plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap4.js') }}"></script>  
    <script src="{{ asset('js/login.js') }}"></script>
</body>
</html>