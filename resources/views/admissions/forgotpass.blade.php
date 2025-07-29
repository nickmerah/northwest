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
                 <h4>Forgot Password</h4>
             </div>
             <div class="card-body">
                 <p class="text-muted">We will send a link to reset your password</p>

                 @if (isset($message) && $registered ?? false)
                 <div class="alert alert-success">
                     {{ $message }}
                     @if ($applicationNo)
                     <p>Your New Password is: <strong>{{ $newPassword }}</strong></p>
                     @endif
                 </div>
                 @endif
                 @if (session('error'))
                 <div class="alert alert-danger">{{ session('error') }}</div>
                 @endif

                 <form id="app_login" name="app_login" action="{{ route('admissions.fpass') }}" method="post">
                     @csrf
                     <div class="form-group">
                         <label for="email">Application Number</label>
                         <input id="email" type="text" class="form-control" name="regno" tabindex="1" required autofocus autocomplete="off" value="{{ old('regno') }}">
                     </div>
                     <div class="form-group">
                         <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                             Reset Password
                         </button>
                     </div>
                 </form>
                 <div class="float-right">
                     <a href="{{ route('admissions') }}"> <strong> &lt;&lt; Back to Home</strong> </a>


                 </div>
             </div>
         </div>
     </div>
     @endsection