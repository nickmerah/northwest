  @extends('admissions.admlayout')

  @section('content')
  <div class="row">
      <div class="col-12 col-sm-12 offset-sm-1 col-md-12 offset-md-2 col-lg-12 offset-lg-2 col-xl-6 offset-xl-3">
          <div class="login-brand">
              <div align="center">
                  <a href="{{route('admissions')}}">
                      <picture>
                          <source media="(max-width: 799px)" srcset="{{ asset('public/assets/img/delta.png') }}">
                          <source media="(min-width: 800px)" srcset="{{ asset('public/assets/img/delta.png') }}">
                          <img src="{{ asset('public/assets/img/delta.png') }}" alt="School Logo" class="responsive-img valign profile-image-login">
                      </picture>

                  </a>
              </div>

          </div>
          <div class="row">

              <div class="col-12 col-sm-12 col-lg-12">

                  <div class="card card-primary">
                      <div>
                          {{-- Show success or error messages --}}
                          @if (session('success'))
                          <div class="alert alert-success">{{ session('success') }}</div>
                          @endif

                          @if (session('error'))
                          <div class="alert alert-danger">{{ session('error') }}</div>
                          @endif
                      </div>
                      <div class="card-header">
                          <h4>Create Account

                          </h4>

                      </div>

                      <form id="add_create" name="add_create" action="{{ route('admissions.store') }}" method="post">
                          @csrf
                          <div class="card-body">
                              <div class="form-group">
                                  <div class="input-group mb-2">
                                      <div class="input-group-prepend">
                                          <div class="input-group-text"><i class="fas fa-user"></i></div>
                                      </div>
                                      <input name="surname" class="form-control @error('surname') is-invalid @enderror" placeholder="Surname" required autocomplete="off" value="{{ old('surname') }}">
                                  </div>
                                  @error('surname')
                                  <div class="text-danger small">{{ $message }}</div>
                                  @enderror
                              </div>

                              <div class="form-group">
                                  <div class="input-group mb-2">
                                      <div class="input-group-prepend">
                                          <div class="input-group-text"><i class="fas fa-user-plus"></i></div>
                                      </div>
                                      <input name="firstname" type="text" class="form-control @error('firstname') is-invalid @enderror" placeholder="Firstname" required autocomplete="off" value="{{ old('firstname') }}">
                                  </div>
                                  @error('firstname')
                                  <div class="text-danger small">{{ $message }}</div>
                                  @enderror
                              </div>

                              <div class="form-group">
                                  <div class="input-group mb-2">
                                      <div class="input-group-prepend">
                                          <div class="input-group-text"><i class="fas fa-user-alt"></i></div>
                                      </div>
                                      <input name="othernames" type="text" class="form-control @error('othernames') is-invalid @enderror" placeholder="Othernames" autocomplete="off" value="{{ old('othernames') }}">
                                  </div>
                                  @error('othernames')
                                  <div class="text-danger small">{{ $message }}</div>
                                  @enderror
                              </div>

                              <div class="form-group">
                                  <div class="input-group mb-2">
                                      <div class="input-group-prepend">
                                          <div class="input-group-text"><i class="fas fa-school"></i></div>
                                      </div>
                                      <select name="progtype" id="sprogtype" class="form-control @error('progtype') is-invalid @enderror" required>

                                      </select>

                                      <script>
                                          const BASE_URL = "{{ config('app.url') }}";
                                          document.addEventListener('DOMContentLoaded', function() {
                                              const select = document.getElementById('sprogtype');

                                              // Clear existing and add default option
                                              select.innerHTML = `<option value="">Select Programme Type</option>`;

                                              fetch(`${BASE_URL}/api/v1/programme-types`)
                                                  .then(response => {
                                                      if (!response.ok) throw new Error('Failed to fetch programme types');
                                                      return response.json();
                                                  })
                                                  .then(data => {
                                                      const programmeTypeList = data.data ?? [];

                                                      programmeTypeList.forEach(prog => {
                                                          const option = document.createElement('option');
                                                          option.value = prog.programmet_id;
                                                          option.textContent = prog.programmet_name;
                                                          select.appendChild(option);
                                                      });
                                                  })
                                                  .catch(error => {
                                                      console.error('Error loading programme types:', error);
                                                  });
                                          });
                                      </script>

                                  </div> @error('progtype')
                                  <div class="text-danger small">{{ $message }}</div>
                                  @enderror
                              </div>

                              <div class="form-group">
                                  <div class="input-group mb-3">
                                      <div class="input-group-prepend">
                                          <span class="input-group-text"><i class="fas fa-university"></i></span>
                                      </div>
                                      <select name="prog" id="sel_prog" class="form-control @error('prog') is-invalid @enderror" required>
                                      </select>

                                      <script>
                                          document.addEventListener('DOMContentLoaded', function() {
                                              const select = document.getElementById('sel_prog');

                                              // Clear existing and add default option
                                              select.innerHTML = `<option value="">Select Programme </option>`;

                                              fetch(`${BASE_URL}/api/v1/programmes`)
                                                  .then(response => {
                                                      if (!response.ok) throw new Error('Failed to fetch programmes');
                                                      return response.json();
                                                  })
                                                  .then(data => {
                                                      const programmeList = data.data ?? [];

                                                      programmeList.forEach(prog => {
                                                          const option = document.createElement('option');
                                                          option.value = prog.programme_id;
                                                          option.textContent = prog.programme_name;
                                                          select.appendChild(option);
                                                      });
                                                  })
                                                  .catch(error => {
                                                      console.error('Error loading programme:', error);
                                                  });
                                          });
                                      </script>
                                  </div>
                                  @error('prog')
                                  <div class="text-danger small">{{ $message }}</div>
                                  @enderror
                              </div>

                              <div class="form-group">
                                  <div class="input-group mb-2">
                                      <div class="input-group-prepend">
                                          <div class="input-group-text"><i class="fas fa-school"></i></div>
                                      </div>
                                      <select name="cos_id" id="sel_cos" class="form-control @error('cos_id') is-invalid @enderror" required>
                                          <option value="">Select Course of Study - First Choice</option>
                                      </select>
                                  </div>
                                  @error('cos_id')
                                  <div class="text-danger small">{{ $message }}</div>
                                  @enderror
                              </div>

                              <div class="form-group">
                                  <div class="input-group mb-2">
                                      <div class="input-group-prepend">
                                          <div class="input-group-text"><i class="fas fa-school"></i></div>
                                      </div>
                                      <select name="cos_id_two" id="sel_cos_two" class="form-control @error('cos_id_two') is-invalid @enderror" required>
                                          <option value="">Select Course of Study - Second Choice</option>
                                      </select>
                                  </div>
                                  @error('cos_id_two')
                                  <div class="text-danger small">{{ $message }}</div>
                                  @enderror
                              </div>

                              <script>
                                  function updateCourseOptions(selectId, progId, progTypeId, optionText) {
                                      const cosSelect = document.getElementById(selectId);
                                      cosSelect.innerHTML = `<option value="">${optionText}</option>`;

                                      if (progId && progTypeId) {
                                          const url = `${BASE_URL}/api/v1/courses-of-study/${progId}/${progTypeId}`;

                                          fetch(url)
                                              .then(response => {
                                                  if (!response.ok) throw new Error('Network response was not ok');
                                                  return response.json();
                                              })
                                              .then(data => {
                                                  const courseList = data.data ?? [];

                                                  cosSelect.innerHTML = `<option value="">${optionText}</option>`;

                                                  courseList.forEach(cos => {
                                                      const option = document.createElement('option');
                                                      option.value = cos.do_id;
                                                      option.text = cos.programme_option;
                                                      cosSelect.appendChild(option);
                                                  });
                                              })
                                              .catch(error => console.error('Error fetching Course of Study:', error));
                                      }
                                  }

                                  document.getElementById('sprogtype').addEventListener('change', function() {
                                      const sprogTypeId = this.value;

                                      const selProg = document.getElementById('sel_prog');
                                      const selCos = document.getElementById('sel_cos');
                                      const selCosTwo = document.getElementById('sel_cos_two');

                                      selProg.value = '';
                                      selCos.innerHTML = '<option value="">Select Course of Study - First Choice</option>';
                                      selCosTwo.innerHTML = '<option value="">Select Course of Study - Second Choice</option>';

                                      selProg.addEventListener('change', function() {
                                          const progId = this.value;

                                          selCos.innerHTML = '<option value="">Select Course of Study - First Choice</option>';
                                          selCosTwo.innerHTML = '<option value="">Select Course of Study - Second Choice</option>';

                                          setTimeout(function() {
                                              updateCourseOptions('sel_cos', progId, sprogTypeId, 'Select First Choice');
                                              updateCourseOptions('sel_cos_two', progId, sprogTypeId, 'Select Second Choice');
                                          }, 200); // Reduced to 200ms or remove if unnecessary
                                      });
                                  });
                              </script>



                              <div class="form-group">
                                  <div class="input-group mb-2">
                                      <div class="input-group-prepend">
                                          <div class="input-group-text"><i class="fas fa-lock"></i></div>
                                      </div>
                                      <input name="password" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" required data-indicator="pwindicator">
                                  </div>
                                  @error('password')
                                  <div class="text-danger small">{{ $message }}</div>
                                  @enderror
                              </div>

                              <div class="form-group">
                                  <div class="input-group mb-2">
                                      <div class="input-group-prepend">
                                          <div class="input-group-text"><i class="fas fa-at"></i></div>
                                      </div>
                                      <input name="email" type="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email Address" required autocomplete="off" value="{{ old('email') }}">
                                  </div>
                                  @error('email')
                                  <div class="text-danger small">{{ $message }}</div>
                                  @enderror
                              </div>

                              <div class="form-group">
                                  <div class="input-group mb-2">
                                      <div class="input-group-prepend">
                                          <div class="input-group-text"><i class="fas fa-phone"></i></div>
                                      </div>
                                      <input name="phoneno" type="number" class="form-control @error('phoneno') is-invalid @enderror" placeholder="Phone Number" required autocomplete="off" value="{{ old('phoneno') }}">
                                  </div>
                                  @error('phoneno')
                                  <div class="text-danger small">{{ $message }}</div>
                                  @enderror
                              </div>

                              <div class="form-group">
                                  <div class="input-group mb-2">
                                      <div class="input-group-prepend">
                                          <div class="input-group-text"><i class="fas fa-user-lock"></i></div>
                                      </div>
                                      <input type="number" class="form-control @error('captchaResult') is-invalid @enderror" name="captchaResult"
                                          placeholder="Solve the Math: {{ $random_number1 }} + {{ $random_number2 }} =" required autocomplete="off">
                                  </div>
                                  @error('captchaResult')
                                  <div class="text-danger small">{{ $message }}</div>
                                  @enderror
                                  <input type="hidden" name="first_number" value="{{ $random_number1 }}">
                                  <input type="hidden" name="second_number" value="{{ $random_number2 }}">
                              </div>

                              <div align="center">
                                  <button class="btn btn-primary btn-lg btn-block" type="submit">Create Account</button>
                              </div>

                              <br />
                              <div class="float-right">
                                  <a href="{{ route('admissions') }}"><strong>&lt;&lt; Back to Home</strong></a>
                              </div>
                          </div>
                      </form>


                  </div>
                  <div align="center" class="example5">
                      <marquee> {{ $schoolInfo['appmarque'] }} </marquee>
                  </div>

              </div>

          </div>

      </div>
      @endsection