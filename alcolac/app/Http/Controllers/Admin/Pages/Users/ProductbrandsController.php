<?php

namespace App\Http\Controllers\Backend\Productbrand;

use App\Models\Productbrand\Productbrand;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\RedirectResponse;
use App\Http\Responses\ViewResponse;
use App\Http\Responses\Backend\Productbrand\CreateResponse;
use App\Http\Responses\Backend\Productbrand\EditResponse;
use App\Repositories\Backend\Productbrand\ProductbrandRepository;

use App\Http\Requests\Backend\Productbrand\ManageProductbrandRequest;
use App\Http\Requests\Backend\Productbrand\CreateProductbrandRequest;
use App\Http\Requests\Backend\Productbrand\StoreProductbrandRequest;
use App\Http\Requests\Backend\Productbrand\EditProductbrandRequest;
use App\Http\Requests\Backend\Productbrand\UpdateProductbrandRequest;
use App\Http\Requests\Backend\Productbrand\DeleteProductbrandRequest;

/**
 * ProductbrandsController
 */
class ProductbrandsController extends Controller
{
    /**
     * variable to store the repository object
     * @var ProductbrandRepository
     */
    protected $repository;

    /**
     * contructor to initialize repository object
     * @param ProductbrandRepository $repository;
     */
    public function __construct(ProductbrandRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  App\Http\Requests\Backend\productbrand\ManageProductbrandRequest  $request
     * @return \App\Http\Responses\ViewResponse
     */
    public function index(ManageProductbrandRequest $request)
    {
        return new ViewResponse('backend.productbrands.index');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @param  CreateProductbrandRequestNamespace  $request
     * @return \App\Http\Responses\Backend\productbrand\CreateResponse
     */
    public function create(CreateProductbrandRequest $request)
    {
        return new CreateResponse('backend.productbrands.create');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param StoreProductbrandRequestNamespace  $request
     * @return \App\Http\Responses\RedirectResponse
     */
    public function store(StoreProductbrandRequest $request)
    {
        //Input received from the request
        $input = $request->except(['_token']);
        //Create the model using repository create method
        $this->repository->create($input);
        //return with successfull message
        return new RedirectResponse(route('admin.productbrands.index'), ['flash_success' => trans('alerts.backend.productbrands.created')]);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  App\Models\productbrand\productbrand  $productbrand
     * @param  EditProductbrandRequestNamespace  $request
     * @return \App\Http\Responses\Backend\productbrand\EditResponse
     */
    public function edit(Productbrand $productbrand, EditProductbrandRequest $request)
    {

        return new EditResponse($productbrand);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateProductbrandRequestNamespace  $request
     * @param  App\Models\productbrand\productbrand  $productbrand
     * @return \App\Http\Responses\RedirectResponse
     */
    public function update(UpdateProductbrandRequest $request, Productbrand $productbrand)
    {
        //Input received from the request
        $input = $request->except(['_token']);
        //Update the model using repository update method
        $this->repository->update( $productbrand, $input );
        //return with successfull message
        return new RedirectResponse(route('admin.productbrands.index'), ['flash_success' => trans('alerts.backend.productbrands.updated')]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  DeleteProductbrandRequestNamespace  $request
     * @param  App\Models\productbrand\productbrand  $productbrand
     * @return \App\Http\Responses\RedirectResponse
     */
    public function destroy(Productbrand $productbrand, DeleteProductbrandRequest $request)
    {
        //Calling the delete method on repository
        $this->repository->delete($productbrand);
        //returning with successfull message
        return new RedirectResponse(route('admin.productbrands.index'), ['flash_success' => trans('alerts.backend.productbrands.deleted')]);
    }

}
