<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="ThemeBucket">
    <link rel="shortcut icon" href="#" type="image/png">

    <title>Login</title>

    <link href="/oa_admin/css/style.css" rel="stylesheet">
    <link href="/oa_admin/css/style-responsive.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="/oa_admin/js/html5shiv.js"></script>
    <script src="/oa_admin/js/respond.min.js"></script>
    <![endif]-->
</head>

<body class="login-body">

<div class="container">

    <form class="form-signin" method="POST" action="{{ route('login') }}">
        {{ csrf_field() }}
        <div class="form-signin-heading text-center">
            <h1 class="sign-title">登录</h1>
            <img src="/oa_admin/images/login-logo.png" alt=""/>
        </div>
        <div class="login-wrap">
            <input type="email" id="email" class="form-control" placeholder="E-mail" name="email" value="{{ old('email') }}" required autofocus>
            @if ($errors->has('email'))
                <span class="help-block">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif

            <input id="password"  type="password" class="form-control" placeholder="Password" name="password" required>
            @if ($errors->has('password'))
                <span class="help-block">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif

            <button class="btn btn-lg btn-login btn-block" type="submit">
                <i class="fa fa-check"></i>
            </button>

            <div class="registration">
                还没有帐号?
                <a class="" href="#">
                    请联系管理员！
                </a>
            </div>
            <label class="checkbox">
                <input type="checkbox"   name="remember" {{ old('remember') ? 'checked' : '' }}> 记住我
                <span class="pull-right">
                    <a data-toggle="modal" href="{{ route('password.request') }}"> 忘记密码?</a>
                </span>
            </label>

        </div>
        <!-- modal -->

    </form>

</div>



<!-- Placed js at the end of the document so the pages load faster -->
<script src="/oa_admin/js/jquery-1.10.2.min.js"></script>
<script src="/oa_admin/js/bootstrap.min.js"></script>
<script src="/oa_admin/js/modernizr.min.js"></script>
</body>
</html>



