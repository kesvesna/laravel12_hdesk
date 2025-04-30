<?php

namespace App\Http\Controllers\Backend\Contacts;

use App\Http\Controllers\Controller;
use App\Http\Filters\Users\UserFilter;
use App\Http\Requests\Users\UserFilterRequest;
use App\Models\User;
use Illuminate\Http\Response;

class ContactController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        //$this->authorizeResource(Document::class, 'document');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(UserFilterRequest $request): Response
    {
        $data = $request->validated();

        $filter = app()->make(UserFilter::class, ['queryParams' => array_filter($data)]);

        $users = User::filter($filter)
            //->with(['trk', 'building', 'floor', 'room'])
            ->orderBy('created_at', 'desc')
            ->paginate(config('backend.users.pagination'));

        return \response()->view('backend.contacts.index', [
            'users' => $users,
            'old_filters' => $data,
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): Response
    {
        return \response()->view('backend.contacts.show', [
            'user' => $user,
        ]);
    }


}
