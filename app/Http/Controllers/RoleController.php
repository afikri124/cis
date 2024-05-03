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

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct() {
        // $this->middleware('auth');
        $this->middleware('can:read role');
        // if(!Gate::allows('read role')){
        //     abort(404);
        // }
    }

    public function test()
    {
        //ini hanya untuk keperluan testing
        $user = User::find("1");
        $test = $user->syncRoles(['staff','admin']);
        var_dump($test);
    }

    public function index(Request $request)
    {
        // $this->authorize('read role');
        if ($request->isMethod('post')) { //jika menerima method post dari form
            // validasi input form
            $this->validate($request, [ 
                'name'=> ['required', 'string', 'max:255', Rule::unique('roles')],
                'guard_name'=> ['required']
            ]);
            //memasukkan data ke database
            $data = Role::create([
                'name' => $request->name,
                'guard_name' => $request->guard_name,
                'color'=> $request->color,
                'description'=> $request->description
            ]);
            //kembali ke halaman index
            return redirect()->route('roles.index')->with('msg','Role "'.$request->name.'" successfully added!');
        }
        //variabel digunakan untuk pilihan
        $permissions = Permission::get();
        $guard_names = Role::select('guard_name')->groupBy('guard_name')->get();
        return view('configuration.roles.index', compact('guard_names', 'permissions'));
    }

    public function data(Request $request)
    {
        $data = Role::
            with(['permissions' => function ($query) {
                $query->select('id');
            }])
            ->with(['users' => function ($query) {
                $query->select('id');
            }])
            ->select('*')->orderBy("name");
            return Datatables::of($data)
                ->filter(function ($instance) use ($request) {
                    //jika pengguna memfilter berdasarkan guard_name
                    if (!empty($request->get('select_guard_name'))) {
                        $instance->where('guard_name', $request->get('select_guard_name'));
                    }
                    //jika pengguna memfilter berdasarkan permission
                    if (!empty($request->get('select_permission'))) {
                        $instance->whereHas('permissions', function($q) use($request){
                            $q->where('permission_id', $request->get('select_permission'));
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

    /**
     * Show the form for creating a new resource.
     */
    // public function create()
    // {
    //     //
    //     $this->authorize('read role');
    //     return 'create page';
    // }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     //membuat
    //     $this->authorize('create role');
    //     return "store";
    // }

    /**
     * Display the specified resource.
     */
    // public function show(string $id)
    // {
    //     //menampilkan
    //     $this->authorize('create role');
    //     return "show by id";
    // }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($idd, Request $request)
    {
        //menampilkan edit
        $this->authorize('update role');
        //mencoba mendeskripsikan idd menjadi id
        try {
            $id = Crypt::decrypt($idd);
        } catch (DecryptException $e) {
            return redirect()->route('roles.index');
        }
        //jika menerima method post dari form
        if ($request->isMethod('post')) {
            $this->validate($request, [ 
                'name' => ['required', 'string'],
                'guard_name' => ['required', 'string'],
            ]);
            //mengubah data di database
            Role::where('id', $id)->update([
                'name'=> $request->name,
                'guard_name'=> $request->guard_name,
                'color'=> $request->color,
                'description'=> $request->description,
                'updated_at' => Carbon::now()
            ]);
            //mencatat perubahan di log
            Log::info(Auth::user()->name." update Role #".$id.", ".$request->name);
            //kembali ke halaman edit dengan pesan notifikasi
            return redirect()->route('roles.edit', ['id'=>$idd])->with('msg','Role '.$request->name.' updated successfully!');
        }
        //mencari data user berdasarkan id
        $data = Role::find($id);
        //variabel digunakan untuk pilihan guard
        $guard_names = Role::select('guard_name')->groupBy('guard_name')->get();
        return view('configuration.roles.edit', compact('data','guard_names'));
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, string $id)
    // {
    //     //submit edit
    //     $this->authorize('update role');
    //     return "update by id";
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request) //hapus by id
    {
        $this->authorize('delete role');
        $data = Role::find($request->id);
        //jika data ditemukan
        if($data){
            //mencatat proses penghapusan di log
            Log::warning(Auth::user()->username." deleted Role #".$data->id.", name : ".$data->name);
            //data user dihapus dari database
            $data->delete();
            //jika sukses mengembalikan data dalam bentuk json
            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully!'
            ]);
        } else {
            //jika gagal mengembalikan data dalam bentuk json
            return response()->json([
                'success' => false,
                'message' => 'Role failed to delete!'
            ]);
        }
    }
}
