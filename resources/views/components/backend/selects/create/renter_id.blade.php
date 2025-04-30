<div class="row">
    <div class="col mb-3">
        <label for="renter_id" class="form-label form-label-sm">Арендатор<span
                class="text-danger"><b> *</b></span></label>
        <select name="renter_id" id="renter_id" class="form-select form-select-sm">
            @forelse($renters as $renter)
                <option
                    value="{{$renter->id}}" {{old('renter_id') === $renter->id ? 'selected' : null}}>{{$renter->organization->name}}</option>
            @empty
                <option value="">нет данных ...</option>
            @endforelse
        </select>
        @error('renter_id')
        <div class="text-danger">{{$message}}</div>
        @enderror
    </div>
</div>
