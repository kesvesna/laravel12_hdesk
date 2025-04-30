<div class="row">
    <div class="col mb-3">
        <label for="user_function_id" class="form-label form-label-sm">Должность <span
                class="text-danger"><b> *</b></span></label>
        <select name="user_function_id" id="user_function_id" class="form-select form-select-sm">
            @forelse($functions as $function)
                <option
                    value="{{$function->id}}" {{old('user_function_id') === $function->id ? 'selected' : null}}>{{$function->name}}</option>
            @empty
                <option value="">нет данных ...</option>
            @endforelse
        </select>
        @error('user_function_id')
        <div class="text-danger">{{$message}}</div>
        @enderror
    </div>
</div>
