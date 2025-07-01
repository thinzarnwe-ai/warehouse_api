<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Branch;
use App\Models\Position;
use App\Models\Positions;
use App\Models\BranchUser;
use App\Models\Department;
use App\Models\UserBranch;
use App\Models\Departments;
use Illuminate\Support\Arr;
use App\Models\MainDocument;
use App\Models\UserCategory;
use Illuminate\Http\Request;
use App\Models\ChangeBranchLog;
use App\Models\ProductCategory;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use App\Interfaces\UserRepositoryInterface;

class UserController extends Controller
{
    private UserRepositoryInterface $repository;


    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index','store']]);
        $this->middleware('permission:user-create', ['only' => ['create','store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }
    public function login()
    {
        return view('layouts.auth.login');
    }

    public function checkLogin(Request $request)
    {
        // dd($request->all());
        $data = $request->validate([
            'employee_id'=>'required',
            'password' => 'required',
        ]);

        $this->repository->saveLog($data);
        $user = User::where(["emp_id" => $data['employee_id']])->first();
        if ($user && Hash::check($data['password'], $user->password))
        {
            Auth::login($user);
            return redirect()->route('branches.index');
        }
        else{
            return redirect()->route('admins.login')->with('fails','Wrong Password');
        }

    }

    public function logout()
{
        Auth::logout();

        return redirect('login');
    }

    // public function index(Request $request)
    // {
    //     getRequiredData();
    //     $role                   = Session::get('role');
    //     $emp_id                 = Session::get('emp_id');
    //     $branch_id              = Session::get('branch_id');
    //     $category_id            = Session::get('category_id');


    //     $users                  = $this->search_query($role,$emp_id,$branch_id,$category_id);
    //     // $users                  = User::with('departments','positions','roles','user_branches')->latest()->paginate(10);
    //     $departments            = Department::all();
    //     $categories             = ProductCategory::all();
    //     $positions              = Position::all();
    //     $branches               = Branch::all();
    //     $roles                  = Role::all();

    //     if($request->ajax()){

    //         return response()->json([
    //             'success'               => true,
    //             'users'                 => $users,
    //             'departments'           => $departments,
    //             'categories'            => $categories,
    //             'positions'             => $positions,
    //             'branches'              => $branches,
    //             'roles'                 => $roles,
    //            ]);
    //     }
    //     return view('admins.users.index',compact('users','departments','categories','positions','branches','roles'));
    // }

    public function index()
    {
      

        $users = User::with('branches')->get();
        $branches = Branch::all();
        $roles = Role::all();

        return view('admins.users.index', compact('users', 'branches', 'roles'));
    }


    public function create()
    {
        getRequiredData();
      
        $roles                  = Role::all();
        return view('admins.users.create',compact('roles'));
    }


    public function store(UserRequest $request)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
                $input = $request->all();
                // dd($input);
                $input['password'] = Hash::make($input['password']);
                unset($input['branch_id']);
                $user = User::create($input);
                $user_id = $user->id;
                $branch_ids = $request->branch_id;
                // dd($branch_ids);
                foreach ($branch_ids as $branch_id) {
                    $userBranch['user_id'] = $user_id;
                    $userBranch['branch_id'] = $branch_id;
                    UserBranch::create($userBranch);
                }
            $role = Role::find($request->input('role_id'));
            // dd($role);
            if (!$role) {
                DB::rollBack();
                return response()->json(['error' => 'Role not found.'], 404);
            }

            $user->assignRole($role->id);
            // dd($user);
            DB::commit(); 
            return response()->json(['success' => 'User created and role assigned successfully.']);
        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            DB::rollBack();
            return response()->json(['error' => 'Something went wrong'], 500);
        }

    }


    public function show($id)
    {
        $user = User::find($id);
        $branches = UserBranch::where('user_id', $user->id)->with('branch')->get();
        // dd($branches);
        return view('admins.users.show',compact('user','branches'));
    }


    public function edit($id)
    {
        $user = User::with('branches')->find($id);
        $role = $user->roles->first(); 
        return response()->json([
            'user' => $user,
            'role_id' => $role ? $role->id : null,
            'branch_ids' => $user->branches->pluck('id'),
        ]);
    }


    public function update(UserRequest $request, $id)
    {
        try {
            $input = $request->all();
            // dd($input);
            if (!empty($input['password'])) {
                $input['password'] = Hash::make($input['password']);
            } else {
                $input = Arr::except($input, array('password'));
            }
            unset($input['branch_id']);
            $user = User::find($id);
            $user->update($input);
            DB::table('model_has_roles')->where('model_id', $id)->delete();
            $user_id = $user->id;
            DB::table('user_branches')->where('user_id', $user_id)->delete();
            $branch_ids = $request->branch_id;
            $user->assignRole($request->input('roles'));
            foreach ($branch_ids as $branch_id) {
                $userBranch['user_id'] = $user_id;
                $userBranch['branch_id'] = $branch_id;
                UserBranch::create($userBranch);
            }
            return response()->json(['success' => 'User updated successfully.']);
        } catch (Exception $e) {
            Log::debug($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $item = User::findorFail($id);
            $item->delete();
            return response()->json(['success' => 'User deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }


    public function user_delete($id)
    {
      
        UserBranch::where('user_id',$id)->delete();
        User::find($id)->delete();
        return response()->json([
            'success' => true,
            'message' => 'Success Deleted user',
            ]);
    }

    // public function get_position($dept_id)
    // {
    //     $positions = Position::where('department_id',$dept_id)->get();
    //     // dd($positions);
    //     return response()->json($positions, 200);
    // }

    public function get_branch_name(Request $request)
    {
       $branch_ids = collect($request->user_branches)->pluck('branch_id');

        return response()->json($branch_ids, 200);

    }

    public function user_search(Request $request)
    {

        getRequiredData();
        $branches               = Branch::all();
        $roles                  = Role::all();

        $role                   = $request->role;
        $emp_id                 = $request->emp_id;
        $branch_id              = $request->branch_id;
        $category_id            = $request->category_id;

        $users                  = $this->search_query($role,$emp_id,$branch_id,$category_id);

        Session::put(['branch_id'=>$branch_id,'role'=>$role,'emp_id'=>$emp_id,'category_id'=>$category_id]);

        $users->appends($request->all());

        return view('admins.users.index',compact('users','branches','roles'));
    }

    public function search_query($role,$emp_id,$branch_id,$category_id)
    {
        $result                  = User::query();

        if (!empty($role)) {
            $result = User::whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role);
            });

        }
        if (!empty($emp_id)) {
            $result         = $result->where('emp_id',$emp_id);

        }
        if (!empty($branch_id)) {
            $user_branch_ids  = UserBranch::where(['branch_id'=>$branch_id])->pluck('user_id');
            $result         = $result->whereIn('id',$user_branch_ids);

        }
     

        return $result->orderBy('id','desc')->paginate(10);
    }

    public function change_branch()
    {
        $branches = Branch::all();
        return view('admins.users.change_branch',compact('branches'));
    }

    public function update_branch(Request $request)
    {
        // dd();
        $branch_ids             = $request->branch_id;
        $input = $this->validate($request, [
            'emp_id'            => 'required',
        ]);
        $user = User::where('emp_id',$request->emp_id)->first();
        $old_branches = $user->user_branches()->pluck('branch_id')->toArray();
        $user->user_branches()->delete();
        foreach($branch_ids as $branch_id)
        {
            $user_branch                = new UserBranch();
            $user_branch->branch_id     = $branch_id;
            $user_branch->user_id       = $user->id;
            $user_branch->save();
            // dd($user_branch);
        }   
        $log                = new ChangeBranchLog();
        $log->emp_id        = $request->emp_id;
        $log->old_branch_id = implode(",",$old_branches);
        $log->new_branch_id = implode(",",$branch_ids);
        $log->updated_by    = getAuthUser()->id;
        $log->ip_address    = request()->ip();
        $log->save();
        $data = $this->user_info($user->emp_id);
        return response()->json($data, 200);
    }
    public function get_user($emp_id)
    {
        $data = $this->user_info($emp_id);
        return response()->json($data, 200);
    }
    public function user_info($emp_id){
        $user           = User::where('emp_id',$emp_id)->first();
        $user_branches  = [];
        foreach($user->user_branches as $u_branch)
        {
            array_push($user_branches,$u_branch->branches);
        }
        $data           = ['user'=>$user,'user_branches'=>$user_branches];
        return $data;
    }
}
