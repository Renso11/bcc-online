<!DOCTYPE html>
<html>

<!-- Mirrored from adminlte.io/themes/AdminLTE/pages/examples/login.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 09 Aug 2022 22:17:12 GMT -->
<!-- Added by HTTrack -->
<meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Espace admin</title>

  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <link rel="stylesheet" href="/bower_components/bootstrap/dist/css/bootstrap.min.css">

  <link rel="stylesheet" href="/bower_components/font-awesome/css/font-awesome.min.css">

  <link rel="stylesheet" href="/bower_components/Ionicons/css/ionicons.min.css">

  <link rel="stylesheet" href="/dist/css/AdminLTE.min.css">

  <link rel="stylesheet" href="/plugins/iCheck/square/blue.css">


  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>

<body class="hold-transition login-page">
  <div class="login-box">
    <div class="login-logo">
      <a href="/">
        <img src="/dist/img/logo.svg" style="width: 150px;" class="mr-10" />
      </a>
    </div>

    <div class="login-box-body">
      <p class="login-box-msg">Connectez vous à votre session</p>
      <form method="POST" action="{{ route('login') }}" autocomplete="off">
        @csrf
        <div class="form-group has-feedback">
          <input type="text" autocomplete="off" class="encryptInput form-control @error('username') is-invalid @enderror" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1" name="username" value="{{ old('username') }}" required autocomplete="username" autofocus>
          <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>
        @error('username')
          <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
          </span>
        @enderror
        <div class="form-group has-feedback">
          <input type="password" autocomplete="off" class="encryptInput form-control @error('password') is-invalid @enderror" placeholder="Mot de passe" required aria-label="Password" aria-describedby="basic-addon1" name="password">
          <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        @error('password')
          <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
          </span>
        @enderror
        <div class="row">
          <div class="col-xs-12">
            <button type="submit" class="btn btn-primary btn-block btn-flat">Connexion</button>
          </div>

        </div>
      </form>
    </div>

  </div>


  <script src="/bower_components/jquery/dist/jquery.min.js"></script>

  <script src="/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

  <script src="/plugins/iCheck/icheck.min.js"></script>
  <script>
    $(function () {
      $('input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%' /* optional */
      });
    });
  </script>
</body>

</html>