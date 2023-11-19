<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Contract\Storage;

class ProductController extends Controller
{
    protected $database;
    protected $firebaseStorage;

    public function __construct(Database $database, Storage $firebaseStorage)
    {
        $this->database = $database;
        $this->firebaseStorage = $firebaseStorage;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = $this->database->getReference('products')->getValue();
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $category = $this->database->getReference('category')->getValue();
        return view('products.createProduct', compact('category'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $bucket = $this->firebaseStorage->getBucket();
            $object = $bucket->upload(file_get_contents($file->getPathname()), [
                'name' => 'images/' . $file->getClientOriginalName(),
            ]);

            $imageUrl = $object->signedUrl(now()->addYears(24));
        }

        $postData = [
            'name'=> $request->productName,
            'desc'=> $request->description,
            'category'=> $request->category,
            'price'=> $request->price,
            'stock'=> $request->stock,
            'image_url' => $imageUrl
        ];

        // $postRef = $this->database->getReference('products')->push($postData);

        try {
            $this->database->getReference('products')->push($postData);
            return redirect()->route('products.index')->with('success', 'yey');
        } catch (\Exception $e) {
            return redirect()->route('products.index')->with('error', 'Error Ngab: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = $this->database->getReference('category')->getValue();
        return view('products.editProduct', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $productRef = $this->database->getReference('products')->getChild($id);

        if (!$productRef->getValue()) {
            return redirect()->route('products.index')->with('error', 'Product not found');
        }

        $imageUrl = $productRef->getChild('image_url')->getValue();
        $productRef->remove();

        if ($imageUrl) {
            $firebaseStorage =  $this->firebaseStorage->getBucket();

            $pathInfo = pathinfo(parse_url($imageUrl, PHP_URL_PATH));
            $filePath = 'images/' . $pathInfo['basename'];

            $firebaseStorage->object($filePath)->delete();
        }

        return redirect()->route('products.index')->with('success', 'Product deleted successfully');
    }
}
