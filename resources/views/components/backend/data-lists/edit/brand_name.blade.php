<div class="row">
    <div class="col mb-3">
        <label for="brand_name" class="form-label form-label-sm">Бренд <span class="text-danger"><b>*</b></span></label>
        <input value="{{old('brand_name', $renter_trk_room_brand->brand->name)}}" class="form-control form-control-sm"
               list="brands_list" id="brand_name" name="brand_name" placeholder="Начните писать ...">
        <datalist id="brands_list">
            @forelse($brands as $brand)
                <option value="{{$brand->name}}">
            @empty
                <option value="нет данных ...">
            @endforelse
        </datalist>
        @error('brand_name')
        <div class="text-danger">{{$message}}</div>
        @enderror
    </div>
</div>
