<div class="row">
    <div class="col mb-3">
        <label for="phone" class="form-label form-label-sm">Телефон</label>
        <input type="text" id="phone" required value="{{old('phone')}}" name="phone"
               class="form-control form-control-sm" placeholder="+7 904 613 78 62">
        @error('phone')
        <div class="text-danger">{{$message}}</div>
        @enderror
    </div>
</div>

