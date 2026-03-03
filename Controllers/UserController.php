<?php

namespace Controllers;

use Core\Http\Request;
use Core\Http\Response;
use Services\UserService;

class UserController
{
    private UserService $userService;
    public function __construct()
    {
        $this->userService = new UserService();
    }
    public function getUserProfile(Request $request)
    {
        // should response the decoded user profile
        $userId = $request->getAuthId();
        $userProfile = $this->userService->decodedUserProfile($userId);
        Response::json($userProfile);
    }
    public function updateUserProfile(Request $request)
    {
        // get the id stored from token then use that to update the user profile.
        $userId = $request->getAuthId();
        $data = $request->validate([
            'name' => true,
            'phone' => true,
        ], true);
        $this->userService->updateFieldsProvided($userId, $data);
        Response::json(['message' => 'Profile Updated Successfully']);
    }
}