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
                 <h4>Login</h4>

             </div>
             @if (session('error'))
             <div class="alert alert-danger">{{ session('error') }}</div>
             @endif
             <div class="card-body">



                 <div class="float-right">
                     <a href="{{ route('admissions.startpart') }}"> <strong> New Registration? Begin Here</strong> </a>


                 </div>

                 <form method="POST" action="{{ route('admissions.login') }}" class="needs-validation" autocomplete="off">
                     @csrf
                     <div class="form-group">
                         <label for="regno"> Application Number</label>
                         <input id="regno" type="text" class="form-control @error('regno') is-invalid @enderror" name="regno" tabindex="1" required autofocus maxlength="15" minlength="4">

                         @error('regno')
                         <div class="text-danger big">{{ $message }}</div>
                         @enderror
                     </div>
                     <div class="form-group">
                         <div class="d-block">
                             <label for="password" class="control-label">Password</label>
                             <div class="float-right">
                                 <a href="{{ route('admissions.fpass') }}"> <strong> Forgot Password?</strong> </a>
                             </div>
                         </div>
                         <input id="password" type="password" class="form-control" name="passkey" minlength="4" tabindex="2" required>
                         <div class="invalid-feedback">
                             please fill in your password of atleast 4 characters
                         </div>
                     </div>

                     <div class="form-group">
                         <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4" id="login-btn">
                             <span id="login-text">Login</span>
                             <span id="loading-spinner" class="d-none">
                                 <i class="fas fa-spinner fa-spin"></i> Loading...
                             </span>
                         </button>
                     </div>
                     <div class="mt-1 text-muted text-center">
                         <div align="center" class="example5">
                             <marquee> {{ $schoolInfo['appmarque'] ?? '' }} </marquee>
                         </div>
                         <div align="center">
                             <p id="demo" style="text-align:center; font-weight:bold; font-size: 16px;color:red"> </p>
                         </div>
                     </div>
                 </form>
                 <script>
                     document.addEventListener('DOMContentLoaded', function() {
                         const form = document.querySelector('form');
                         const loginBtn = document.getElementById('login-btn');
                         const loginText = document.getElementById('login-text');
                         const loadingSpinner = document.getElementById('loading-spinner');

                         form.addEventListener('submit', function() {
                             loginBtn.disabled = true;
                             loginText.classList.add('d-none');
                             loadingSpinner.classList.remove('d-none');
                         });
                     });
                 </script>

             </div>
         </div>

     </div>
 </div>

 @endsection