@extends('layouts.guest')
@section('title', '503 Service Unavailable')
@section('content')
    <!-- auth-page wrapper -->
    <div class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
        <div class="bg-overlay"></div>
        <!-- auth-page content -->
        <div class="auth-page-content overflow-hidden pt-lg-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-5">
                        <div class="card overflow-hidden card-bg-fill galaxy-border-none">
                            <div class="card-body p-4">
                                <div class="text-center">
                                    <lord-icon class="avatar-xl" src="https://cdn.lordicon.com/etwtznjn.json" trigger="loop"
                                        colors="primary:#405189,secondary:#0ab39c"></lord-icon>
                                    <h1 class="text-primary mb-4">Oops !</h1>
                                    <h4 class="text-uppercase">Server Sibuk</h4>
                                    <p class="text-muted mb-4">Server sedang sibuk, silakan coba lagi nanti!</p>
                                    <a href="{{ url()->previous() }}" class="btn btn-success"><i
                                            class="mdi mdi-home me-1"></i>Back to
                                        home</a>
                                </div>
                            </div>
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->

                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end auth page content -->
    </div>
    <!-- end auth-page-wrapper -->

@endsection
