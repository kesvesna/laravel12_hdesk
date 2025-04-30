<div class="row">
    <div class="col mb-3">
        <label for="trk_id" class="form-label form-label-sm">ТРК<span class="text-danger"><b> *</b></span></label>
        <select name="trk_id" id="trk_id" class="form-select form-select-sm">
            @forelse($trks as $trk)
                <option
                    value="{{$trk->id}}" {{isset($user->trk->id) && $user->trk->id === $trk->id ? 'selected' : null}}>{{$trk->name}}</option>
            @empty
                <option value="">нет данных ...</option>
            @endforelse
        </select>
        @error('trk_id')
        <div class="text-danger">{{$message}}</div>
        @enderror
    </div>
</div>
