<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Settings\UserService;
use App\Http\Requests\Settings\UserFormRequest;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = $this->userService->getUsers($request->all());
        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserFormRequest $request)
    {
        try {
            $user = $this->userService->createUser($request->validated());
            return response()->json(['success' => true, 'result' => $user], 201);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $user = $this->userService->findUser($id);
            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404);
            }
            return response()->json(['success' => true, 'result' => $user]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserFormRequest $request, string $id)
    {
        try {
            $user = $this->userService->updateUser($id, $request->validated());
            return response()->json(['success' => true, 'result' => $user]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->userService->deleteUser($id);
            return response()->json(['success' => true], 204);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Get user stats
     */
    public function stats()
    {
        $stats = $this->userService->getUserStats();
        return response()->json(['result' => $stats]);
    }

    /**
     * Bulk delete users
     */
    public function bulkDelete(Request $request)
    {
        try {
            $this->userService->bulkDeleteUsers($request->ids);
            return response()->json(['success' => true], 204);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}