@extends('layouts.index')

@section('content')
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Forms</a></li>
        <li class="active">General Elements</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Generator</h3>
                    </div>
                    <!-- form start -->

                        <form class="form-horizontal" method="post" action="{{ url('/admin/generator') }}">
                            {{ csrf_field() }}

                            <div class="form-group">
                                <label for="crud_name" class="col-sm-2 control-label">Crud Type:</label>
                                <div class="col-md-10">
                                    <select name="choose" class="form-control" id="choose">
                                        <option value="all">All</option>
                                        <option value="controller">Controller</option>
                                        <option value="model">Model</option>
                                        <option value="view">View</option>
                                        <option value="migration">Migration</option>
                                    </select>
                                </div>
                            </div>
                            <div class="box-body">
                        <div class="form-group a c mo mi v">
                          <label for="inputEmail3" class="col-sm-2 control-label">Crud Name</label>
                              <div class="col-sm-10">
                                <input type="text" name="crud_name" class="form-control" id="crud_name" placeholder="Posts" required="true">
                              </div>
                        </div>
                        <div class="form-group a c">
                          <label for="inputPassword3" class="col-sm-2 control-label">Controller Namespace</label>
                              <div class="col-sm-10">
                                <input type="text" name="controller_namespace" class="form-control" id="controller_namespace" placeholder="Admin">
                              </div>
                        </div>
                        <div class="form-group a c v">
                          <label for="inputPassword3" class="col-sm-2 control-label">Route Group Prefix</label>
                              <div class="col-sm-10">
                                <input type="text" name="route_group" class="form-control" id="route_group" placeholder="admin">
                              </div>
                        </div>
                        <div class="form-group a c v">
                          <label for="inputPassword3" class="col-sm-2 control-label">View Path</label>
                              <div class="col-sm-10">
                                <input type="text" name="view_path" class="form-control" id="view_path" placeholder="admin">
                              </div>
                        </div>
                        <div class="form-group a c">
                          <label for="inputPassword3" class="col-sm-2 control-label">Want to add route?</label>
                            <div class="col-sm-10">
                                <select name="route" class="form-control" id="route">
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group a mo">
                          <label for="inputPassword3" class="col-sm-2 control-label">Relationships</label>
                            <div class="col-sm-10">
                                <input type="text" name="relationships" class="form-control" id="relationships" placeholder="comments#hasMany#App\Comment">
                                <p class="help-block">method#relationType#Model</p>
                            </div>
                        </div>
                        <div class="form-group a v">
                          <label for="inputPassword3" class="col-sm-2 control-label">Form Helper</label>
                            <div class="col-sm-10">
                                <input type="text" name="form_helper" class="form-control" id="form_helper" placeholder="laravelcollective" value="laravelcollective">
                            </div>
                        </div>
                        <div class="form-group a mo mi">
                          <label for="inputPassword3" class="col-sm-2 control-label">Want to use soft deletes?</label>
                            <div class="col-sm-10">
                                <select name="soft_deletes" class="form-control" id="soft_deletes">
                                    <option value="no">No</option>
                                    <option value="yes">Yes</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group table-fields a c mo mi v">
                            <h4 class="text-center">Table Fields:</h4><br>
                            <center><div class="entry col-md-12 offset-md-12 form-inline">
                                <input class="form-control" name="fields[]" type="text" placeholder="field_name" required="true">
                                <select name="fields_type[]" class="form-control">
                                    <option value="string">string</option>
                                    <option value="char">char</option>
                                    <option value="varchar">varchar</option>
                                    <option value="password">password</option>
                                    <option value="email">email</option>
                                    <option value="date">date</option>
                                    <option value="datetime">datetime</option>
                                    <option value="time">time</option>
                                    <option value="timestamp">timestamp</option>
                                    <option value="text">text</option>
                                    <option value="mediumtext">mediumtext</option>
                                    <option value="longtext">longtext</option>
                                    <option value="json">json</option>
                                    <option value="jsonb">jsonb</option>
                                    <option value="binary">binary</option>
                                    <option value="number">number</option>
                                    <option value="integer">integer</option>
                                    <option value="bigint">bigint</option>
                                    <option value="mediumint">mediumint</option>
                                    <option value="tinyint">tinyint</option>
                                    <option value="smallint">smallint</option>
                                    <option value="boolean">boolean</option>
                                    <option value="decimal">decimal</option>
                                    <option value="double">double</option>
                                    <option value="float">float</option>
                                </select>

                                <label>Required</label>
                                <select name="fields_required[]" class="form-control">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>

                                <button class="btn btn-success btn-add inline btn-sm" type="button">
                                    <span class="fa fa-plus"></span>
                                </button>
                            </div>
                        </div></center>
                        <p class="help text-center">
                            It will automatically assume form fields based on the migration field type.
                        </p>
                      </div>
                      <!-- /.box-body -->
                      <div class="box-footer">
                            <div class="form-group row">
                                <div class="offset-md-12 col-md-12">
                                    <center><button type="submit" class="btn btn-primary" name="generate">Generate</button></center>
                                </div>
                            </div>
                      </div>
                      <!-- /.box-footer -->
                    </form>
                </div>
              <!-- /.box -->
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
    {!! $script_master !!}
@endsection
