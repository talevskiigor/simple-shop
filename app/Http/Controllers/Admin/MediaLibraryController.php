<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\{MediaFiles, MediaReferences};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;

class MediaLibraryController extends Controller
{
    public function index(Request $request)
    {
        $query = Media::query()->where('path', 'like', 'images/%')->orderByDesc('id');
        if ($request->filled('q')) $query->where('name', 'like', '%'.mb_substr((string) $request->query('q'), 0, 100).'%');
        if (in_array($request->query('type'), ['image', 'video'])) $query->where('type', $request->query('type'));
        $items = $query->paginate(36)->withQueryString();
        if ($request->expectsJson()) return response()->json([
            'data' => $items->getCollection()->map(fn ($item) => $this->payload($item)), 'next' => $items->nextPageUrl(),
        ]);
        return view('admin.media.index', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate(['file' => ['required', 'file', 'max:'.config('media.max_upload_kb'), 'mimetypes:image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm'], 'alt' => 'nullable|string|max:255']);
        $upload = $request->file('file');
        $mime = $upload->getMimeType();
        $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif', 'video/mp4' => 'mp4', 'video/webm' => 'webm'];
        $type = str_starts_with($mime, 'image/') ? 'image' : 'video';
        $size = $type === 'image' ? @getimagesize($upload->getPathname()) : null;
        if ($type === 'image' && (!$size || $size[0] * $size[1] > config('media.max_pixels'))) throw ValidationException::withMessages(['file' => 'The image is invalid or exceeds 20 megapixels.']);
        if ($type === 'image') {
            $decoded = match ($mime) {
                'image/jpeg' => @imagecreatefromjpeg($upload->getPathname()),
                'image/png' => @imagecreatefrompng($upload->getPathname()),
                'image/webp' => @imagecreatefromwebp($upload->getPathname()),
                'image/gif' => @imagecreatefromgif($upload->getPathname()),
            };
            if (!$decoded) throw ValidationException::withMessages(['file' => 'The image is damaged and cannot be decoded.']);
            imagedestroy($decoded);
        }
        $hash = hash_file('sha256', $upload->getPathname());
        $item = Media::where('sha256', $hash)->first();
        if (!$item || !app(MediaFiles::class)->resolve($item->path)) {
            $path = 'images/uploads/'.$hash.'.'.$extensions[$mime];
            $target = config('media.root').'/'.$path;
            File::ensureDirectoryExists(dirname($target));
            $temporary = tempnam(dirname($target), '.upload-');
            try {
                if (!copy($upload->getPathname(), $temporary) || !rename($temporary, $target)) throw new \RuntimeException('Unable to store media.');
                chmod($target, 0644);
            } finally { if (is_file($temporary)) unlink($temporary); }
            $item = Media::create(['name' => mb_substr(pathinfo($upload->getClientOriginalName(), PATHINFO_FILENAME), 0, 255), 'path' => $path, 'file' => basename($path), 'w' => $size[0] ?? null, 'h' => $size[1] ?? null, 'type' => $type, 'mime' => $mime, 'bytes' => filesize($target), 'sha256' => $hash, 'alt' => $request->input('alt')]);
        }
        if ($request->expectsJson()) return response()->json($this->payload($item), 201);
        return redirect()->route('media.index')->with('status', 'Media uploaded. Identical files are reused.');
    }

    public function update(Request $request, Media $media)
    {
        $media->update($request->validate(['name' => 'required|string|max:255', 'alt' => 'nullable|string|max:255']));
        return back()->with('status', 'Media details saved.');
    }

    public function destroy(Media $media)
    {
        if (app(MediaReferences::class)->used($media->path)) return back()->withErrors(['media' => 'This file is used by a product, page, template or order. Remove its references first.']);
        // Physical cleanup is separately audited and archived by media:reconcile.
        $media->delete();
        return back()->with('status', 'Media removed from the library. The recoverable original will be included in the next audited cleanup.');
    }

    private function payload(Media $item): array
    {
        return ['id' => $item->id, 'name' => $item->name, 'alt' => $item->alt ?? '', 'path' => $item->path, 'type' => $item->type, 'url' => app(MediaFiles::class)->url($item->path), 'embed' => $item->type === 'image' ? \App\Helpers\Image::get($item->path, 1024, null, 80) : app(MediaFiles::class)->url($item->path), 'preview' => $item->type === 'image' ? \App\Helpers\Image::get($item->path, 256) : null];
    }
}
