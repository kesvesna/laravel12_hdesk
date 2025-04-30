<div class="row">
    <div class="col mb-3">
        <label for="town_id" class="form-label form-label-sm">Город<span class="text-danger"><b> *</b></span></label>
        <select name="town_id" id="town_id" class="form-select form-select-sm">
            @forelse($towns as $town)
                <option
                    value="{{$town->id}}" {{isset($user->town->id) && $user->town->id === $town->id ? 'selected' : null}}>{{$town->name}}</option>
            @empty
                <option value="">нет данных ...</option>
            @endforelse
        </select>
        @error('town_id')
        <div class="text-danger">{{$message}}</div>
        @enderror
    </div>
</div>
