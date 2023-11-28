<?php

namespace App\Http\Controllers;

use App\Helpers\Image;
use App\Http\Requests\StoreMediaRequest;
use App\Http\Requests\UpdateMediaRequest;
use App\Models\Media;
use Illuminate\Support\Facades\File;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mediaDir = public_path('media/images');

        $files = array_diff(scandir($mediaDir), array('..', '.','.gitignore'));
        $data = [];
        foreach ($files as $file){
            try {
                $fileObject = new \SplFileObject($mediaDir . DIRECTORY_SEPARATOR . $file);
                // dd($fileObject->getFileInfo());
                if(Image::isImage($fileObject->getPathname())){
                    $data[] = $fileObject;
                }else {
//                    dump($fileObject);
                }

//                dump($data[0]->getPathname());
//                Image::get($data[0]->getPathname());
//                dd($data[0]->getPath());
            }catch (\Throwable $e){
                // Silent for now.
            }

        }
//        dd($data);
        return view('admin.media.index',[
            'files'=>$data
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMediaRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Media $media)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Media $media)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMediaRequest $request, Media $media)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Media $media)
    {
        //
    }
}
