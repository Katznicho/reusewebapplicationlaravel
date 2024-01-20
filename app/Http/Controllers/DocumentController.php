<?php

namespace App\Http\Controllers;

use App\Models\Verification;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    //
    public function viewDocument(Request $request, Verification $record)
    {
        $url = $record->document_url;

        return view('pdfs.view_agreemnt', ['documentUrl' => $url]);
    }

    public function downloadDocument(Request $request, Verification $record)
    {
        $url = $record->passport;

        return view('pdfs.view_community_document', ['documentUrl' => $url]);
    }
}
