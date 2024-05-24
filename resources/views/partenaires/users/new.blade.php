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
    Nouvel utilisateur de partenaire
@endsection
@section('page')
    Nouvel utilisateur de partenaire
@endsection
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Ajout d'utilisateur de partenaire </h3>
                </div>

                <div class="box-body">
                    <form action="/partenaire/user/add/{{ $partenaire->id }}" method="POST"> 
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">  
                                        <label for="recipient-name" class="control-label">Nom de l'Utilisateur:</label>
                                        <input type="text" required autocomplete="off" class="form-control" name="name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">  
                                        <label for="recipient-name" class="control-label">Prenom de l'utilisateur:</label>
                                        <input type="text" required autocomplete="off" class="form-control" name="lastname">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="recipient-name" class="control-label">Telephone :</label>
                                    <div class="form-group">
                                        <input id="phone" required name="phone" style="width: 244%" type="tel" value=""/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Role :</label>
                                        <select required class="form-control select2bs4 type" name="role" id="" style="width:100%">
                                            <option value="">Selectionner un role</option>                                                                    
                                            @foreach ($roles as $item)
                                                <option value="{{ $item->id }}">{{ $item->libelle }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
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