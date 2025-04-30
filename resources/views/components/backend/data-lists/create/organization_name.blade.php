<div class="row">
    <div class="col mb-3">
        <label for="exampleDataList" class="form-label form-label-sm">Организация <span
                class="text-danger"><b>*</b></span></label>
        <input required value="{{old('organization_name')}}" class="form-control form-control-sm"
               list="organizations_list" id="organization_name" name="organization_name"
               placeholder="Начните писать ...">
        <datalist id="organizations_list">
            @forelse($organizations as $organization)
                <option value="{{$organization->name}}">
            @empty
                <option value="нет данных ...">
            @endforelse
        </datalist>
        @error('organization_name')
        <div class="text-danger">{{$message}}</div>
        @enderror
    </div>
</div>
