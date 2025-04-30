<?php

namespace App\MoonShine\Resources;

use App\Models\Town;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;


class MoonShineTownResource extends ModelResource
{
    protected string $model = Town::class;
    protected string $title = 'Towns';
    protected ?string $alias = 'towns';
    protected bool $stickyTable = true;
    protected int $itemsPerPage = 25;
    protected string $sortColumn = 'created_at';

    protected function indexFields(): iterable
    {
        return [
            Text::make('Name')->customWrapperAttributes(['width' => '10%']),
            Text::make('Alias'),
            Text::make('Created_at'),
        ];
    }

    protected function formFields(): iterable
    {
        return [
            Box::make([
                ID::make(),
                Text::make('Name'),
            ]),
        ];
    }

    protected function detailFields(): iterable
    {
        return [
            Text::make('ID', 'id'),
            Text::make('Name', 'name'),
            Text::make('Alias', 'alias'),

        ];
    }

    public function rules($item): array
    {
        return [
            'name' => ['required', 'string', 'min:3'],
        ];
    }

    protected function filters(): iterable
    {
        return [
            Text::make('Name', 'name'),
        ];
    }

    protected function search(): array
    {
        return ['name'];
        //return ['name.trks'];
    }

}
