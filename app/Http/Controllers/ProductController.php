<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Firestore;
use Kreait\Firebase\Contract\Storage;
// use Google\Cloud\Firestore\FirestoreClient;

class ProductController extends Controller
{
    protected $firestore;
    protected $firebaseStorage;

    public function __construct(Firestore $firestore, Storage $firebaseStorage)
    {
        $this->firestore = $firestore;
        $this->firebaseStorage = $firebaseStorage;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = [];
        $productRef = app('firebase.firestore')->database()->collection('products')->documents();

        foreach ($productRef as $product) {
            $productData = $product->data();
            $productData['id'] = $product->id();
            $products[] = $productData;
        }

        // var_dump($productRef);

        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('products.createProduct');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            if ($request->hasFile('image')) {
                $file = $request->file('image');

                $bucket = $this->firebaseStorage->getBucket();
                $object = $bucket->upload(file_get_contents($file->getPathname()), [
                    'name' => 'products/' . $file->getClientOriginalName(),
                ]);

                $imageUrl = $object->signedUrl(now()->addYears(24));
            }

            $newproducts = [
                'category' => $request->category,
                'desc' => $request->description,
                'name' => $request->productName,
                'price' => $request->price,
                'stock' => $request->stock,
                'image' => $imageUrl,
            ];

            // var_dump($imageUrl);


            app('firebase.firestore')->database()->collection('products')->add($newproducts);
            return redirect()->route('products.index');
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
        $productRef = app('firebase.firestore')->database()->collection('products');
        $document = $productRef->document($id);
        $productId = $id;
        $productData = $document->snapshot()->data();

        return view('products.editProduct', compact('productData','productId'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Ambil referensi dokumen produk
            $productRef = app('firebase.firestore')->database()->collection('products')->document($id);
            $productData = $productRef->snapshot()->data();

            if (!$productData) {
                return redirect()->route('products.index')->with('error', 'Product not found');
            }

            // Hapus gambar lama jika ada
            if ($request->hasFile('image')) {
                $imageUrl = $productData['image'];
                $pathInfo = pathinfo(parse_url($imageUrl, PHP_URL_PATH));
                $filePath = 'products/' . $pathInfo['basename'];

                $firebaseStorage =  $this->firebaseStorage->getBucket();
                $firebaseStorage->object($filePath)->delete();

                // Unggah gambar baru
                $file = $request->file('image');
                $bucket = $this->firebaseStorage->getBucket();
                $object = $bucket->upload(file_get_contents($file->getPathname()), [
                    'name' => 'products/' . $file->getClientOriginalName(),
                ]);

                $imageUrl = $object->signedUrl(now()->addYears(24));
            }

            // Perbarui data produk
            $updatedProductData = [
                'category' => $request->category,
                'desc' => $request->description,
                'name' => $request->productName,
                'price' => $request->price,
                'stock' => $request->stock,
                'image' => $imageUrl ?? $productData['image'], // Gunakan URL gambar baru jika ada, jika tidak gunakan yang lama
            ];

            $productRef->set($updatedProductData, ['merge' => true]);

            return redirect()->route('products.index')->with('success', 'Product updated successfully');
        } catch (\Exception $e) {
            return redirect()->route('products.index')->with('error', 'Error updating product: ' . $e->getMessage());
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $productRef = app('firebase.firestore')->database()->collection('products');

        $document = $productRef->document($id);
        $productData = $document->snapshot()->data();

        if (!$productData) {
            return redirect()->route('products.index')->with('error', 'Product not found');
        }

        // Delete the product document
        $document->delete();

        // Check if the product has an image
        if ($productData['image']) {
            try {
                $imageUrl = $productData['image'];

                $pathInfo = pathinfo(parse_url($imageUrl, PHP_URL_PATH));
                $filePath = 'products/' . $pathInfo['basename'];

                $firebaseStorage =  $this->firebaseStorage->getBucket();
                $firebaseStorage->object($filePath)->delete();
            } catch (\Exception $e) {
                // Handle any errors
                return redirect()->route('products.index')->with('error', 'Error deleting image: ' . $e->getMessage());
            }
        }

        return redirect()->route('products.index')->with('success', 'Product deleted successfully');
    }
}
