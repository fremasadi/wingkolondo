<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Nama</label>
        <input type="text" name="name" class="form-control"
            value="{{ old('name', $user->name ?? '') }}" required>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control"
            value="{{ old('email', $user->email ?? '') }}" required>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">No HP</label>
        <input type="text" name="no_hp" class="form-control"
            value="{{ old('no_hp', $user->no_hp ?? '') }}">
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Role</label>
        <select name="role" class="form-select" required>
            <option value="">-- Pilih Role --</option>
            <option value="admin" @selected(old('role', $user->role ?? '')=='admin')>Admin</option>
            <option value="kurir" @selected(old('role', $user->role ?? '')=='kurir')>Kurir</option>
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control"
            placeholder="{{ isset($user) ? 'Kosongkan jika tidak diubah' : '' }}">
    </div>
</div>

<div class="mt-3">
    <button class="btn btn-primary">{{ $button }}</button>
    <a href="{{ route('users.index') }}" class="btn btn-secondary">Kembali</a>
</div>