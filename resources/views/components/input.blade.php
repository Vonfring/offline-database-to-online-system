@props(['nama', 'label', 'nilai' => null, 'tipe' => 'text', 'wajib' => false, 'bantuan' => null])

<div {{ $attributes->only('class')->merge(['class' => 'mb-3']) }}>
    <label for="{{ $nama }}" @class(['form-label', 'wajib' => $wajib])>{{ $label }}</label>
    <input id="{{ $nama }}" name="{{ $nama }}" type="{{ $tipe }}" value="{{ old($nama, $nilai) }}"
           {{ $attributes->except('class')->merge(['class' => 'form-control'.($errors->has($nama) ? ' is-invalid' : '')]) }}
           @required($wajib)>
    @error($nama)<div class="invalid-feedback">{{ $message }}</div>@enderror
    @if ($bantuan)<div class="form-text">{{ $bantuan }}</div>@endif
</div>
