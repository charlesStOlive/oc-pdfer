<?php namespace Waka\Pdfer\Classes;

use Event;

class PdfQueueCreator
{

    public function fire($job, $data)
    {
        if ($job) {
            Event::fire('job.start.pdf', [$job, 'Création de PDFs ']);
        }

        //trace_log($data);

        $listIds = $data['listIds'];
        $productorId = $data['productorId'];
        //trace_log($productorId);
        $pdf = new PdfCreator($productorId);
        $lot = $data['lot'] ?? false;

        foreach ($listIds as $modelId) {
            $pdf->renderCloud($modelId, $lot);
        }

        if ($job) {
            Event::fire('job.end.email', [$job]);
            $job->delete();
        }
    }
}
