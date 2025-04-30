<div class="row">
    <div class="col mb-3">
        <label for="updated_at" class="form-label form-label-sm">Исправлен</label>
        <input type="updated_at" readonly name="created_at" value="{{$user->updated_at}}"
               class="form-control form-control-sm">
        @error('updated_at')
        <div class="text-danger">{{$message}}</div>
        @enderror
    </div>
</div>

