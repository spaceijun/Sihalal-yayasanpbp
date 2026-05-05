<div class="row padding-1 p-1">
    <div class="col-md-12">

        <input type="hidden" name="user_id" value="{{ Auth::id() }}">
        <div class="form-group mb-2 mb20">
            <label class="form-label">{{ __('Nama Lengkap') }}</label>
            <input type="text" class="form-control" value="{{ Auth::user()->name }}" disabled>
        </div>

        {{-- NO TICKET — auto-generate, readonly --}}
        <div class="form-group mb-2 mb20">
            <label for="no_ticket" class="form-label">{{ __('No Ticket') }}</label>
            <input type="text" name="no_ticket" class="form-control @error('no_ticket') is-invalid @enderror"
                value="{{ old('no_ticket', $noTicket ?? $ticket?->no_ticket) }}" id="no_ticket" readonly>
            {!! $errors->first('no_ticket', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        {{-- SUBJECT --}}
        <div class="form-group mb-2 mb20">
            <label for="subject" class="form-label">{{ __('Subject') }}</label>
            <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror"
                value="{{ old('subject', $ticket?->subject) }}" id="subject" placeholder="Subject">
            {!! $errors->first('subject', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        {{-- DESCRIPTION — CKEditor --}}
        <div class="form-group mb-2 mb20">
            <label class="form-label" for="description">{{ __('Description') }} <span
                    class="text-danger">*</span></label>
            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                rows="6" placeholder="Tulis deskripsi tiket di sini...">{{ old('description', $ticket?->description) }}</textarea>
            {!! $errors->first('description', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <script>
            ClassicEditor.create(document.querySelector('#description')).catch(error => console.error(error));
        </script>

        {{-- ── FOTO / LAMPIRAN ── --}}
        <div class="form-group mb-2 mb20">
            <label for="file" class="form-label">{{ __('File') }}</label>

            {{-- Preview file existing (saat edit) --}}
            @if (!empty($ticket?->file))
                <div class="mb-2">
                    @php
                        $ext = pathinfo($ticket->file, PATHINFO_EXTENSION);
                        $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']);
                    @endphp
                    @if ($isImage)
                        <img src="{{ Storage::url($ticket->file) }}" alt="Preview" class="img-thumbnail mb-1"
                            style="max-height: 150px;">
                    @else
                        <a href="{{ Storage::url($ticket->file) }}" target="_blank"
                            class="btn btn-sm btn-outline-secondary">
                            <i class="fa fa-file"></i> Lihat File Saat Ini
                        </a>
                    @endif
                    <div class="form-text text-muted">Upload file baru untuk mengganti file di atas.</div>
                </div>
            @endif

            <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" id="file"
                accept=".jpg,.jpeg,.png,.gif,.webp,.bmp,.svg,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar">

            <div class="form-text text-muted">
                Format: JPG, PNG, JPEG, PDF &mdash; Maks. <strong>5 MB</strong>
            </div>

            @error('file')
                <div class="invalid-feedback d-block"><strong>{{ $message }}</strong></div>
            @enderror
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>
