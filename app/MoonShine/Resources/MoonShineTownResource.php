<?php

namespace App\MoonShine\Resources;

use App\Models\Town;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;


class MoonShineTownResource extends ModelResource
{
    protected string $model = Town::class;
    protected string $title = 'Towns';
    protected ?string $alias = 'towns';

    public function fields(): array
    {
        return [
            ID::make(),
            Text::make('Name', 'name'),
        ];
    }

    public function rules($item): array
    {
        return [];
    }

}
