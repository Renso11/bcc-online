@extends('base')
@section('css')
<link rel="stylesheet" href="/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<link rel="stylesheet" href="/plugins/bs-stepper/css/bs-stepper.min.css">
<link rel="stylesheet" href="/plugins/intl-tel-input-master/build/css/intlTelInput.css" />
<link rel="stylesheet" href="/plugins/intl-tel-input-master/build/css/demo.css" />
@endsection
@section('title')
    Modifier le partenaire {{ $partenaire->code_partenaire }}
@endsection
@section('page')
    Modifier le partenaire {{ $partenaire->code_partenaire }}
@endsection
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Modification de partenaire </h3>
                </div>

                <div class="box-body">
                    <form action="/partenaire/update/{{ $partenaire->id }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="container">
                                <div class="form-group">  
                                    <label for="recipient-name" class="control-label">Libelle du partenaire:</label>
                                    <input type="text" value="{{ $partenaire->libelle }}" autocomplete="off" required class="form-control" name="libelle">
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="recipient-name" class="control-label">Email</label>
                                            <input type="email" value="{{ $partenaire->email }}" autocomplete="off" required class="form-control" name="email">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="recipient-name" class="control-label">Telephone</label>
                                        <div class="form-group">
                                            <input id="phone" value="{{ $partenaire->telephone }}" required name="phone" style="width: 238%" type="tel" value=""/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">  
                                            <label for="recipient-name" class="control-label"> N° du RCCM</label>
                                            <input type="text" value="{{ $partenaire->num_rccm }}" autocomplete="off" required class="form-control" name="num_rccm">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">  
                                            <label for="recipient-name" class="control-label"> Fichier du RCCM <small>(Cliquer pour changer l'ancien)</small></label>
                                            <input type="file" autocomplete="off"  class="form-control" name="rccm">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">  
                                            <label for="recipient-name" class="control-label">N° IFU</label>
                                            <input type="text" autocomplete="off" value="{{ $partenaire->num_ifu }}" required class="form-control" name="num_ifu">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">  
                                            <label for="recipient-name" class="control-label">Fichier du IFU <small>(Cliquer pour changer l'ancien)</small></label>
                                            <input type="file" autocomplete="off" class="form-control" name="ifu">
                                        </div>
                                    </div>
                                </div>
                                <br/>
                                <button class="btn btn-primary" type="submit">Modifier</button>
                            </div> 
                        </div> 
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('js')
    <script src="/plugins/select2/js/select2.full.min.js"></script>
    <script src="/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script src="/plugins/intl-tel-input-master/build/js/intlTelInput.js"></script>
    <script>
        $(".example1").DataTable({
            ordering: false
        });
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        })
    </script>
    <script>
      var input = document.querySelector("#phone");
      window.intlTelInput(input, {
        // allowDropdown: false,
        // autoInsertDialCode: true,
        // autoPlaceholder: "off",
        //containerClass: "tel-input",
        // countrySearch: false,
        // customPlaceholder: function(selectedCountryPlaceholder, selectedCountryData) {
        //   return "e.g. " + selectedCountryPlaceholder;
        // },
        // defaultToFirstCountry: false,
        // dropdownContainer: document.querySelector('#custom-container'),
        // excludeCountries: ["us"],
        // fixDropdownWidth: false,
        // formatAsYouType: false,
        // formatOnDisplay: false,
        // geoIpLookup: function(callback) {
        //   fetch("https://ipapi.co/json")
        //     .then(function(res) { return res.json(); })
        //     .then(function(data) { callback(data.country_code); })
        //     .catch(function() { callback(); });
        // },
        hiddenInput: () => "phone_full",
        // i18n: { 'de': 'Deutschland' },
        initialCountry: "bj",
        // nationalMode: false,
        // onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
        // placeholderNumberType: "MOBILE",
        // preferredCountries: ['cn', 'jp'],
        // showFlags: false,
        // showSelectedDialCode: true,
        // useFullscreenPopup: true,
        utilsScript: "/plugins/intl-tel-input-master/build/js/utils.js"
      });
    </script>
@endsection