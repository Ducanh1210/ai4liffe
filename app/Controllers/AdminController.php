<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\AssessmentModel;

class AdminController extends Controller
{
    public function stats(): void
    {
        $model = new AssessmentModel();
        $summary = $model->summaryByMajor();
        $this->view('stats', [
            'summary' => $summary,
        ]);
    }
}


