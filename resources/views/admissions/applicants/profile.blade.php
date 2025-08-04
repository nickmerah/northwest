  @extends('admissions.applicants.layout')

  @section('content')

  <!-- Main Content -->

  <div class="main-content">
      <section class="section">

          <ul class="breadcrumb breadcrumb-style ">
              <li class="breadcrumb-item">

                  <h4 class="page-title m-b-0">My Profile</h4>
              </li>
              <li class="breadcrumb-item">
                  <a href="{{ route('admissions.dashboard') }}">
                      <i class="fas fa-home"></i></a>
              </li>
              <li class="breadcrumb-item active">Dashboard</li>
          </ul>
          @php
          //feeId = 1 for appplication form fee
          $filtered = collect($applicantPayments)->filter(fn($p) => $p['feeId'] == 1);
          $pstat = $filtered->isEmpty() ? 0 : 1;

          @endphp
          <div class="row">
              <div class="col-12 col-md-6 col-lg-12">
                  <div class="card">
                      <div class="card-header">
                          <h4>Biodata</h4>
                      </div>
                      <div class="card-body">

                          @if (session('error'))
                          <div class="alert alert-danger">
                              {{ session('error') }}
                          </div>
                          @endif
                          @if (session('success'))
                          <div class="alert alert-success">
                              {{ session('success') }}
                          </div>
                          @endif
                          <form id="update_profile" name="update_profile"
                              action="{{ route('admissions.biodata') }}" method="post"
                              enctype="multipart/form-data">
                              @csrf

                              <div class="row">
                                  <div class="mb-3 col-md-6">
                                      <label class="form-label" for="inputFirstName"><strong>Application Number</strong></label>
                                      <input type="text" class="form-control" name="app_no" value="{{ $biodetail->applicationNumber }}" readonly>
                                  </div>

                                  <div class="mb-3 col-md-6">
                                      <label class="form-label" for="inputLastName"><strong>Surname</strong></label>
                                      <input type="text" class="form-control" name="surname" value="{{ $biodetail->surname }}" readonly>
                                  </div>
                              </div>

                              <div class="row">
                                  <div class="mb-3 col-md-6">
                                      <label class="form-label" for="inputFirstName"><strong>Firstname</strong></label>
                                      <input type="text" class="form-control" name="firstname" value="{{ $biodetail->firstname }}" readonly>
                                  </div>

                                  <div class="mb-3 col-md-6">
                                      <label class="form-label" for="inputLastName"><strong>Othername</strong></label>
                                      <input type="text" class="form-control" name="othernames" value="{{ $biodetail->othernames }}" placeholder="Other name" autocomplete="off">
                                  </div>
                              </div>

                              <div class="row">
                                  <div class="mb-3 col-md-6">
                                      <label class="form-label"><strong>Email</strong></label>
                                      <input type="text" class="form-control" name="student_email" value="{{ $biodetail->studentEmail }}" autocomplete="off" readonly>
                                  </div>

                                  <div class="mb-3 col-md-6">
                                      <label class="form-label"><strong>GSM</strong></label>
                                      <input type="text" class="form-control" name="student_mobiletel" value="{{ $biodetail->studentPhoneNo }}" autocomplete="off" readonly>
                                  </div>
                              </div>

                              <div class="row">
                                  <div class="mb-3 col-md-6">
                                      <label class="form-label"><strong>Course of Study</strong></label>
                                      <select id="sprog" class="form-control" disabled>
                                          <option>{{ $biodetail->firstChoiceCourse['programme_option'] ?? 'N/A' }}</option>
                                      </select>
                                  </div>

                                  <div class="mb-3 col-md-6">
                                      <label class="form-label"><strong>Gender</strong></label>
                                      <select name="gender" class="form-control" required>
                                          <option value="{{ $biodetail->gender ?? '' }}">{{ $biodetail->gender ?? 'Select Gender' }}</option>
                                          <option value="Male" {{ old('gender', $biodetail->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                          <option value="Female" {{ old('gender', $biodetail->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                      </select>
                                  </div>
                              </div>

                              <div class="row">
                                  <div class="mb-3 col-md-6">
                                      <label class="form-label"><strong>Programme</strong></label>
                                      <select id="sprog" class="form-control" disabled>
                                          <option>{{ $biodetail->programme['programme_name'] ?? 'N/A' }}</option>
                                      </select>
                                  </div>

                                  <div class="mb-3 col-md-6">
                                      <label class="form-label"><strong>Marital Status</strong></label>
                                      <select name="marital_status" class="form-control" required>
                                          <option value="{{ $biodetail->maritalStatus ?? '' }}">{{ $biodetail->maritalStatus ?? 'Select Marital Status' }}</option>
                                          <option value="Single">Single</option>
                                          <option value="Married">Married</option>
                                          <option value="Divorced">Divorced</option>
                                          <option value="Widowed">Widowed</option>
                                      </select>
                                  </div>
                              </div>

                              <div class="row">
                                  <div class="mb-3 col-md-6">
                                      <label class="form-label"><strong>State</strong></label>
                                      <select name="state" id="sel_state" class="form-control" required>
                                          <option value="{{ $biodetail->stateofOrigin['state_id'] ?? '' }}">{{ $biodetail->stateofOrigin['state_name'] ?? 'Select State' }}</option>
                                          @foreach ($states as $state)
                                          <option value="{{ $state['state_id'] }}">{{ $state['state_name'] }}</option>
                                          @endforeach
                                      </select>
                                  </div>

                                  <div class="mb-3 col-md-6">
                                      <label class="form-label"><strong>LGA</strong></label>
                                      <select name="lga" id="sel_lga" class="form-control" required>
                                          <option value="{{ $biodetail->lga['lga_id'] ?? '' }}">{{ $biodetail->lga['lga_name'] ?? 'Select LGA' }}</option>
                                      </select>
                                  </div>
                              </div>

                              <script>
                                  document.getElementById('sel_state').addEventListener('change', function() {
                                      const stateId = this.value;
                                      const lgaSelect = document.getElementById('sel_lga');

                                      if (stateId) {
                                          fetch("{{ url('/api/v1/getlga') }}", {
                                                  method: 'POST',
                                                  headers: {
                                                      'Content-Type': 'application/x-www-form-urlencoded'
                                                  },
                                                  body: new URLSearchParams('stateId=' + stateId)
                                              })
                                              .then(response => response.json())
                                              .then(data => {
                                                  lgaSelect.innerHTML = '<option value="">Select LGA</option>';
                                                  data['data'].forEach(lga => {
                                                      const option = document.createElement('option');
                                                      option.value = lga.lga_id;
                                                      option.text = lga.lga_name;
                                                      lgaSelect.add(option);
                                                  });
                                              })
                                              .catch(error => console.error('Error fetching LGAs:', error));
                                      } else {
                                          lgaSelect.innerHTML = '<option value="">Select State of Origin</option>';
                                      }
                                  });
                              </script>


                              <div class="row">
                                  @php $hasBirthDate = !empty($biodetail->birthDate); @endphp
                                  <div class="mb-3 col-md-4">
                                      <label class="form-label"><strong>Day of Birth</strong></label>
                                      <select name="dob" class="form-control" required>
                                          <option value="{{ $hasBirthDate ? date('d', strtotime($biodetail->birthDate)) : '' }}">{{ $hasBirthDate ? date('d', strtotime($biodetail->birthDate)) : 'Select Day' }}</option>
                                          @for ($day = 1; $day <= 31; $day++)
                                              @php $val=str_pad($day, 2, '0' , STR_PAD_LEFT); @endphp
                                              <option value="{{ $val }}">{{ $val }}</option>
                                              @endfor
                                      </select>
                                  </div>
                                  <div class="mb-3 col-md-4">
                                      <label class="form-label"><strong>Month of Birth</strong></label>
                                      <select name="mob" class="form-control" required>
                                          <option value="{{ $hasBirthDate ? date('m', strtotime($biodetail->birthDate)) : '' }}">{{ $hasBirthDate ? date('M', strtotime($biodetail->birthDate)) : 'Select Month' }}</option>
                                          @php
                                          $months = [
                                          '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
                                          '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Aug',
                                          '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'
                                          ];
                                          @endphp
                                          @foreach ($months as $num => $name)
                                          <option value="{{ $num }}">{{ $name }}</option>
                                          @endforeach
                                      </select>
                                  </div>
                                  <div class="mb-3 col-md-4">
                                      <label class="form-label"><strong>Year of Birth</strong></label>
                                      <select name="yob" class="form-control" required>
                                          <option value="{{ $hasBirthDate ? date('Y', strtotime($biodetail->birthDate)) : '' }}">{{ $hasBirthDate ? date('Y', strtotime($biodetail->birthDate)) : 'Select Year' }}</option>
                                          @for ($year = now()->year; $year >= 1970; $year--)
                                          <option value="{{ $year }}">{{ $year }}</option>
                                          @endfor
                                      </select>
                                  </div>
                              </div>
                              <div class="row">
                                  <div class="mb-3 col-md-6">
                                      <label class="form-label"><strong>Permanent Home Address</strong></label>
                                      <input type="text" class="form-control" name="student_homeaddress"
                                          value="{{ old('student_homeaddress', $biodetail->studentHomeAddress) }}"
                                          placeholder="Permanent Home Address" required autocomplete="off">
                                  </div>

                                  <div class="mb-3 col-md-6">
                                      <label class="form-label"><strong>Contact Address</strong></label>
                                      <input type="text" class="form-control" name="contact_address"
                                          value="{{ old('contact_address', $biodetail->contactAddress) }}"
                                          placeholder="Contact Address" required autocomplete="off">
                                  </div>
                              </div>
                              <div class="row">
                                  <div class="mb-3 col-md-6">
                                      <label class="form-label"><strong>Next of Kin Name</strong></label>
                                      <input type="text" class="form-control" name="nok"
                                          value="{{ old('nok', $biodetail->nextofKin) }}"
                                          placeholder="Next of Kin Name" required autocomplete="off">
                                  </div>

                                  <div class="mb-3 col-md-6">
                                      <label class="form-label"><strong>Next of Kin Phone Number</strong></label>
                                      <input type="number" class="form-control" name="nok_tel"
                                          value="{{ old('nok_tel', $biodetail->nextofKinPhoneNo) }}"
                                          placeholder="Next of Kin Phone Number" required autocomplete="off">
                                  </div>
                              </div>

                              <div class="row">
                                  <div class="mb-3 col-md-6">
                                      <label class="form-label"><strong>Next of Kin Address</strong></label>
                                      <input type="text" class="form-control" name="nok_address"
                                          value="{{ old('nok_address', $biodetail->nextofKinAddress) }}"
                                          placeholder="Next of Kin Address" required autocomplete="off">
                                  </div>

                                  <div class="mb-3 col-md-6">
                                      <label class="form-label"><strong>Next of Kin Email Address</strong></label>
                                      <input type="email" class="form-control" name="nok_email"
                                          value="{{ old('nok_email', $biodetail->nextofKinEmail) }}"
                                          placeholder="Next of Kin Email Address" required autocomplete="off">
                                  </div>
                              </div>

                              <div class="row">
                                  <div class="mb-3 col-md-6">
                                      <label class="form-label"><strong>Next of Kin Relationship</strong></label>
                                      <input type="text" class="form-control" name="nok_rel"
                                          value="{{ old('nok_rel', $biodetail->nextofKinRelationship) }}"
                                          placeholder="Next of Kin Relationship" required autocomplete="off">
                                  </div>

                                  <div class="mb-3 col-md-6">
                                      <label class="form-label"><strong>Home Town / Village</strong></label>
                                      <input type="text" class="form-control" name="hometown"
                                          value="{{ old('hometown', $biodetail->homeTown) }}"
                                          placeholder="Home Town / Village" required autocomplete="off">
                                  </div>
                              </div>
                              <div class="row">
                                  @if ($biodetail->profilePicture === 'avatar.jpg')
                                  <div class="mb-3 col-md-12">
                                      <label class="form-label"><strong>Upload Passport (Max. size 100kb, Allowed: .jpg, .jpeg)</strong></label>
                                      <input type="file" class="form-control" name="file" required accept="image/jpeg, image/jpg">
                                      <input type="hidden" class="form-control" name="updatePassport" required value="1">
                                  </div>

                                  <div class="mb-3 col-md-12 alert alert-warning alert-dismissible" role="alert">
                                      <div class="alert-message">
                                          <strong>Warning!</strong> Confirm your profile information before saving
                                      </div>
                                  </div>

                                  @if ($pstat == 1)
                                  <div class="mb-3 col-md-12">
                                      <button type="submit" class="btn btn-primary">Confirm and Save Biodata</button>
                                  </div>
                                  @else
                                  <div class="alert alert-danger alert-dismissible" role="alert">
                                      <div class="alert-message">
                                          <strong>APPLICATION FEE PAYMENT</strong> NOT MADE.
                                      </div>
                                  </div>
                                  @endif
                                  @else
                                  <div class="alert alert-info alert-dismissible" role="alert">
                                      <div class="alert-message">
                                          <strong>Info!</strong> Passport Photo already Uploaded.
                                      </div>
                                  </div>

                                  <div class="mb-3 col-md-12">
                                      <button type="submit" class="btn btn-primary">Update Biodata</button>

                                      @if ($applicantStatus['biodata'] == 1 )
                                      <a href="{{ route('admissions.olevel') }}" class="btn btn-success">
                                          Proceed to add Olevel Result</a>
                                      @endif
                                  </div>
                                  @endif


                              </div>
                          </form>
                      </div>
                  </div>
              </div>
          </div>

          @endsection