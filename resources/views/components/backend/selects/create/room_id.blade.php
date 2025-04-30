<div class="row">
    <div class="col mb-3">
        <label for="room_id" class="form-label form-label-sm">Помещение<span
                class="text-danger"><b> *</b></span></label>
        <select required name="room_id" id="room_id" class="form-select form-select-sm">
            @forelse($rooms as $room)
                <option
                    value="{{$room->id}}" {{old('room_id') === $room->id ? 'selected' : null}}>{{$room->name}}</option>
            @empty
                <option value="">нет данных ...</option>
            @endforelse
        </select>
        @error('room_id')
        <div class="text-danger">{{$message}}</div>
        @enderror
    </div>
</div>
