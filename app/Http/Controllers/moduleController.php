<?php

namespace App\Http\Controllers;

use app\custom\common_stuff;
use App\Module;
use App\Project;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;

class moduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $project_id = null )
    {
        if( $project_id ) {
            $modules = Module::where('project_id',$project_id)->get();
        }else{
            $modules = Module::all();
        }

        return view('admin.module.index',compact('project_id','modules'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create( $project_id = null )
    {
        $statuses = common_stuff::get_status_options();

        $projects = array('') + Project::lists('title','id')->toArray();
        $modules = array('') + Module::lists('title','id')->toArray();

        $assignees = User::lists('first_name','id');

        return view('admin.module.create',compact( 'statuses', 'projects','modules','assignees','project_id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $module = Module::create($request->all());
        $module->assigned_users()->sync($request->user_id);
        $module->user()->associate(get_current_user_id());
        $module->save();

        return redirect()->route('admin.modules.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $module = Module::find($id);
        return view( 'admin.module.single', compact('module'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $module = Module::find($id);

        $statuses = common_stuff::get_status_options();

        $projects = array('') + Project::lists('title','id')->toArray();

        $modules = array('') + Module::lists('title','id')->toArray();

        $module->assigned_users = json_decode($module->assigned_users);
        $module->assigned_users = array_map(function($item) {
            return $item->id;
        }, $module->assigned_users);

        $assignees = User::lists('first_name','id');
        return view('admin.module.edit', compact('module','statuses','projects','modules','assignees'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $module = Module::find($id);
        $module ->update($request->all());
        $module->assigned_users()->sync($request->user_id);
        $module->user()->associate(get_current_user_id());
        $module->save();

        return redirect()->route('admin.modules.edit',$module->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Module::destroy($id);
        return redirect()->route('admin.modules.index');
    }
}
