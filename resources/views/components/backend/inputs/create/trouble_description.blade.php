<div class="row">
    <div class="col mb-3">
        <label for="trouble_description" class="form-label form-label-sm">Проблема <span
                class="text-danger"><b>*</b></span></label>
        <textarea required name="trouble_description" class="form-control form-control-sm"
                  placeholder="Где проблема и в чем она">{{old('trouble_description')}}</textarea>
        @error('trouble_description')
        <div class="text-danger">{{$message}}</div>
        @enderror
    </div>
</div>

