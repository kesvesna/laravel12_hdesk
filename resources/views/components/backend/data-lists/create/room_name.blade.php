<div class="row">
    <div class="col mb-3">
        <label for="room_name" class="form-label form-label-sm">Помещение <span
                class="text-danger"><b>*</b></span></label>
        <input value="{{old('room_name')}}" class="form-control form-control-sm" list="rooms_list" id="room_name"
               name="room_name" placeholder="Начните писать ...">
        <datalist id="rooms_list">
            @forelse($rooms as $room)
                <option value="{{$room->name}}">
            @empty
                <option value="нет данных ...">
            @endforelse
        </datalist>
        @error('room_name')
        <div class="text-danger">{{$message}}</div>
        @enderror
    </div>
</div>
