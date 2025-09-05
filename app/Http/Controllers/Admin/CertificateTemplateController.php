<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificateTemplate;
use Illuminate\Http\Request;

class CertificateTemplateController extends Controller
{
    public function index(Request $r)
    {
        $templates = CertificateTemplate::query()
            ->when($r->filled('q'), fn($q)=>$q->where('name','like','%'.$r->q.'%'))
            ->latest('id')->paginate(20)->withQueryString();

        return view('admin.certificate_templates.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.certificate_templates.create');
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name'          => ['required','string','max:160'],
            'background_url'=> ['nullable','url'],
            'fields_json'   => ['nullable','array'],
            'svg_json'      => ['nullable','array'],
            'is_active'     => ['nullable','boolean'],
        ]);
        $data['is_active'] = (bool)($data['is_active'] ?? true);

        $tpl = CertificateTemplate::create($data);
        return redirect()->route('admin.certificate-templates.show', $tpl)->with('success','Template created');
    }

    public function show(CertificateTemplate $certificate_template)
    {
        return view('admin.certificate_templates.show', ['template'=>$certificate_template]);
    }

    public function edit(CertificateTemplate $certificate_template)
    {
        return view('admin.certificate_templates.edit', ['template'=>$certificate_template]);
    }

    public function update(Request $r, CertificateTemplate $certificate_template)
    {
        $data = $r->validate([
            'name'          => ['required','string','max:160'],
            'background_url'=> ['nullable','url'],
            'fields_json'   => ['nullable','array'],
            'svg_json'      => ['nullable','array'],
            'is_active'     => ['required','boolean'],
        ]);

        $certificate_template->update($data);
        return redirect()->route('admin.certificate-templates.show', $certificate_template)->with('success','Template updated');
    }

    public function destroy(CertificateTemplate $certificate_template)
    {
        $certificate_template->delete();
        return back()->with('success','Template deleted');
    }
}
