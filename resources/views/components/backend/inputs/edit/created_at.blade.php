<div class="row">
    <div class="col mb-3">
        <label for="created_at" class="form-label form-label-sm">Создан</label>
        <input type="created_at" readonly name="created_at" value="{{$user->created_at}}"
               class="form-control form-control-sm">
        @error('created_at')
        <div class="text-danger">{{$message}}</div>
        @enderror
    </div>
</div>

