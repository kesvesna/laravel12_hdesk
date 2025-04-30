<div class="row">
    <div class="col mb-3">
        <label for="division_id" class="form-label form-label-sm">Кому заявка <span class="text-danger"><b> *</b></span></label>
        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal"
                data-bs-target="#exampleModal2">Инфо
        </button>
        <select name="division_id" id="user_division_id" class="form-select form-select-sm">
            @forelse($divisions as $division)
                <option
                    value="{{$division->id}}" {{old('division_id') === $division->id ? 'selected' : null}}>{{$division->name}}</option>
            @empty
                <option value="">нет данных ...</option>
            @endforelse
        </select>
        @error('division_id')
        <div class="text-danger">{{$message}}</div>
        @enderror
    </div>
</div>
