<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{CertificateIssue, CertificateTemplate, User, Course};
use Illuminate\Http\Request;

class CertificateIssueController extends Controller
{
    public function index(Request $r)
    {
        $issues = CertificateIssue::query()
            ->with(['template','user','course'])
            ->when($r->filled('q'), function($q) use ($r) {
                $q->where('serial','like','%'.$r->q.'%')
                  ->orWhereHas('user', fn($u)=>$u->where('name','like','%'.$r->q.'%'));
            })
            ->when($r->filled('assessment_type'), fn($q)=>$q->where('assessment_type',$r->assessment_type))
            ->latest('id')->paginate(20)->withQueryString();

        return view('admin.certificate_issues.index', compact('issues'));
    }

    public function show(CertificateIssue $certificate_issue)
    {
        $certificate_issue->load(['template','user','course']);
        return view('admin.certificate_issues.show', ['issue'=>$certificate_issue]);
    }

    public function destroy(CertificateIssue $certificate_issue)
    {
        $certificate_issue->delete();
        return back()->with('success','Issue deleted');
    }
}
