<div class="row">
    <div class="col mb-3">
        <label for="brand_id" class="form-label form-label-sm">Бренд<span class="text-danger"><b> *</b></span></label>
        <select name="brand_id" id="brand_id" class="form-select form-select-sm">
            @forelse($brands as $brand)
                <option
                    value="{{$brand->id}}" {{old('brand_id') === $brand->id ? 'selected' : null}}>{{$brand->name}}</option>
            @empty
                <option value="">нет данных ...</option>
            @endforelse
        </select>
        @error('brand_id')
        <div class="text-danger">{{$message}}</div>
        @enderror
    </div>
</div>
