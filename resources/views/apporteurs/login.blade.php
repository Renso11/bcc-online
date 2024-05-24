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

  <link rel="stylesheet" href="{{ asset('toastr/toastr.min.css') }}">
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
      <p class="login-box-msg">Connectez vous à votre session apporteur</p>
      <form method="POST" action="{{ route('loginCheckApporteur') }}" autocomplete="off">
        @csrf
        <div class="form-group has-feedback">
          <input type="number" class="encryptInput form-control" placeholder="Telephone" name="telephone" required>
          <span class="glyphicon glyphicon-user form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
          <input type="password" class="encryptInput form-control" placeholder="Mot de passe" required name="password">
          <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
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
    <script src="{{ asset('toastr/toastr.min.js') }}"></script>
    <script>
        $(function() {
            @if (session()->has('success'))
                toastr.success("{{ session()->get('success') }}")
            @endif
            @if (session()->has('warning'))
                toastr.warning("{{ session()->get('warning') }}")
            @endif
            @if (session()->has('error'))
                toastr.error("{{ session()->get('error') }}")
            @endif
        }); 
    </script>
</body>

</html>