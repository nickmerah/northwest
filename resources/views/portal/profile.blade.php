@extends('layouts.portal')

@section('content')
    <h2>Biodata</h2>

    <hr>

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>

    @endif

    <form method="POST" action="{{ route('updateprofile') }}">
        @csrf

        <div class="form-group">
            <label for="fullName"><strong>Full Names</strong></label>
            <input type="text" class="form-control"
                   value="{{ $student->surname }} {{ $student->firstname }} {{ $student->othernames }}" disabled>
        </div>
        <hr>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <div class="form-group">
            <label for="marital_status"><strong>Marital Status</strong></label>
            <select class="form-control @error('marital_status') is-invalid @enderror" id="marital_status"
                    name="marital_status" required>
                <option value="">Select Marital Status</option>
                <option value="Single" {{ $student->marital_status == 'Single' ? 'selected' : '' }}>Single</option>
                <option value="Married" {{ $student->marital_status == 'Married' ? 'selected' : '' }}>Married</option>
                <option value="Widow" {{ $student->marital_status == 'Widow' ? 'selected' : '' }}>Widow</option>
                <option value="Widower" {{ $student->marital_status == 'Widower' ? 'selected' : '' }}>Widower</option>
            </select>
            @error('marital_status')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="contact_address"><strong>Contact Address</strong></label>
            <textarea class="form-control @error('contact_address') is-invalid @enderror" id="contact_address" rows="3"
                      name="contact_address" required>{{ old('contact_address', $student->contact_address) }}</textarea>
            @error('contact_address')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="student_homeaddress"><strong>Home Address</strong></label>
            <textarea class="form-control @error('student_homeaddress') is-invalid @enderror" id="student_homeaddress"
                      rows="3" name="student_homeaddress"
                      required>{{ old('student_homeaddress', $student->student_homeaddress) }}</textarea>
            @error('student_homeaddress')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="std_genotype"><strong>Genotype</strong></label>
            <select class="form-control @error('std_genotype') is-invalid @enderror" id="std_genotype"
                    name="std_genotype" required>
                <option value="">Select Genotype</option>
                <option value="AA" {{ $student->std_genotype == 'AA' ? 'selected' : '' }}>AA</option>
                <option value="AS" {{ $student->std_genotype == 'AS' ? 'selected' : '' }}>AS</option>
                <option value="AC" {{ $student->std_genotype == 'AC' ? 'selected' : '' }}>AC</option>
                <option value="SS" {{ $student->std_genotype == 'SS' ? 'selected' : '' }}>SS</option>
                <option value="SC" {{ $student->std_genotype == 'SC' ? 'selected' : '' }}>SC</option>
                <option value="CC" {{ $student->std_genotype == 'CC' ? 'selected' : '' }}>CC</option>
            </select>
            @error('std_genotype')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="std_bloodgrp"><strong>Blood Group</strong></label>
            <select class="form-control @error('std_bloodgrp') is-invalid @enderror" id="std_bloodgrp"
                    name="std_bloodgrp" required>
                <option value="">Select Blood Group</option>
                <option value="A+" {{ $student->std_bloodgrp == 'A+' ? 'selected' : '' }}>A+</option>
                <option value="A-" {{ $student->std_bloodgrp == 'A-' ? 'selected' : '' }}>A-</option>
                <option value="B+" {{ $student->std_bloodgrp == 'B+' ? 'selected' : '' }}>B+</option>
                <option value="B-" {{ $student->std_bloodgrp == 'B-' ? 'selected' : '' }}>B-</option>
                <option value="AB+" {{ $student->std_bloodgrp == 'AB+' ? 'selected' : '' }}>AB+</option>
                <option value="AB-" {{ $student->std_bloodgrp == 'AB-' ? 'selected' : '' }}>AB-</option>
                <option value="O+" {{ $student->std_bloodgrp == 'O+' ? 'selected' : '' }}>O+</option>
                <option value="O-" {{ $student->std_bloodgrp == 'O-' ? 'selected' : '' }}>O-</option>
            </select>
            @error('std_bloodgrp')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>


        <div class="form-group">
            <label for="student_email"><strong>Email address</strong></label>
            <input type="email" class="form-control @error('student_email') is-invalid @enderror" id="student_email"
                   name="student_email" value="{{ old('student_email', $student->student_email) }}" required>
            @error('student_email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="student_mobiletel"><strong>Phone Number</strong></label>
            <input type="tel" class="form-control @error('student_mobiletel') is-invalid @enderror"
                   name="student_mobiletel" value="{{ old('student_mobiletel', $student->student_mobiletel) }}"
                   required>
            @error('student_mobiletel')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="next_of_kin"><strong>Next of Kin Name</strong></label>
            <input type="text" class="form-control @error('next_of_kin') is-invalid @enderror" name="next_of_kin"
                   value="{{ old('next_of_kin', $student->next_of_kin) }}" required>
            @error('next_of_kin')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="nok_tel"><strong>Next of Kin Phone Number</strong></label>
            <input type="tel" class="form-control @error('nok_tel') is-invalid @enderror" name="nok_tel"
                   value="{{ old('nok_tel', $student->nok_tel) }}" required>
            @error('nok_tel')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="nok_address"><strong>Next of Kin Address</strong></label>
            <textarea class="form-control @error('nok_address') is-invalid @enderror" name="nok_address" rows="3"
                      required>{{ old('nok_address', $student->nok_address) }}</textarea>
            @error('nok_address')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="hometown"><strong>Home Town / Village </strong></label>
            <input type="text" class="form-control @error('hometown') is-invalid @enderror" name="hometown"
                   value="{{ old('hometown', $student->hometown) }}" required>
            @error('hometown')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <hr>
        <div class="form-group">
            <label for="fullName"><strong>Birthdate</strong></label>
            <input type="text" class="form-control" id="birthdate"
                   value="{{ \Carbon\Carbon::parse($student->birthdate)->format('l, F j, Y') }}" disabled>
        </div>

        <div class="form-group">
            <label for="gender"><strong>Gender</strong></label>
            <select class="form-control" id="gender" name="gender" disabled>
                <option value="{{ empty($student->gender) ? '' : $student->gender }}"
                        {{ empty($student->gender) ? 'selected' : '' }} required>
                    {{ empty($student->gender) ? 'Select Gender' : $student->gender }}
                </option>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
        </div>
        <div class="form-group">
            <label for="fullName"><strong>State of Origin</strong></label>
            <input type="text" class="form-control" id="state_of_origin"
                   value="{{ optional($student->stateor)->state_name ?? 'NA' }}" disabled>
        </div>
        <div class="form-group">
            <label for="fullName"><strong>LGA</strong></label>
            <input type="text" class="form-control" id="local_gov"
                   value="{{ optional($student->lga)->lga_name ?? 'NA' }}" disabled>
        </div>
        <div class="form-group">
            <label for="phoneNumber"><strong>Programme</strong></label>
            <input type="tel" class="form-control" value="{{ $student->programme->programme_name }}" disabled>
        </div>
        <div class="form-group">
            <label for="phoneNumber"><strong>Programme Type</strong></label>
            <input type="tel" class="form-control" value="{{ $student->programmeType->programmet_name }}" disabled>
        </div>
        <div class="form-group">
            <label for="phoneNumber"><strong>School</strong></label>
            <input type="tel" class="form-control" value="{{ $student->school->faculties_name }}" disabled>
        </div>
        <div class="form-group">
            <label for="phoneNumber"><strong>Department</strong></label>
            <input type="tel" class="form-control" value="{{ $student->department->departments_name }}" disabled>
        </div>

        <div class="form-group">
            <label for="phoneNumber"><strong>Course of Study</strong></label>
            <input type="tel" class="form-control" value="{{ $student->departmentOption->programme_option }}" disabled>
        </div>

        <div class="form-group">
            <label for="phoneNumber"><strong>Level</strong></label>
            <input type="tel" class="form-control" value="{{ $student->level->level_name }}" disabled>
        </div>

        <button type="submit" class="btn btn-orange btn-block">Update Profile</button>
    </form>
@endsection
