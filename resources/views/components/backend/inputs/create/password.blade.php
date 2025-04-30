<div class="row">
    <div class="col mb-3">
        <label for="password" class="form-label form-label-sm">Пароль <span class="text-danger"><b>*</b></span></label>
        <input type="password" required name="password" value="{{old('password')}}" class="form-control form-control-sm"
               placeholder="От 8 символов, a-z, A-Z, 0-9,">
        @error('password')
        <div class="text-danger">{{$message}}</div>
        @enderror
    </div>
</div>
