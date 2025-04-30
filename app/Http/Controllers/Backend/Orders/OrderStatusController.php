<?php

namespace App\Http\Controllers\Backend\Orders;

use App\Http\Controllers\Controller;
use App\Http\Filters\OrderStatuses\OrderStatusFilter;
use App\Http\Requests\OrderStatuses\OrderStatusFilterRequest;
use App\Http\Requests\OrderStatuses\StoreOrderStatusFormRequest;
use App\Http\Requests\OrderStatuses\UpdateOrderStatusFormRequest;
use App\Models\Orders\OrderStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderStatusController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(OrderStatus::class, 'order_status');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(OrderStatusFilterRequest $request): Response
    {
        $data = $request->validated();

        $filter = app()->make(OrderStatusFilter::class, ['queryParams' => array_filter($data)]);

        $order_statuses = OrderStatus::filter($filter)
            ->orderBy('created_at', 'desc')
            ->orderBy('name')
            ->paginate(config('backend.order_statuses.pagination'));

        return \response()->view('backend.order_statuses.index', [
            'order_statuses' => $order_statuses,
            'all_order_statuses' => OrderStatus::orderBy('name')->get(),
            'old_filters' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return \response()->view('backend.order_statuses.create', [
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderStatusFormRequest $request): RedirectResponse
    {
        Log::info('User try to store new order_status',
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
            ]);

        if ($request->isMethod('post')) {
            try {
                $data = $request->validated();
                OrderStatus::create([
                    'name' => $data['name'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);
                return redirect()->route('order_statuses.index')->with('success', 'Данные сохранены');
            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Display the specified resource.
     */
    public function show(OrderStatus $order_status): Response
    {
        return \response()->view('backend.order_statuses.show', [
            'order_status' => $order_status,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OrderStatus $order_status): Response
    {
        return \response()->view('backend.order_statuses.edit', [
            'order_status' => $order_status,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderStatusFormRequest $request, OrderStatus $order_status): RedirectResponse
    {
        Log::info('User try to update $order_status name',
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
            ]);

        if ($request->isMethod('patch')) {
            $data = $request->validated();

            $order_status->update([
                'name' => $data['name'],
                'last_editor_id' => Auth::id(),
            ]);
            return redirect()->back()->with('success', 'Изменения сохранены');
        }
        return redirect()->back()->with('error', 'Изменения не сохранены');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrderStatus $order_status): RedirectResponse
    {
        Log::info('User try to delete $order_status',
            [
                'user' => Auth::user()->name,
                'order_status' => $order_status,
            ]);

        try {
            $order_status->update([
                'destroyer_id' => Auth::id(),
            ]);
            $order_status->delete();
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');
        }
        return redirect()->route('order_statuses.index')->with('success', 'Данные удалены');
    }
}
