<?php namespace Waka\Pdfer\Classes\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Waka\Pdfer\Models\Bloc;

class BlocsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $bloc = new Bloc();
            $bloc->id = $row['id'] ?? null;
            $bloc->name = $row['name'] ?? null;
            $bloc->slug = $row['slug'] ?? null;
            $bloc->contenu = $row['contenu'] ?? null;
            $bloc->description = $row['description'] ?? null;
            $bloc->save();
        }
    }
}
