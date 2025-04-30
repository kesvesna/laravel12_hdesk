<div class="row">
    <div class="col mb-3">
        <label for="name" class="form-label form-label-sm">Фамилия И.О. <span
                class="text-danger"><b>*</b></span></label>
        <input type="text" value="{{old('name')}}" required name="name" class="form-control form-control-sm"
               placeholder="Иванов И.И.">
        @error('name')
        <div class="text-danger">{{$message}}</div>
        @enderror
    </div>
</div>

