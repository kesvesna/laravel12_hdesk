<div class="row">
    <div class="col mb-3">
        <label for="email" class="form-label form-label-sm">Почта <span class="text-danger"><b>*</b></span></label>
        <input type="email" required name="email" value="{{old('email')}}" class="form-control form-control-sm"
               placeholder="i.ivanov@fortgroup.ru">
        @error('name')
        <div class="text-danger">{{$message}}</div>
        @enderror
    </div>
</div>

