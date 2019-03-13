<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title }}</title>

        <!-- Bootstrap -->
        <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/bootstrap-dialog.min.css') }}" rel="stylesheet">

        <link href="{{ asset('css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">

        <link href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}" rel="stylesheet">

        <link href="{{ asset('plugins/daterangepicker/datepicker3.css') }}" rel="stylesheet">

        <link href="{{ asset('css/jquery.dataTables.css') }}" rel="stylesheet">
        <link href="{{ asset('font-awesome-4.7.0/css/font-awesome.min.css') }}" rel="stylesheet">
        <!-- Ionicons -->   
        <link href="{{ asset('ionicons-2.0.1/css/ionicons.min.css') }}" rel="stylesheet">
        <!-- <link href="{{ asset('css/pretty-checkbox.min.css') }}" rel="stylesheet"> -->
        <link href="{{ asset('css/sweetalert.css') }}" rel="stylesheet">
        <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">
        <link href="{{ asset('css/reset.css') }}" rel="stylesheet">
        @stack('styles')

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <input type="hidden" name="baseurl" value="{{ url('/') }}">
        <style>
            body {
                padding-top: 50px;
            }
            .navbar-template {
                padding: 40px 15px;
            }

            .navbar-login
            {
                width: 305px;
                padding: 10px;
                padding-bottom: 0px;
            }

            .navbar-login-session
            {
                padding: 10px;
                padding-bottom: 0px;
                padding-top: 0px;
            }

            .icon-size
            {
                font-size: 87px;
            }

        </style>
        <div class="navbar navbar-default navbar-fixed-top" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="{{ route('home') }}">CCM <span class="btitle">[{{ Auth::user()->businessunit->bname }}]</span></a>
                </div>
                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li><a href="{{ route('home') }}">Dashboard</a></li>
                        <li>
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Transactions <b class="caret"></b></a>

                            <ul class="dropdown-menu">
                                <li><a href="{{ route('dbupdatefromatp') }}">Check Database Update From ATP DB</a></li>
                                <li><a href="{{ route('institutional') }}">Institutional Check (Textfile Import)</a></li>
                                <li><a href="{{ route('receiving') }}">Check Receiving (Manual Entry)</a></li>                                
                                <li><a href="{{ route('receivingupload') }}">Check Receiving (Textfile Upload)</a></li>
                                <li><a href="{{ route('checksforclearing') }}">Checks for Clearing</a></li>
                                <li><a href="{{ route('bouncedchecks') }}">Bounced Checks</a></li>
                            </ul>
                        </li>                       
                        <li>
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Masterfile <b class="caret"></b></a>

                            <ul class="dropdown-menu">
                                @if(Auth::user()->usertype->usertype_name == 'Admin') 
                                <li><a href="{{ route('users') }}">User</a></li>
                                @endif 
                                <li><a href="{{ route('salesman') }}">Salesman</a></li>
                                <li><a href="{{ route('customers') }}">Customer</a></li>
                                <li><a href="{{ route('banks') }}">Banks</a></li>
                                <li><a href="{{ route('checklist') }}">Check</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Reports <b class="caret"></b></a>

                            <ul class="dropdown-menu">     
                                <!-- <li><a href="{{ route('checksfordeposit') }}">Checks for Deposit</a></li>       -->
                                <li><a href="{{ route('checkreports') }}">Check Reports</a></li>
                                <li><a href="{{ route('checklistpdc') }}">PDC</a></li>
                                <li><a href="{{ route('clearedchecks') }}">Cleared Checks (Batch)</a></li>
                                <!-- <li><a href="{{ route('bouncedchecks2') }}">Test</a></li> -->
                            </ul>
                        </li>
                        <!-- <li>
                            <a href="{{ route('about') }}">About</a>                               

                        </li> -->
                        <!--<li>
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Menu 2 <b class="caret"></b></a>

                            <ul class="dropdown-menu">
                                <li><a href="#">Action [Menu 2.1]</a></li>
                                <li><a href="#">Another action [Menu 2.1]</a></li>
                                <li><a href="#">Something else here [Menu 2.1]</a></li>
                                <li class="divider"></li>
                                <li><a href="#">Separated link [Menu 2.1]</a></li>
                                <li class="divider"></li>
                                <li><a href="#">One more separated link [Menu 2.1]</a></li>
                                <li>
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown [Menu 2.1] <b class="caret"></b></a>

                                    <ul class="dropdown-menu">
                                        <li><a href="#">Action [Menu 2.2]</a></li>
                                        <li><a href="#">Another action [Menu 2.2]</a></li>
                                        <li><a href="#">Something else here [Menu 2.2]</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#">Separated link [Menu 2.2]</a></li>
                                        <li class="divider"></li>
                                        <li>
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown [Menu 2.2] <b class="caret"></b></a>

                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown [Menu 2.3] <b class="caret"></b></a>

                                                    <ul class="dropdown-menu">
                                                        <li><a href="#">Action [Menu 2.4]</a></li>
                                                        <li><a href="#">Another action [Menu 2.4]</a></li>
                                                        <li><a href="#">Something else here [Menu 2.4]</a></li>
                                                        <li class="divider"></li>
                                                        <li><a href="#">Separated link [Menu 2.4]</a></li>
                                                        <li class="divider"></li>
                                                        <li><a href="#">One more separated link [Menu 2.4]</a></li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>-->
                    </ul>

                    <ul class="nav navbar-nav navbar-right">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <span class="glyphicon glyphicon-user"></span> 
                                <strong>{{ Auth::user()->name }}</strong>
                                <span class="glyphicon glyphicon-chevron-down"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <div class="navbar-login">
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <p class="text-center">
                                                    <span class="glyphicon glyphicon-user icon-size"></span>
                                                </p>
                                            </div>
                                            <div class="col-lg-8">
                                                <p class="text-left"><strong>{{ Auth::user()->name }}</strong></p>
                                                <p class="text-left small"><strong>Username: {{ Auth::user()->username }}</strong></p>
                                                <p class="text-left small"><strong>Department: {{ Auth::user()->usertype->usertype_name }}</strong></p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <div class="navbar-login navbar-login-session">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <p>
                                                    <a class="btn btn-danger btn-block" href="{{ route('logout') }}"
                                                        onclick="event.preventDefault();
                                                                 document.getElementById('logout-form').submit();">
                                                        Logout
                                                    </a>

                                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                        {{ csrf_field() }}
                                                    </form>                                                    
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>

                </div><!--/.nav-collapse -->

            </div>
        </div>

        @yield('content')       

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->

        <script src="{{ asset('plugins/jQuery/jquery-2.2.3.min.js') }}"></script>        
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="{{ asset('js/bootstrap.min.js') }}"></script>   
        <script src="{{ asset('js/bootstrap-dialog.min.js') }}"></script>   
        <script src="{{ asset('js/jquery.mask.min.js') }}"></script>   
        <script src="{{ asset('js/jquery.inputmask.bundle.min.js') }}"></script>   
        <script src="{{ asset('js/jquery.validate.min.js') }}"></script>   
        <script src="{{ asset('js/jquery.dataTables.js') }}"></script>   
        <script src="{{ asset('js/jquery.num2words.js') }}"></script>  
        <script src="{{ asset('js/sweetalert.min.js') }}"></script>  
        <script src="{{ asset('js/navbar.js') }}"></script> 
        <script src="{{ asset('plugins/datepicker/bootstrap-datepicker.js') }}"></script>  
        @stack('scripts')  
        <script src="{{ asset('js/funct5.js') }}"></script>   

    </body>
</html>