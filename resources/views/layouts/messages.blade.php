@php
    $toasts = [];

    if (session('success'))  $toasts[] = ['icon' => 'success', 'title' => session('success')];
    if (session('error'))    $toasts[] = ['icon' => 'error',   'title' => session('error')];
    if (session('warning'))  $toasts[] = ['icon' => 'warning', 'title' => session('warning')];
    if (session('info'))     $toasts[] = ['icon' => 'info',    'title' => session('info')];

    if ($errors->any()) {
        $toasts[] = [
            'icon'  => 'error',
            'title' => 'Validasi Gagal',
            'html'  => '<ul style="text-align:left;margin:0;padding-left:1.2em">'
                     . implode('', array_map(fn($e) => '<li>'.e($e).'</li>', $errors->all()))
                     . '</ul>',
        ];
    }
@endphp

@if (count($toasts))
<script>
document.addEventListener('DOMContentLoaded', function () {
    var Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 4500,
        timerProgressBar: true,
        didOpen: function (toast) {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    @foreach ($toasts as $toast)
    Toast.fire(@json($toast));
    @endforeach
});
</script>
@endif
