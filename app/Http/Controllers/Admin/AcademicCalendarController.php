<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\AcademicCalendar;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\AcademicCalendarRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AcademicCalendarController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('manage academic calendars');
        $calendars = AcademicCalendar::orderBy('academic_year', 'desc')->paginate(5);
        return view('admin.academic-calendar.index', compact('calendars'));
    }

    public function create()
    {
        return view('admin.academic-calendar.create');
    }

    public function store(AcademicCalendarRequest $request)
    {
        $filePath = $request->file('file')->store('academic-calendars', 'public');

        AcademicCalendar::create([
            'title' => $request->title,
            'file_path' => $filePath,
            'academic_year' => $request->academic_year,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.academic-calendar.index')
            ->with('success', 'Kalender Akademik berhasil ditambahkan');
    }

    public function edit(AcademicCalendar $academicCalendar)
    {
        return view('admin.academic-calendar.edit', compact('academicCalendar'));
    }

    public function update(AcademicCalendarRequest $request, AcademicCalendar $academicCalendar)
    {
        $data = [
            'title' => $request->title,
            'academic_year' => $request->academic_year,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->hasFile('file')) {
            // Hapus file lama jika ada
            if ($academicCalendar->file_path && Storage::disk('public')->exists($academicCalendar->file_path)) {
                Storage::disk('public')->delete($academicCalendar->file_path);
            }

            $data['file_path'] = $request->file('file')->store('academic-calendars', 'public');
        }

        $academicCalendar->update($data);

        return redirect()->route('admin.academic-calendar.index')
            ->with('success', 'Kalender Akademik berhasil diperbarui');
    }

    public function destroy(AcademicCalendar $academicCalendar)
    {
        // Hapus file dari storage
        if ($academicCalendar->file_path && Storage::disk('public')->exists($academicCalendar->file_path)) {
            Storage::disk('public')->delete($academicCalendar->file_path);
        }

        $academicCalendar->delete();

        return redirect()->route('admin.academic-calendar.index')
            ->with('success', 'Kalender Akademik berhasil dihapus');
    }

    public function setActive(AcademicCalendar $academicCalendar)
    {
        // Nonaktifkan semua kalender
        AcademicCalendar::query()->update(['is_active' => false]);

        // Aktifkan kalender yang dipilih
        $academicCalendar->update(['is_active' => true]);

        return back()->with('success', 'Kalender Akademik berhasil diaktifkan');
    }
}
