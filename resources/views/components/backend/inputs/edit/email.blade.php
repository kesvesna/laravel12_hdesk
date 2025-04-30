<div class="row">
    <div class="col mb-3">
        <label for="email" class="form-label form-label-sm">Почта (пустая = старая)</label>
        <input type="email" name="email" class="form-control form-control-sm" placeholder="{{$user->email}}">
        @error('name')
        <div class="text-danger">{{$message}}</div>
        @enderror
    </div>
</div>

