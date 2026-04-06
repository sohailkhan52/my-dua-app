<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Font;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FontController extends Controller
{
    /**
     * Allowed font file extensions
     *
     * @var array
     */
    protected $allowedExtensions = ['ttf', 'otf', 'woff', 'woff2'];

    /**
     * Maximum allowed file size in KB (10240 KB = 10 MB)
     *
     * @var int
     */
    protected $maxFileSize = 10240;

    /**
     * Display a listing of all fonts
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch all fonts from database
        $fonts = Font::orderBy("font_name")->get();
        return view("fonts.index", compact('fonts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return view("fonts.create");
    }

    /**
     * Store a newly created font in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'font_name' => 'required|string|max:255',
            'font_file' => 'required|file|max:' . $this->maxFileSize,
        ],
            [
                'file_name.required' => 'Please upload a file.',
                'file_name.file'     => 'The uploaded item must be a valid file.',
                'file_name.max'      => "file size must be  upto $this->maxFileSize",
            ]
        );
        // Get uploaded file
        $file      = $request->file('font_file');
        $extension = strtolower($file->getClientOriginalExtension());

        // Validate file extension
        if (! in_array($extension, $this->allowedExtensions)) {
            return back()->witherrors(['font_file' => "Only TTF,OTF,WOFF,and WOFF2 files are allowed."])->withInput();
        }

        // Generate safe filename
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName     = Str::slug($request->font_name) ?: 'font';
        $fileName     = time() . '_' . $safeName . '_' . uniqid() . '.' . $extension;

        // Store file in public/fonts directory
        $path = $file->storeAs('fonts', $fileName, 'public');

        // Create database record
        Font::create([
            'font_name'         => ucwords($request->font_name),
            'font_path'         => $path,
            'original_filename' => $file->getClientOriginalName(),
            'file_extension'    => $extension,
            'file_size'         => $file->getSize(),
        ]);
        return redirect()->route('fonts.index')
            ->with('success', 'Font uploaded successfully.');

    }

    /**
     * Change the default font by storing selection in session
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeFont(int $id)
    {
        $font = Font::find($id);

        // Store font ID in session if font exists
        if ($font) {

            session(['default_font_id' => $font->id]);

        }
        return back()->with('success', 'Font changed successfully');
    }

    /**
     * Show the form for editing the specified font
     *
     * @param string $id
     * @return \Illuminate\View\View
     */
    public function edit(string $id)
    {
        $font = Font::find($id);
        return view('fonts.edit', compact('font'));
    }

    /**
     * Update the specified font in storage
     *
     * @param Request $request
     * @param Font $font
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Font $font)
    {
        $request->validate([
            'font_name' => 'required|string|max:255',
            'font_file' => 'nullable|file|max:' . $this->maxFileSize,
        ],
            [
                'file_name.file' => 'The uploaded item must be a valid file.',
                'file_name.mix'  => "file must be upto $this->maxFileSize.",
            ]
        );

        // Prepare update data
        $data = ['font_name' => ucwords($request->font_name)];

        // Handle file upload if a new file is provided
        if ($request->hasFile('font_file')) {
            $file      = $request->file('font_file');
            $extension = strtolower($file->getClientOriginalExtension());

            // Validate file extension
            if (! in_array($extension, $this->allowedExtensions)) {
                return back()->withErrors(['font_file' => 'Only TTF, OTF, WOFF, and WOFF2 files are allowed.'])->withInput();
            }

            // Delete old font file if exists
            if ($font->font_path && Storage::disk('public')->exists($font->font_path)) {
                Storage::disk('public')->delete($font->font_path);

            }

            // Generate new filename and store file
            $safeName = Str::slug($request->font_name) ?: 'font';
            $fileName = time() . '_' . $safeName . uniqid() . '.' . $extension;
            $path     = $file->storeAs('fonts', $fileName, 'public');

            // Add file data to update array
            $data['file_path']         = $path;
            $data['original_filename'] = $file->getClientOriginalName();
            $data['file_extension']    = $extension;
            $data['file_size']         = $file->getSize();
        }

        // Update database record
        $font->update($data);
        return redirect()->route('fonts.index')
            ->with('success', 'Font updated successfully.');
    }

    /**
     * Remove the specified font from storage
     *
     * @param Font $font
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Font $font)
    {
        // Delete physical font file from storage
            // Delete old font file if exists
            if ($font->font_path && Storage::disk('public')->exists($font->font_path)) {
                $true=Storage::disk('public')->delete($font->font_path);
                

            }
        // Delete database record
        $font->delete();

        return redirect()->route('fonts.index')
            ->with('success', 'Font deleted successfully.');
    }

    /**
     * Download the font file
     *
     * @param Font $font
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(Font $font)
    {
        // Check if file path exists
        if (! $font->font_path) {
            abort(404, 'File path not found');
        }

        // Return file download response
        return response()->download(
            storage_path('app/public/' . $font->font_path),
            $font->original_filename
        );
    }

}
