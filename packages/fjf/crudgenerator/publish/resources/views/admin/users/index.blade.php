@extends('layouts.index')

@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Users
        <small></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Users</a></li>
        <li class="active">Index</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="col-xs-6">
                        <a href="{{ url('/admin/users/create') }}" class="btn btn-success btn-sm" title="Add New User">
                          <i class="fa fa-plus" aria-hidden="true"></i> Add New
                        </a>
                    </div>

                    <div class="col-xs-6" align="right">
                        {!! Form::open(['method' => 'GET', 'url' => '/admin/users', 'class' => 'form-inline my-2 my-lg-0 float-right', 'role' => 'search'])  !!}
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Search...">
                              <span class="input-group-btn">
                                    <button class="btn btn-flat" type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>
                              </span>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
                <br/>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th><center>ID</center></th>
                                <th><center>Name</center></th>
                                <th><center>Email</center></th>
                                <th><center>Actions</center></th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $item)
                            <tr>
                                <td><center>{{ $item->id }}</center></td>
                                <td><a href="{{ url('/admin/users', $item->id) }}"><center>{{ $item->name }}</center></a></td>
                                <td><center>{{ $item->email }}</center></td>
                                <td><center>
                                    <a href="{{ url('/admin/users/' . $item->id) }}" title="View User"><button class="btn btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i> View</button></a>
                                    <a href="{{ url('/admin/users/' . $item->id . '/edit') }}" title="Edit User"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>
                                    {!! Form::open([
                                        'method' => 'DELETE',
                                        'url' => ['/admin/users', $item->id],
                                        'style' => 'display:inline'
                                    ]) !!}
                                        {!! Form::button('<i class="fa fa-trash-o" aria-hidden="true"></i> Delete', array(
                                                'type' => 'submit',
                                                'class' => 'btn btn-danger btn-sm',
                                                'title' => 'Delete User',
                                                'onclick'=>'return confirm("Confirm delete?")'
                                        )) !!}
                                    {!! Form::close() !!} </center>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="pagination"> {!! $users->appends(['search' => Request::get('search')])->render() !!} </div>
                </div>
            </div>
        </div>
    </div>
</section>
</div>

@endsection
