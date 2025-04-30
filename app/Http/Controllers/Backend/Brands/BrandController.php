<?php

namespace App\Http\Controllers\Backend\Brands;

use App\Http\Controllers\Controller;
use App\Http\Filters\Brands\BrandFilter;
use App\Http\Requests\Brands\BrandFilterRequest;
use App\Http\Requests\Brands\StoreBrandFormRequest;
use App\Http\Requests\Brands\UpdateBrandFormRequest;
use App\Models\Brands\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(Brand::class, 'brand');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(BrandFilterRequest $request): Response
    {
        //$this->authorize('viewAny');

        $data = $request->validated();

        $filter = app()->make(BrandFilter::class, ['queryParams' => array_filter($data)]);

        $brands = Brand::filter($filter)
            ->orderBy('created_at', 'desc')
            ->orderBy('name')
            ->paginate(config('backend.brands.pagination'));

        return \response()->view('backend.brands.index', [
            'brands' => $brands,
            'all_brands' => Brand::orderBy('name')->get(),
            'old_filters' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return \response()->view('backend.brands.create', [
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBrandFormRequest $request): RedirectResponse
    {
        Log::info('User try to store new brand',
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
            ]);

        if ($request->isMethod('post')) {
            try {
                $data = $request->validated();
                Brand::create([
                    'name' => $data['name'],
                    'alias' => Str::slug($data['name']),
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);
                return redirect()->route('brands.index')->with('success', 'Данные сохранены');
            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');

    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand): Response
    {
        return \response()->view('backend.brands.show', [
            'brand' => $brand,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand): Response
    {
        return \response()->view('backend.brands.edit', [
            'brand' => $brand,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBrandFormRequest $request, Brand $brand): RedirectResponse
    {
        Log::info('User try to update new brand',
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
            ]);

        if ($request->isMethod('patch')) {
            $data = $request->validated();

            $brand->update([
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
    public function destroy(Brand $brand): RedirectResponse
    {
        Log::info('User try to delete new brand',
            [
                'user' => Auth::user()->name,
                'brand' => $brand,
            ]);

        try {

            if(count($brand->trk_renters) > 0)
            {
                return redirect()->back()->with('error', 'Нельзя удалять бренд. Он используется в профилях арендаторов.');
            }

            if(count($brand->trk_counters) > 0)
            {
                return redirect()->back()->with('error', 'Нельзя удалять бренд. Он используется в счетчиках.');
            }

            if(count($brand->admin_apps) > 0)
            {
                return redirect()->back()->with('error', 'Нельзя удалять бренд. Он используется в заявках.');
            }

            $brand->update([
                'destroyer_id' => Auth::id(),
            ]);

            $brand->delete();

        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');
        }
        return redirect()->route('brands.index')->with('success', 'Данные удалены');

    }
}
