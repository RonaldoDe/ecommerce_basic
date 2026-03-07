<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AboutSetting;
use App\Models\AboutTeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AboutController extends Controller
{
    public function index()
    {
        $about = AboutSetting::getSingleton();
        $team  = AboutTeamMember::visible()->get();

        return view('web.about', compact('about', 'team'));
    }
    // ── Mostrar formulario de edición ─────────────────────────
    public function edit()
    {
        $about = AboutSetting::getSingleton();
        $team  = AboutTeamMember::orderBy('order')->get();

        return view('admin.pages.about', compact('about', 'team'));
    }

    // ── Guardar cambios de configuración ──────────────────────
    public function update(Request $request)
    {
        $request->validate([
            'hero_label'        => 'nullable|string|max:100',
            'hero_title'        => 'nullable|string|max:200',
            'hero_subtitle'     => 'nullable|string',
            'hero_image'        => 'nullable|image|max:3072',
            'about_title'       => 'nullable|string|max:200',
            'about_description' => 'nullable|string',
            'about_description_2'=> 'nullable|string',
            'about_image'       => 'nullable|image|max:3072',
            'mission_title'     => 'nullable|string|max:200',
            'mission_text'      => 'nullable|string',
            'vision_title'      => 'nullable|string|max:200',
            'vision_text'       => 'nullable|string',
            'values_title'      => 'nullable|string|max:200',
            'values_text'       => 'nullable|string',
            'cta_title'         => 'nullable|string|max:200',
            'cta_description'   => 'nullable|string',
            'cta_btn_text'      => 'nullable|string|max:100',
            'cta_btn_url'       => 'nullable|string|max:255',
            'cta_btn2_text'     => 'nullable|string|max:100',
            'cta_btn2_url'      => 'nullable|string|max:255',
        ]);

        $about = AboutSetting::getSingleton();
        $data  = $request->except(['_token', '_method', 'hero_image', 'about_image',
                                   'stats', 'why_us', 'timeline']);

        // ── Imágenes ──────────────────────────────────────────
        if ($request->hasFile('hero_image')) {
            if ($about->hero_image) Storage::disk('public')->delete($about->hero_image);
            $data['hero_image'] = $request->file('hero_image')->store('about', 'public');
        }
        if ($request->hasFile('about_image')) {
            if ($about->about_image) Storage::disk('public')->delete($about->about_image);
            $data['about_image'] = $request->file('about_image')->store('about', 'public');
        }

        // ── JSON: Stats ───────────────────────────────────────
        $stats = [];
        if ($request->filled('stat_icon')) {
            foreach ($request->stat_icon as $i => $icon) {
                if (empty($icon)) continue;
                $stats[] = [
                    'icon'  => $icon,
                    'value' => $request->stat_value[$i] ?? '',
                    'label' => $request->stat_label[$i] ?? '',
                ];
            }
        }
        $data['stats'] = $stats ?: null;

        // ── JSON: Why us ──────────────────────────────────────
        $whyUs = [];
        if ($request->filled('why_icon')) {
            foreach ($request->why_icon as $i => $icon) {
                if (empty($icon)) continue;
                $whyUs[] = [
                    'icon'        => $icon,
                    'title'       => $request->why_title[$i] ?? '',
                    'description' => $request->why_desc[$i] ?? '',
                ];
            }
        }
        $data['why_us'] = $whyUs ?: null;

        // ── JSON: Timeline ────────────────────────────────────
        $timeline = [];
        if ($request->filled('tl_year')) {
            foreach ($request->tl_year as $i => $year) {
                if (empty($year)) continue;
                $timeline[] = [
                    'year'        => $year,
                    'title'       => $request->tl_title[$i] ?? '',
                    'description' => $request->tl_desc[$i] ?? '',
                ];
            }
        }
        $data['timeline'] = $timeline ?: null;

        $about->update($data);

        return redirect()->route('admin.pages.about.edit')
                         ->with('message', 'Página "Nosotros" actualizada correctamente.')
                         ->with('icon', 'success')
                         ->with('status', 200);
    }

    // ══ EQUIPO ════════════════════════════════════════════════

    public function storeMember(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:150',
            'role'  => 'required|string|max:150',
            'bio'   => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'linkedin' => 'nullable|url|max:255',
            'twitter'  => 'nullable|url|max:255',
            'email'    => 'nullable|email|max:255',
            'order'    => 'nullable|integer',
        ]);

        $data = $request->except(['_token', 'photo']);
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('about/team', 'public');
        }

        AboutTeamMember::create($data);

        return redirect()->route('admin.pages.about.edit')
                         ->with('message', 'Miembro agregado.')
                         ->with('icon', 'success')
                         ->with('status', 200);
    }

    public function updateMember(Request $request, AboutTeamMember $member)
    {
        $request->validate([
            'name'  => 'required|string|max:150',
            'role'  => 'required|string|max:150',
            'bio'   => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'linkedin' => 'nullable|url|max:255',
            'twitter'  => 'nullable|url|max:255',
            'email'    => 'nullable|email|max:255',
            'order'    => 'nullable|integer',
            'active'   => 'nullable|boolean',
        ]);

        $data = $request->except(['_token', '_method', 'photo']);
        $data['active'] = $request->boolean('active');

        if ($request->hasFile('photo')) {
            if ($member->photo) Storage::disk('public')->delete($member->photo);
            $data['photo'] = $request->file('photo')->store('about/team', 'public');
        }

        $member->update($data);

        return redirect()->route('admin.pages.about.edit')
                         ->with('message', 'Miembro actualizado.')
                         ->with('icon', 'success')
                         ->with('status', 200);
    }

    public function destroyMember(AboutTeamMember $member)
    {
        if ($member->photo) Storage::disk('public')->delete($member->photo);
        $member->delete();

        return redirect()->route('admin.pages.about.edit')
                         ->with('message', 'Miembro eliminado.')
                         ->with('icon', 'success')
                         ->with('status', 200);
    }
}