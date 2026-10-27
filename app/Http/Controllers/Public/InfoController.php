<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CertificationType;
use App\Models\DocumentRequirement;
use App\Models\Tuk;
use Illuminate\View\View;

class InfoController extends Controller
{
    public function certifications(): View
    {
        return view('public.info.certifications', [
            'certificationTypes' => CertificationType::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function tuks(): View
    {
        return view('public.info.tuks', [
            'tuks' => Tuk::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function documentRequirements(): View
    {
        return view('public.info.document-requirements', [
            'documentRequirements' => DocumentRequirement::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
        ]);
    }
}
