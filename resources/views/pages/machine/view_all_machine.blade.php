@extends('layouts.master')
@section('title')
@lang('translation.Products')
@endsection
@section('css')
<link href="{{ URL::asset('/assets/libs/ion-rangeslider/ion-rangeslider.min.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
@endsection

@section('content')
@component('common-components.breadcrumb')
@slot('pagetitle') Ecommerce @endslot
@slot('title') Machines Map @endslot
@endcomponent

<!-- Map Section -->
<div id="map" style="margin:auto; height: 300px; margin-bottom: 20px;"></div>

<div class="row">
    <div class="col-xl-11 col-lg-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <div>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>All Machines</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="form-inline float-md-end">
                                <div class="search-box ms-2">
                                    <div class="position-relative">
                                        <input type="text" class="form-control bg-light border-light rounded" placeholder="Search...">
                                        <i class="mdi mdi-magnify search-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>


                    <div class="row">
                        <div class="col-xl-3 col-sm-6">

                            <div class="product-box">

                                <div class="product-img pt-4 px-4">
                                    
                                    <img src="{{ URL::asset('assets/images/saat_pool.png') }}" alt="" class="img-fluid mx-auto d-block">
                                </div>
                                <div class="text-center product-content p-4">
                                    <h5 class="mb-1"><a href="/view_machine_detail" class="text-reset">Toul Kork Machine</a></h5>
                                    <p class="text-muted font-size-13">Gray, Shoes</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6">
                            <div class="product-box">
                                <div class="product-img pt-4 px-4">
                                    <div class="product-wishlist">
                                        <a href="#">
                                            <i class="mdi mdi-heart-outline"></i>
                                        </a>
                                    </div>
                                    <img src="{{URL::asset('assets/images/saat_pool.png')}}" alt="" class="img-fluid mx-auto d-block">
                                </div>



                                <div class="text-center product-content p-4">

                                    <h5 class="mb-1"><a href="#" class="text-reset">Nike N012 Shoes</a></h5>
                                    <p class="text-muted font-size-13">Gray, Shoes</p>

                                    <h5 class="mt-3 mb-0"><span class="text-muted me-2"><del>$280</del></span> $260</h5>

                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6">
                            <div class="product-box">
                                <div class="product-img pt-4 px-4">
                                    <div class="product-wishlist">
                                        <a href="#">
                                            <i class="mdi mdi-heart-outline"></i>
                                        </a>
                                    </div>
                                    <img src="{{URL::asset('assets/images/saat_pool.png')}}" alt="" class="img-fluid mx-auto d-block">
                                </div>



                                <div class="text-center product-content p-4">

                                    <h5 class="mb-1"><a href="#" class="text-reset">Nike N012 Shoes</a></h5>
                                    <p class="text-muted font-size-13">Gray, Shoes</p>

                                    <h5 class="mt-3 mb-0"><span class="text-muted me-2"><del>$280</del></span> $260</h5>

                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6">
                            <div class="product-box">
                                <div class="product-img pt-4 px-4">
                                    <div class="product-wishlist">
                                        <a href="#">
                                            <i class="mdi mdi-heart-outline"></i>
                                        </a>
                                    </div>
                                    <img src="{{URL::asset('assets/images/saat_pool.png')}}" alt="" class="img-fluid mx-auto d-block">
                                </div>



                                <div class="text-center product-content p-4">

                                    <h5 class="mb-1"><a href="#" class="text-reset">Nike N012 Shoes</a></h5>
                                    <p class="text-muted font-size-13">Gray, Shoes</p>

                                    <h5 class="mt-3 mb-0"><span class="text-muted me-2"><del>$280</del></span> $260</h5>

                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6">
                            <div class="product-box">
                                <div class="product-img pt-4 px-4">
                                    <div class="product-wishlist">
                                        <a href="#">
                                            <i class="mdi mdi-heart-outline"></i>
                                        </a>
                                    </div>
                                    <img src="{{URL::asset('assets/images/saat_pool.png')}}" alt="" class="img-fluid mx-auto d-block">
                                </div>



                                <div class="text-center product-content p-4">

                                    <h5 class="mb-1"><a href="#" class="text-reset">Nike N012 Shoes</a></h5>
                                    <p class="text-muted font-size-13">Gray, Shoes</p>

                                    <h5 class="mt-3 mb-0"><span class="text-muted me-2"><del>$280</del></span> $260</h5>

                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6">
                            <div class="product-box">
                                <div class="product-img pt-4 px-4">
                                    <div class="product-wishlist">
                                        <a href="#">
                                            <i class="mdi mdi-heart-outline"></i>
                                        </a>
                                    </div>
                                    <img src="{{URL::asset('assets/images/saat_pool.png')}}" alt="" class="img-fluid mx-auto d-block">
                                </div>



                                <div class="text-center product-content p-4">

                                    <h5 class="mb-1"><a href="#" class="text-reset">Nike N012 Shoes</a></h5>
                                    <p class="text-muted font-size-13">Gray, Shoes</p>

                                    <h5 class="mt-3 mb-0"><span class="text-muted me-2"><del>$280</del></span> $260</h5>

                                </div>
                            </div>
                        </div>


                    </div>
                    <!-- end row -->

                    <div class="row mt-4">
                        <div class="col-sm-6">
                            <div>
                                <p class="mb-sm-0">Page 2 of 84</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="float-sm-end">
                                <ul class="pagination pagination-rounded mb-sm-0">
                                    <li class="page-item disabled">
                                        <a href="#" class="page-link"><i class="mdi mdi-chevron-left"></i></a>
                                    </li>
                                    <li class="page-item">
                                        <a href="#" class="page-link">1</a>
                                    </li>
                                    <li class="page-item active">
                                        <a href="#" class="page-link">2</a>
                                    </li>
                                    <li class="page-item">
                                        <a href="#" class="page-link">3</a>
                                    </li>
                                    <li class="page-item">
                                        <a href="#" class="page-link">4</a>
                                    </li>
                                    <li class="page-item">
                                        <a href="#" class="page-link">5</a>
                                    </li>
                                    <li class="page-item">
                                        <a href="#" class="page-link"><i class="mdi mdi-chevron-right"></i></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- end row -->

@endsection
@section('script')
<script src="{{ URL::asset('/assets/libs/ion-rangeslider/ion-rangeslider.min.js') }}"></script>
<script src="{{ URL::asset('/assets/js/pages/product-filter-range.init.js') }}"></script>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
    // Initialize the map
    var map = L.map('map').setView([11.6, 104.9], 13);

    // Add a tile layer to the map
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Add a marker to the map
    L.marker([11.6, 104.9]).addTo(map)
        .bindPopup('A pretty CSS3 popup.<br> Easily customizable.')
        .openPopup();
</script>
@endsection