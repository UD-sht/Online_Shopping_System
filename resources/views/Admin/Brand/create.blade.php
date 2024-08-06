@extends('Admin.Layouts.app')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid my-2">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Create Brand</h1>
                    </div>
                    <div class="col-sm-6 text-right">
                        <a href="{{ route('admin.brand.index') }}" class="btn btn-primary">Back</a>
                    </div>
                </div>
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <form action="{{ route('admin.brand.store') }}" method="POST" id="BrandForm">
                @csrf
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name">Name</label>
                                        <input type="text" name="name" id="name" class="form-control"
                                            placeholder="Name">
                                        <p></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="slug">Slug</label>
                                        <input type="text" readonly name="slug" id="slug" class="form-control"
                                            placeholder="Slug">
                                        <p></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status">Status</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="1">Active</option>
                                            <option value="0">Block</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="pb-5 pt-3">
                        <button type="submit" class="btn btn-primary">Create</button>
                        <a href="{{ route('admin.brand.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
                    </div>
                </div>
            </form>
            <!-- /.card -->
        </section>
        <!-- /.content -->
    </div>
@endsection

@section('jsPage')
    <script>
        $('#BrandForm').submit(function(event) {
            event.preventDefault();
            var element = $(this);
            $('button[type=submit]').prop('disabled', true);
            $.ajax({
                url: '{{ route('admin.brand.store') }}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {
                    if (response.status == true) {
                        $('button[type=submit]').prop('disabled', false);
                        window.location.href = "{{ route('admin.brand.index') }}";
                        $('#name').removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html('')
                        $('#slug').removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html('')

                    } else {
                        var errors = response.errors;
                        if (errors.name) {
                            $('#name').addClass('is-invalid')
                                .siblings('p')
                                .addClass('invalid-feedback')
                                .html(errors.name)
                        }
                        else{
                            $('#name').removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html('')
                        }
                        if (errors.slug) {
                            $('#slug').addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors.slug)
                        }
                        else{
                            $('#slug').removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html('')
                        }
                    }
                },
                error: function(jqXHR, exception) {
                    console.log('Ajax error: ', jqXHR, exception);
                },
            });
        });

        $('#name').change(function(){
            var element = $(this);
            $('button[type=submit]').prop('disabled', true);
            $.ajax({
                url: '{{ route('getslug') }}',
                type: 'get',
                data: { title: element.val() },
                dataType: 'json',
                success: function(response){
                    $('button[type=submit]').prop('disabled', false);
                    if(response.status == true){
                        $('#slug').val(response.slug)
                    }
                },
            });
        });

    </script>
@endsection
