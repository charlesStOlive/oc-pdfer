<?php namespace Waka\Pdfer\Classes\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Waka\Pdfer\Models\WakaPdf;

class WakaPdfsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $wakaPdf = new WakaPdf();
            $wakaPdf->id = $row['id'] ?? null;
            $wakaPdf->name = $row['name'] ?? null;
            $wakaPdf->slug = $row['slug'] ?? null;
            $wakaPdf->pdf_name = $row['pdf_name'] ?? null;
            $wakaPdf->data_source = $row['data_source'] ?? null;
            $wakaPdf->layout_id = $row['layout_id'] ?? null;
            $wakaPdf->template = $row['template'] ?? null;
            $wakaPdf->model_functions = json_decode($row['model_functions'] ?? null);
            $wakaPdf->images = json_decode($row['images'] ?? null);
            $wakaPdf->test_id = $row['test_id'] ?? null;
            $wakaPdf->save();
        }
    }
}
