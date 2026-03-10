<!-- Error Session Alert -->
@if (session('error'))
    <div class="alert alert-danger alert-dismissible alert-label-icon rounded-label fade show material-shadow"
        role="alert">
        <i class="ri-error-warning-line label-icon"></i><strong>Error</strong> - {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Validation Errors Alert -->
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible alert-label-icon rounded-label fade show material-shadow"
        role="alert">
        <i class="ri-alert-line label-icon"></i><strong>Validation Error</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Success Alert  -->
@if (session('success'))
    <div class="alert alert-success alert-dismissible alert-label-icon rounded-label fade show material-shadow"
        role="alert">
        <i class="ri-notification-off-line label-icon"></i><strong>Success</strong> - {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Warning Alert  -->
@if (session('warning'))
    <div class="alert alert-warning alert-dismissible alert-label-icon rounded-label fade show material-shadow"
        role="alert">
        <i class="ri-alert-line label-icon"></i><strong>Warning</strong> - {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Info Alert  -->
@if (session('info'))
    <div class="alert alert-info alert-dismissible alert-label-icon rounded-label fade show material-shadow"
        role="alert">
        <i class="ri-airplay-line label-icon"></i><strong>Info</strong> - {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
