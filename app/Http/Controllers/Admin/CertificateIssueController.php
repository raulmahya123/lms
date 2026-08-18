<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{CertificateIssue, CertificateTemplate, User, Course};
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CertificateIssueController extends Controller
{
    public function index(Request $r)
    {
        $filters = $r->validate([
            'q'               => ['nullable', 'string', 'max:100'],
            'assessment_type' => ['nullable', Rule::in(['course', 'psych'])],
        ]);
        $term = trim((string) ($filters['q'] ?? ''));

        $issues = CertificateIssue::query()
            ->select(['id', 'template_id', 'user_id', 'course_id', 'assessment_type', 'assessment_id', 'serial', 'score', 'issued_at', 'created_at'])
            ->with(['template:id,name', 'user:id,name,email', 'course:id,title'])
            ->when($term !== '', function($q) use ($term) {
                $q->where(function ($w) use ($term) {
                    $w->where('serial','like','%'.$term.'%')
                      ->orWhereHas('user', fn($u)=>$u->where('name','like','%'.$term.'%'));
                });
            })
            ->when($filters['assessment_type'] ?? null, fn($q, $type)=>$q->where('assessment_type',$type))
            ->latest('id')->paginate(20)->withQueryString();

        return view('admin.certificate_issues.index', compact('issues'));
    }

    public function show(CertificateIssue $certificate_issue)
    {
        $certificate_issue->load(['template:id,name,background_url,fields_json,svg_json', 'user:id,name,email', 'course:id,title']);
        return view('admin.certificate_issues.show', ['issue'=>$certificate_issue]);
    }

    public function destroy(CertificateIssue $certificate_issue)
    {
        $certificate_issue->delete();
        return back()->with('success','Issue deleted');
    }
}
