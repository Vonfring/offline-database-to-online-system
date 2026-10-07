@php
    $h = $harga ?? null;
    // Nilai lama (old) hanya dipakai pada form yang tadi dikirim, agar tidak bocor ke form lain di halaman yang sama.
    $pakaiOld = $h ? (string) old('_harga_id') === (string) $h->id : ! old('_harga_id');
    $nilai = fn ($kolom, $bawaan) => $pakaiOld ? old($kolom, $bawaan) : $bawaan;
@endphp
<div class="col-md-4 mb-3">
    <label class="form-label wajib" for="harga_beli_per_kg{{ $akhiran ?? '' }}">Harga beli / kg</label>
    <div class="input-group">
        <span class="input-group-text">Rp</span>
        <input id="harga_beli_per_kg{{ $akhiran ?? '' }}" type="number" name="harga_beli_per_kg" min="0" step="1" required
               value="{{ $nilai('harga_beli_per_kg', $h ? (float) $h->harga_beli_per_kg : null) }}"
               class="form-control {{ $pakaiOld && $errors->has('harga_beli_per_kg') ? 'is-invalid' : '' }}">
    </div>
</div>
<div class="col-md-4 mb-3">
    <label class="form-label wajib" for="harga_jual_per_kg{{ $akhiran ?? '' }}">Harga jual / kg</label>
    <div class="input-group">
        <span class="input-group-text">Rp</span>
        <input id="harga_jual_per_kg{{ $akhiran ?? '' }}" type="number" name="harga_jual_per_kg" min="0" step="1" required
               value="{{ $nilai('harga_jual_per_kg', $h ? (float) $h->harga_jual_per_kg : null) }}"
               class="form-control {{ $pakaiOld && $errors->has('harga_jual_per_kg') ? 'is-invalid' : '' }}">
    </div>
</div>
<div class="col-md-4 mb-3">
    <label class="form-label wajib" for="berlaku_mulai{{ $akhiran ?? '' }}">Berlaku mulai</label>
    <input id="berlaku_mulai{{ $akhiran ?? '' }}" type="date" name="berlaku_mulai" required
           value="{{ $nilai('berlaku_mulai', $h?->berlaku_mulai?->toDateString() ?? now()->toDateString()) }}"
           class="form-control {{ $pakaiOld && $errors->has('berlaku_mulai') ? 'is-invalid' : '' }}">
</div>
