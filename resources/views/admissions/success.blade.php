 @extends('admissions.admlayout')

 @section('content')
 <div class="row">
     <div class="col-12 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-8 offset-lg-2 col-xl-6 offset-xl-3">
         <div class="login-brand">



             <div align="center">
                 <a href="{{ route('admissions') }}">
                     <picture>
                         <source media="(max-width: 799px)" srcset="{{ asset('public/assets/img/delta.png') }}">
                         <source media="(min-width: 800px)" srcset="{{ asset('public/assets/img/delta.png') }}">
                         <img src="{{ asset('public/assets/img/delta.png') }}" alt="School Logo" class="responsive-img valign profile-image-login">
                     </picture>

                 </a>
             </div>

         </div>
         <div class="card card-primary">
             <div class="card-header">
                 <h4>Success</h4>

             </div>
             <div class="alert alert-success">
                 {{ $message }}
             </div>
             <div class="card-body">


                 @if (!empty($applicationNo))
                 <div class="card mt-4">
                     <div class="card-body">
                         <h5>Login Details:</h5>
                         <ul>
                             <li>Application Number: {{ $applicationNo ?? 'N/A' }}</li>
                         </ul>
                     </div>
                 </div>
                 @endif
                 <div class="alert alert-success">
                     Copy your application number in a safe place because you will require it to login.
                 </div>
                 <a href="{{ route('admissions.starting') }}" class="btn btn-primary mt-3">Proceed to Login</a>
             </div>
         </div>

     </div>
 </div>

 @endsection