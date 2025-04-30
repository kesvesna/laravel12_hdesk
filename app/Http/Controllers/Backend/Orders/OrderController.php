<?php

namespace App\Http\Controllers\Backend\Orders;

use App\Http\Controllers\Controller;
use App\Http\Filters\Orders\OrderFilter;
use App\Http\Requests\Orders\OrderFilterRequest;
use App\Http\Requests\Orders\StoreOrderFormRequest;
use App\Http\Requests\Orders\StoreOrderFromTaskFormRequest;
use App\Http\Requests\Orders\StoreOrderFromTrkEquipmentFormRequest;
use App\Http\Requests\Orders\UpdateOrderFormRequest;
use App\Models\DocCommunications\DocCommunication;
use App\Models\Equipments\EquipmentName;
use App\Models\EquipmentSpareParts\EquipmentSparePart;
use App\Models\Orders\Order;
use App\Models\Orders\OrderSparePart;
use App\Models\Orders\OrderStatus;
use App\Models\Rooms\Room;
use App\Models\SpareParts\SparePartName;
use App\Models\Systems\System;
use App\Models\Tasks\Task;
use App\Models\TrkEquipments\TrkEquipment;
use App\Models\TrkRoomRepairs\TrkRoomRepair;
use App\Models\Trks\Trk;
use App\Models\User;
use App\Models\WorkNames\WorkName;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(Order::class, 'order');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(OrderFilterRequest $request): Response
    {
        $data = $request->validated();

        $all_trks = Trk::orderBy('sort_order')->get();

        $filter = app()->make(OrderFilter::class, ['queryParams' => array_filter($data)]);

        $orders = Order::filter($filter)
            ->orderBy('created_at', 'desc')
            ->where('user_division_id', Auth::user()->user_division_id)
            ->paginate(config('backend.orders.pagination'));

        $comments = Order::pluck('comment');

        return \response()->view('backend.orders.index', [
            'orders' => $orders,
            'all_statuses' => OrderStatus::orderBy('name')->get(),
            'old_filters' => $data,
            'all_trks' => $all_trks,
            'spare_part_names' => SparePartName::orderBy('name')->get(),
            'all_account_numbers' => Order::whereNot('account_number', null)->pluck('account_number'),
            'comments' => $comments,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {

        return \response()->view('backend.orders.create', [
            'trks' => Trk::orderBy('sort_order')->get(),
            'systems' => System::orderBy('sort_order')->get(),
            'equipment_names' => EquipmentName::orderBy('name')->get(),
            'rooms' => Room::orderBy('name')->get(),
            'executors' => User::orderBy('name')->get(),
            'works' => WorkName::orderBy('name')->get(),
            'spare_parts' => SparePartName::orderBy('name')->get(),
            'order_status' => OrderStatus::where('name', OrderStatus::NEW)->first(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create_from_task(Task $task): Response
    {
        $this->authorize('create');

        return \response()->view('backend.orders.create_from_task', [
            'trks' => Trk::orderBy('sort_order')->get(),
            'systems' => System::orderBy('sort_order')->get(),
            'equipment_names' => EquipmentName::orderBy('name')->get(),
            'rooms' => Room::orderBy('name')->get(),
            'executors' => User::orderBy('name')->get(),
            'works' => WorkName::orderBy('name')->get(),
            'spare_parts' => SparePartName::orderBy('name')->get(),
            'order_status' => OrderStatus::where('name', OrderStatus::NEW)->first(),
            'task' => $task,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create_from_repair(TrkRoomRepair $repair): Response
    {
        $this->authorize('create', new Order());

        return \response()->view('backend.orders.create_from_repair', [
            'trks' => Trk::orderBy('sort_order')->get(),
            'systems' => System::orderBy('sort_order')->get(),
            'equipment_names' => EquipmentName::orderBy('name')->get(),
            'rooms' => Room::orderBy('name')->get(),
            'executors' => User::orderBy('name')->get(),
            'works' => WorkName::orderBy('name')->get(),
            'spare_parts' => SparePartName::orderBy('name')->get(),
            'order_status' => OrderStatus::where('name', OrderStatus::NEW)->first(),
            'repair' => $repair,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create_from_trk_equipment(TrkEquipment $trk_equipment, EquipmentSparePart $spare_part): Response
    {
        $this->authorize('create', new Order());

        return \response()->view('backend.orders.create_from_trk_equipment', [
            'trks' => Trk::orderBy('sort_order')->get(),
            'systems' => System::orderBy('sort_order')->get(),
            'equipment_names' => EquipmentName::orderBy('name')->get(),
            'rooms' => Room::orderBy('name')->get(),
            'executors' => User::orderBy('name')->get(),
            'works' => WorkName::orderBy('name')->get(),
            'spare_parts' => SparePartName::orderBy('name')->get(),
            'order_status' => OrderStatus::where('name', OrderStatus::NEW)->first(),
            'trk_equipment' => $trk_equipment,
            'equipment_spare_part' => $spare_part,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderFormRequest $request): RedirectResponse
    {

        Log::info('User try to store new order',
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
            ]);

        if ($request->isMethod('post')) {
            try {

                $data = $request->validated();

                $room_name_id = null;
                if (!empty($data['room_name'])) {
                    $room_name_id = Room::where('name', $data['room_name'])->pluck('id')->first();
                    if (empty($room_name_id)) {
                        return redirect()->back()->with('error', 'Нет помещения с таким названием');
                    }
                }

                $equipment_name_id = null;
                if (!empty($data['equipment_name'])) {
                    $equipment_name_id = EquipmentName::where('name', $data['equipment_name'])->pluck('id')->first();
                    if (empty($equipment_name_id)) {
                        return redirect()->back()->with('error', 'Нет оборудования с таким названием');
                    }
                }

                DB::beginTransaction();

                $order = Order::create([
                    'trk_id' => $data['trk_id'],
                    'room_name_id' => $room_name_id,
                    'system_id' => $data['system_id'],
                    'equipment_name_id' => $equipment_name_id,
                    'order_status_id' => $data['order_status_id'],
                    'is_urgency' => (boolean)$data['is_urgency'] ? 1 : 0,
                    'comment' => $data['comment'] ?? null,
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                    'user_division_id' => Auth::user()->user_division_id,
                ]);

                if (count($data['spare_parts']) > 0) {
                    for ($i = 0; $i < count($data['spare_parts']['name']); $i++) {
                        $spare_part_name_id = SparePartName::where('name', $data['spare_parts']['name'][$i])->pluck('id')->first();

                        if (empty($spare_part_name_id)) {
                            return redirect()->back()->with('error', 'Название запчасти ' . $data['spare_parts']['name'][$i] . ' отсутствует. Попросите админа создать название для запчасти.');
                        }

                        OrderSparePart::create([
                            'order_id' => $order->id,
                            'spare_part_name_id' => $spare_part_name_id,
                            'model' => $data['spare_parts']['model'][$i],
                            'value' => $data['spare_parts']['value'][$i],
                            'author_id' => Auth::id(),
                            'last_editor_id' => Auth::id(),
                        ]);
                    }
                }

                DB::commit();
                return redirect()->route('orders.index')->with('success', 'Данные сохранены');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store_from_task(StoreOrderFromTaskFormRequest $request, Task $task): RedirectResponse
    {
        $this->authorize('create', new Order());

        Log::info('User try to store new order from task',
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
            ]);

        if ($request->isMethod('post')) {
            try {

                $data = $request->validated();

                $room_name_id = null;
                if (!empty($data['room_name'])) {
                    $room_name_id = Room::where('name', $data['room_name'])->pluck('id')->first();
                    if (empty($room_name_id)) {
                        return redirect()->back()->with('error', 'Нет помещения с таким названием');
                    }
                }

                $equipment_name_id = null;
                if (!empty($data['equipment_name'])) {
                    $equipment_name_id = EquipmentName::where('name', $data['equipment_name'])->pluck('id')->first();
                    if (empty($equipment_name_id)) {
                        return redirect()->back()->with('error', 'Нет оборудования с таким названием');
                    }
                }

                DB::beginTransaction();

                $order = Order::create([
                    'trk_id' => $data['trk_id'],
                    'room_name_id' => $room_name_id,
                    'system_id' => $data['system_id'],
                    'equipment_name_id' => $equipment_name_id,
                    'order_status_id' => $data['order_status_id'],
                    'comment' => $data['comment'] ?? null,
                    'is_urgency' => (boolean)$data['is_urgency'] ? 1 : 0,
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                    'user_division_id' => Auth::user()->user_division_id,
                ]);

                if (count($data['spare_parts']) > 0) {
                    for ($i = 0; $i < count($data['spare_parts']['name']); $i++) {
                        $spare_part_name_id = SparePartName::where('name', $data['spare_parts']['name'][$i])->pluck('id')->first();

                        if (empty($spare_part_name_id)) {
                            return redirect()->back()->with('error', 'Название запчасти ' . $data['spare_parts']['name'][$i] . ' отсутствует. Сначала создайте его.');
                        }

                        OrderSparePart::create([
                            'order_id' => $order->id,
                            'spare_part_name_id' => $spare_part_name_id,
                            'model' => $data['spare_parts']['model'][$i],
                            'value' => $data['spare_parts']['value'][$i],
                            'author_id' => Auth::id(),
                            'last_editor_id' => Auth::id(),
                        ]);
                    }
                }

                DocCommunication::create([
                    'from_id' => $task->id,
                    'from_type' => get_class($task),
                    'to_id' => $order->id,
                    'to_type' => get_class($order),
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

                DB::commit();
                return redirect()->route('orders.index')->with('success', 'Данные сохранены');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store_from_repair(StoreOrderFromTaskFormRequest $request, TrkRoomRepair $repair): RedirectResponse
    {
        $this->authorize('create', new Order());

        Log::info('User try to store new order from repair',
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
            ]);

        if ($request->isMethod('post')) {
            try {

                $data = $request->validated();

                DB::beginTransaction();

                $order = Order::create([
                    'trk_id' => $repair->trk_room->trk->id,
                    'room_name_id' => $repair->trk_room->room->id,
                    'system_id' => $repair->trk_equipment->system->id,
                    'equipment_name_id' => $repair->trk_equipment->equipment_name->id,
                    'order_status_id' => $data['order_status_id'],
                    'comment' => $data['comment'] ?? null,
                    'is_urgency' => (boolean)$data['is_urgency'] ? 1 : 0,
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                    'user_division_id' => Auth::user()->user_division_id,
                ]);

                if (count($data['spare_parts']) > 0) {
                    for ($i = 0; $i < count($data['spare_parts']['name']); $i++) {
                        $spare_part_name_id = SparePartName::where('name', $data['spare_parts']['name'][$i])->pluck('id')->first();

                        if (empty($spare_part_name_id)) {
                            return redirect()->back()->with('error', 'Название запчасти ' . $data['spare_parts']['name'][$i] . ' отсутствует. Сначала создайте его.');
                        }

                        OrderSparePart::create([
                            'order_id' => $order->id,
                            'spare_part_name_id' => $spare_part_name_id,
                            'model' => $data['spare_parts']['model'][$i],
                            'value' => $data['spare_parts']['value'][$i],
                            'author_id' => Auth::id(),
                            'last_editor_id' => Auth::id(),
                        ]);
                    }
                }

                DocCommunication::create([
                    'from_id' => $repair->id,
                    'from_type' => get_class($repair),
                    'to_id' => $order->id,
                    'to_type' => get_class($order),
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

                DB::commit();

                return redirect()->route('orders.index')->with('success', 'Данные сохранены');

            } catch (\Exception $e) {

                DB::rollBack();
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store_from_trk_equipment(StoreOrderFromTrkEquipmentFormRequest $request, TrkEquipment $trk_equipment): RedirectResponse
    {
        $this->authorize('create', new Order());

        Log::info('User try to store new order from repair',
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
            ]);

        if ($request->isMethod('post')) {
            try {

                $data = $request->validated();

                DB::beginTransaction();

                $order = Order::create([
                    'trk_id' => $trk_equipment->trk_room->trk->id,
                    'room_name_id' => $trk_equipment->trk_room->room->id,
                    'system_id' => $trk_equipment->system->id,
                    'equipment_name_id' => $trk_equipment->equipment_name->id,
                    'order_status_id' => $data['order_status_id'],
                    'comment' => $data['comment'] ?? null,
                    'is_urgency' => (boolean)$data['is_urgency'] ? 1 : 0,
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                    'user_division_id' => Auth::user()->user_division_id,
                ]);

                if (count($data['spare_parts']) > 0) {
                    for ($i = 0; $i < count($data['spare_parts']['name']); $i++) {
                        $spare_part_name_id = SparePartName::where('name', $data['spare_parts']['name'][$i])->pluck('id')->first();

                        if (empty($spare_part_name_id)) {
                            return redirect()->back()->with('error', 'Название запчасти ' . $data['spare_parts']['name'][$i] . ' отсутствует. Сначала создайте его.')->withInput();
                        }

                        OrderSparePart::create([
                            'order_id' => $order->id,
                            'spare_part_name_id' => $spare_part_name_id,
                            'model' => $data['spare_parts']['model'][$i],
                            'value' => $data['spare_parts']['value'][$i],
                            'author_id' => Auth::id(),
                            'last_editor_id' => Auth::id(),
                        ]);
                    }
                }

                DB::commit();

                return redirect()->route('orders.index')->with('success', 'Данные сохранены');

            } catch (\Exception $e) {

                DB::rollBack();
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.')->withInput();
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order): Response
    {
        return \response()->view('backend.orders.show', [
            'order' => $order,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order): Response
    {
        return \response()->view('backend.orders.edit', [
            'order' => $order,
            'trks' => Trk::orderBy('sort_order')->get(),
            'systems' => System::orderBy('sort_order')->get(),
            'equipment_names' => EquipmentName::orderBy('name')->get(),
            'rooms' => Room::orderBy('name')->get(),
            'executors' => User::orderBy('name')->get(),
            'works' => WorkName::orderBy('name')->get(),
            'all_spare_parts' => SparePartName::orderBy('name')->get(),
            'order_statuses' => OrderStatus::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderFormRequest $request, Order $order): RedirectResponse
    {
        Log::info('User try to order',
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
            ]);

        if ($request->isMethod('patch')) {

            try {

                $data = $request->validated();

                $room_name_id = null;
                if (!empty($data['room_name'])) {
                    $room_name_id = Room::where('name', $data['room_name'])->pluck('id')->first();
                    if (empty($room_name_id)) {
                        return redirect()->back()->with('error', 'Нет помещения с таким названием');
                    }
                }

                $equipment_name_id = null;
                if (!empty($data['equipment_name'])) {
                    $equipment_name_id = EquipmentName::where('name', $data['equipment_name'])->pluck('id')->first();
                    if (empty($equipment_name_id)) {
                        return redirect()->back()->with('error', 'Нет оборудования с таким названием');
                    }
                }

                DB::beginTransaction();

                $order->update([
                    'trk_id' => $data['trk_id'],
                    'room_name_id' => $room_name_id,
                    'system_id' => $data['system_id'],
                    'equipment_name_id' => $equipment_name_id,
                    'order_status_id' => $data['order_status_id'],
                    'is_urgency' => (boolean)$data['is_urgency'] ? 1 : 0,
                    'last_editor_id' => Auth::id(),
                    'account_number' => $data['account_number'],
                    'provider' => $data['provider'],
                    'delivery_at' => $data['delivery_at'],
                    'closed_at' => $data['closed_at'],
                ]);

                if (count($data['spare_parts']) > 0) {
                    OrderSparePart::where('order_id', $order->id)->forceDelete();

                    for ($i = 0; $i < count($data['spare_parts']['name']); $i++) {
                        $spare_part_name_id = SparePartName::where('name', $data['spare_parts']['name'][$i])->pluck('id')->first();

                        if (empty($spare_part_name_id)) {
                            return redirect()->back()->with('error', 'Название запчасти ' . $data['spare_parts']['name'][$i] . ' отсутствует. Сначала создайте его.');
                        }

                        OrderSparePart::create([
                            'order_id' => $order->id,
                            'spare_part_name_id' => $spare_part_name_id,
                            'model' => $data['spare_parts']['model'][$i],
                            'value' => $data['spare_parts']['value'][$i],
                            'author_id' => Auth::id(),
                            'last_editor_id' => Auth::id(),
                        ]);
                    }
                }

                DB::commit();
                return redirect()->route('orders.index')->with('success', 'Данные сохранены');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order): RedirectResponse
    {
        Log::info('User try to delete order',
            [
                'user' => Auth::user()->name,
                'order' => $order,
            ]);

        try {

            DB::beginTransaction();

            OrderSparePart::where('order_id', $order->id)->delete();

            $order->update([
                'destroyer_id' => Auth::id(),
            ]);

            $order->delete();

            DB::commit();
        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e);

            return redirect()->back()->with('error', 'Данные не удалены');

        }
        return redirect()->route('orders.index')->with('success', 'Данные удалены');
    }
}
