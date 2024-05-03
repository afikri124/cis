<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Encryption\DecryptException;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Permission\Contracts\Role as ContractsRole;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Contracts\Permission as ContractsPermission;
use Spatie\Permission\Models\Permission;
use Carbon\Carbon;
use App\Models\User;
use DB;

class PermissionController extends Controller
{
    //
    public function __construct() {
        // $this->middleware('can:read permission');
    }

    public function index(Request $request)
    {
        //variabel digunakan untuk pilihan 
        $roles = Role::get();
        $guard_names = Permission::select('guard_name')->groupBy('guard_name')->get();
        return view('configuration.permissions.index', compact('roles','guard_names'));
    }

    public function data(Request $request)
    {
        $data = Permission::
            with(['roles' => function ($query) {
                $query->select('id');
            }])
            ->select('*')->orderBy("name");
            return Datatables::of($data)
                ->filter(function ($instance) use ($request) {
                    //jika pengguna memfilter berdasarkan guard_name
                    if (!empty($request->get('select_guard_name'))) {
                        $instance->where('guard_name', $request->get('select_guard_name'));
                    }
                    //jika pengguna memfilter berdasarkan role
                    if (!empty($request->get('select_role'))) {
                        $instance->whereHas('roles', function($q) use($request){
                            $q->where('role_id', $request->get('select_role'));
                        });
                    }
                    //jika pengguna memfilter menggunakan pencarian
                    if (!empty($request->get('search'))) {
                        $search = $request->get('search');
                        $instance->where('name', 'LIKE', "%$search%");
                    }
                })
                ->addColumn('idd', function($x){
                    //menambahkan kolom idd (id yg ter-enkripsi)
                    return Crypt::encrypt($x['id']);
                })
                ->rawColumns(['idd'])
                ->make(true);
    }
}
