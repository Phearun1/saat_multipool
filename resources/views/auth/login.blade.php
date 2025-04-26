@extends('layouts.master-without-nav')
@section('title')
    @lang('translation.Login')
@endsection
@section('content')
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-center">
                        <a href="{{ url('index') }}" class="mb-5 d-block auth-logo">
                            <img src="{{ URL::asset('/assets/images/logo-dark.png') }}" alt="" height="22"
                                class="logo logo-dark">
                            <img src="{{ URL::asset('/assets/images/logo-light.png') }}" alt="" height="22"
                                class="logo logo-light">
                        </a>
                    </div>
                </div>
            </div>
            <div class="row align-items-center justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card">

                        <div class="card-body p-4">
                            <div class="text-center mt-2">
                                <h5 class="text-white">Welcome Back !</h5>
                                <p class="text-muted">Sign in</p>
                            </div>
                            <div class="p-2 mt-4">
                                <form method="POST" action="{{ route('login') }}">
                                    @csrf

                                    <!-- Phone Number Field -->
                                    <div class="mb-3">
                                        <label class="form-label " for="phone_number">Phone Number</label>
                                        <input type="text" class="form-control @error('phone_number') is-invalid @enderror"
                                            name="phone_number" value="{{ old('phone_number') }}" id="phone_number"
                                            placeholder="Enter Phone Number">
                                        @error('phone_number')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <!-- Password Field -->
                                    <div class="mb-3">
                                        <label class="form-label" for="userpassword">Password</label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                                            name="password" id="userpassword" placeholder="Enter password">
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="mt-3 text-end">
                                        <button class="btn btn-primary w-sm waves-effect waves-light" type="submit">
                                            Log In
                                        </button>
                                    </div>

                                    <!-- Signup Option -->
                                    <!-- <div class="mt-4 text-center">
                                        <p class="mb-0">Don't have an account ? 
                                            <a href="{{ url('register') }}" class="fw-medium text-primary"> Signup now </a>
                                        </p>
                                    </div> -->
                                </form>
                            </div>

                        </div>
                    </div>  
                </div>
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
@endsection




