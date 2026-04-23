<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Application\RegisterUser\RegisterUser;
use App\Domain\User\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Kreait\Firebase\Contract\Database;
use Carbon\Carbon;

class ManageAccountController extends Controller
{
    public function __construct(
        private RegisterUser $registerUser,
        private UserRepository $userRepository,
        private Database $db
    ) {}

    public function index(Request $request): View
    {
        $schoolYearFilter = $request->get('school_year');
        $counts = $this->registerUser->countUsersSummary();
        $data   = $this->registerUser->getAllUsers(7, $schoolYearFilter);
        return view('manage-accounts', compact('data', 'schoolYearFilter', 'counts'));
    }

    public function updateUser(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id'    => 'required|string',
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|max:191',
            'role'       => 'required|in:student,comelec,admin,sao',
            'password'   => 'nullable|min:6',
        ]);

        $uid     = $request->input('user_id');
        $payload = [
            'first_name'  => $request->input('first_name'),
            'middle_name' => $request->input('middle_name', ''),
            'last_name'   => $request->input('last_name'),
            'email'       => $request->input('email'),
            'role'        => $request->input('role'),
            'updated_at'  => Carbon::now()->toDateTimeLocalString(),
        ];

        if ($request->filled('password')) {
            $payload['password'] = Hash::make($request->input('password'));
        }

        $this->db->getReference('users/' . $uid)->update($payload);
        cache()->forget('all_users_raw');

        return redirect()->route('view.manage-accounts')
            ->with('success', 'Account updated successfully.');
    }

    public function deleteUser(string $id): RedirectResponse
    {
        $this->userRepository->deleteUser($id);
        cache()->forget('all_users_raw');

        return redirect()->route('view.manage-accounts')
            ->with('success', 'Account deleted successfully.');
    }

    public function newUser(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name'  => 'required|string|max:100',
            'middle_name' => 'required|string|max:100',
            'last_name'   => 'required|string|max:100',
            'email'       => 'required|email|max:191',
            'password'    => 'required|min:6',
            'role'        => 'required|in:student,comelec,admin,sao',
        ], [
            'first_name.required'  => 'First name is required.',
            'middle_name.required' => 'Middle name is required.',
            'last_name.required'   => 'Last name is required.',
            'email.required'       => 'Email address is required.',
            'email.email'          => 'That email address doesn\'t look valid.',
            'password.required'    => 'Password is required.',
            'password.min'         => 'Password must be at least 6 characters.',
            'role.required'        => 'Please provide a role.',
            'role.in'              => 'Invalid role selected.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('view.manage-accounts')
                ->withErrors($validator)->withInput()->with('show_add_modal', true);
        }

        try {
            $this->registerUser->newUser($validator->validated());
            return redirect()->route('view.manage-accounts')
                ->with('success', 'Account created successfully.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('view.manage-accounts')
                ->withErrors(['email' => $e->getMessage()])->withInput()->with('show_add_modal', true);
        } catch (\Exception $e) {
            return redirect()->route('view.manage-accounts')
                ->withErrors(['general' => 'Something went wrong.'])->withInput()->with('show_add_modal', true);
        }
    }

    public function newUserAPI(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name'  => 'required|string|max:100',
            'middle_name' => 'required|string|max:100',
            'last_name'   => 'required|string|max:100',
            'email'       => 'required|email|max:191',
            'password'    => 'required|min:6',
            'role'        => 'required|in:student,comelec,admin,sao',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $user = $this->registerUser->newUser($validator->validated());
            return response()->json(['success' => true, 'message' => 'User created.', 'data' => $user->toArray()], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'errors' => ['email' => [$e->getMessage()]]], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong.'], 500);
        }
    }
}
