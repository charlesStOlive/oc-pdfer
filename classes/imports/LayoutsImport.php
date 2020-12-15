<?php namespace Waka\Pdfer\Classes\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Waka\Pdfer\Models\Layout;

class LayoutsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $layout = new Layout();
            $layout->id = $row['id'] ?? null;
            $layout->name = $row['name'] ?? null;
            $layout->contenu = $row['contenu'] ?? null;
            $layout->AddCss = $row['AddCss'] ?? null;
            $layout->save();
        }
    }
}
