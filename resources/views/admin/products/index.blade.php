@extends('layouts.admin')

@section('header_section')

@endsection

@section('content')

    <div class="row">
        <div class="col col-sm-12">
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th scope="row">ID</th>
                    <th scope="row">Image</th>
                    <th scope="row">Name</th>
                    <th scope="row">Quantity</th>
                    <th scope="row">Discount</th>
                    <th scope="row">Price</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($products as $item)
                    <tr @if(!$item->quantity) class="table-danger" @endif>
                        <td scope="row">{{$item->id}}</td>
                        <td><img src="{{\App\Helpers\Image::get($item->image,128)}}"></td>
                        <td>{{$item->name}} <br>{{$item->model}}</td>
                        <td>{{$item->quantity}}</td>
                        <td>{{$item->discount?$item->discount.'%':''}}</td>
                        <td class="text-end">{{number_format($item->price,2)}}</td>
                        <td>
                            <div class="row">
                                <div class="col col-sm-4">
                                    <a class="btn btn-sm btn-success"
                                       data-bs-toggle="tooltip" data-bs-placement="top"
                                       data-bs-title="Open preview link."
                                       target="_blank" href="{{url('/product/'. $item->slug)}}"><i
                                            class="fa-solid fa-magnifying-glass"></i></a>

                                </div>
                                <div class="col col-sm-4">
                                    <a class="btn btn-sm btn-primary"
                                       data-bs-toggle="tooltip" data-bs-placement="top"
                                       data-bs-title="Edit item..."
                                       href="#"><i class="fa-solid fa-pen-to-square"></i></a>

                                </div>
                                <div class="col col-sm-4">

                                    <form class="delete{{$item->id}}"
                                        action='{{url('/admin/product/' . $item->id)}}' method='post'>
                                        @csrf
                                        @method('DELETE')

                                        <button type="button" value='Pay' class="btn btn-sm btn-danger"
                                                data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                data-bs-text="{{$item->name}}"
                                                data-bs-form="delete{{$item->id}}"
                                        >
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>

                                    </form>
                                </div>
                            </div>


                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $( document ).ready(function() {
            console.log( "ready!" );

        var exampleModal = document.getElementById('deleteModal')
        exampleModal.addEventListener('show.bs.modal', function (event) {
            // Button that triggered the modal
            var button = event.relatedTarget;
            // Extract info from data-bs-* attributes
            var text = button.getAttribute('data-bs-text');
            var form = button.getAttribute('data-bs-form');

            $('#delete').attr('onclick', "$('."+form+"').submit();");

            var modalTitle = exampleModal.querySelector('.question');
            modalTitle.textContent = text;
        })
        });
    </script>
    <!-- Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"">
            <div class="modal-content">
                <div class="modal-header bg-danger text-light">
                    <h1 class="modal-title fs-5" id="deleteModalLabel">Confirm delete</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="question h5" id="question"></div>
                    Are you sure you want to delete this item?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">No, cancel</button>
                    <button type="submit" id="delete" class="btn btn-danger"  >Yes, delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection
