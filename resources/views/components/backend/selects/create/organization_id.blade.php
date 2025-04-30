<div class="row">
    <div class="col mb-3">
        <label for="organization_id" class="form-label form-label-sm">Юр. лицо <span
                class="text-danger"><b> *</b></span></label>
        <select name="organization_id" id="organization_id" class="form-select form-select-sm">
            @forelse($organizations as $organization)
                <option
                    value="{{$organization->id}}" {{old('organization_id') === $organization->id ? 'selected' : null}}>{{$organization->name}}</option>
            @empty
                <option value="">нет данных ...</option>
            @endforelse
        </select>
        @error('organization_id')
        <div class="text-danger">{{$message}}</div>
        @enderror
    </div>
</div>
