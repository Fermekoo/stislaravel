@extends('base')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Tambah Data Role</h1>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <form action="{{ route('roles.simpan') }}" method="POST">
                    @csrf
                    <div class="card">
                        <div id="collapse-collapsed" class="show" aria-labelledby="heading-collapsed">
                            <div class="card-body">
                                @include('partials._alert')
                                <div class="form-row">
                                    <div class="col-md-12 mb-3">
                                        <label style="font-weight: bold;">Role</label>
                                        <input type="text" name="role_name" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-striped table-md">
                                <thead>
                                    <tr>
                                        <th>Menu</th>
                                        <th>Read</th>
                                        <th>Create</th>
                                        <th>Update</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                @foreach($menus as $menu)
                                    <tr>
                                        
                                        <th>
                                        @if($menu->childs->isNotEmpty())
                                            <a href="#collapse{{$menu->id}}" data-toggle="collapse">+</a>
                                        @endif
                                        <input type="checkbox" value="{{ $menu->permissions[0]->name }}" name="permissions[]" data-type="parent" data-id="{{$menu->id}}">
                                            {{ $menu->name }}
                                        </th>
                                        @if($menu->permissions->count() > 1)
                                            @foreach($menu->permissions as $mp)
                                                <th> <input type="checkbox" name="permissions[]" data-menu_id="{{ $menu->id }}" name="permissions[]" value="{{ $mp->name }}"> </th>
                                            @endforeach
                                        @else

                                            <th colspan="4"></th>
                                        @endif
                                    </tr>
                                <tbody id="collapse{{$menu->id}}" class="collapse">
                                    @foreach($menu->childs as $child)
                                    <tr>
                                        <td><input type="checkbox" data-type="parent" data-parent_menu="{{$menu->id}}" data-id="{{$child->id}}"> {{ $child->name}}</td>
                                        @foreach($child->permissions as $cp)
                                            <th><input type="checkbox" name="permissions[]" data-parent_menu="{{$menu->id}}" data-menu_id="{{ $child->id }}" value="{{ $cp->name }}" id=""></th>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </tbody>
                                @endforeach
                            </table>
                        </div>
                    </div>
                    @can('role-create')
                    <button class="btn btn-success float-right mb-4 mr-2" href="#" id="simpan">
                        <i class="fa fa-save"></i> SIMPAN
                    </button>
                    @endcan
                    <a class="btn btn-warning float-right mb-4 mr-2" href="{{ route('roles') }}"><i class="fa fa-angle-double-left"></i> KEMBALI</a>
            </div>
            </form>
        </div>
    </div>
</section>
@endsection

@push('js')

<script>
    $(function() {
        $("input[type='checkbox']").change(function() {
            $("[data-parent_menu='" + $(this).data('id') + "']").prop('checked', this.checked);
            $("[data-menu_id='" + $(this).data('id') + "']").prop('checked', this.checked);
            $("[data-id='" + $(this).data('menu_id') + "']").prop('checked', this.checked);



            if (this.checked) {
                $("[data-menu='" + $(this).data('parent_menu') + "']").prop('checked', this.checked);
            }

            let count_permission = $("[data-menu_id='"+$(this).data('menu_id')+"']:checked").length;

            if(count_permission > 0) {
                $("[data-id='" + $(this).data('menu_id') + "']").prop('checked', true);
            } else {
                $("[data-id='" + $(this).data('menu_id') + "']").prop('checked', false);
            }

            let count_menu = $("[data-parent_menu='"+$(this).data('parent_menu')+"']:checked").length;

            if(count_menu > 0) {
                $("[data-id='" + $(this).data('parent_menu') + "']").prop('checked', true);
            } else {
                $("[data-id='" + $(this).data('parent_menu') + "']").prop('checked', false);
            }
            
        });
    });
</script>
@endpush