<?php

namespace Controllers;

use Core\Http\Request;
use Core\Http\Response;
use Services\ComplaintService;

class ComplaintController
{
    private ComplaintService $complaintService;
    public function __construct()
    {
        $this->complaintService = new ComplaintService();
    }
    public function createComplaint(Request $request)
    {
        $userId = $request->getAuthId();
        $data = $request->validate([
            'title' => true,
            'description' => true,
            'category' => true
        ]);
        $this->complaintService->addComplaint($userId, $data);
        Response::json(['message' => 'complaint sent!'], 201);
    }
    public function getComplaintsById(Request $request)
    {
        $complaintId = $request->param('complaint_id');
        $complaints = $this->complaintService->getComplaints($complaintId);
        Response::json($complaints);
    }
    public function getComplaintByUser(Request $request)
    {
        $userParamId = $request->param('user_id');
        $complaintsByUser = $this->complaintService->getComplaintsByUser($userParamId);
        Response::json($complaintsByUser);
    }
    public function updateComplaintStatusByAdmin(Request $request)
    {
        // update the complaint status by the admin role
        $complaintId = $request->param('complaint_id');
        $adminId = $request->getAuthId();
        $data = $request->validate([
            'status' => 'pending|in_progress|rejected|resolved'
        ]);
        $this->complaintService->updateComplaintStatus($complaintId, $adminId, $data);
        Response::json(['message' => 'status updated!']);
    }
    public function createAdminResponseToComplaint(Request $request)
    {
        // send a response to complaint by admin role
        $complaintId = $request->param('complaint_id');
        $adminId = $request->getAuthId();
        $data = $request->validate([
            'response' => true
        ]);
        $this->complaintService->sendResponseToComplaint($complaintId, $adminId, $data);
        Response::json(['message' => 'response sent!']);
    }
    public function getComplaintsByAdmin()
    {
        // response all complaints (only with admin role)
        $complaints = $this->complaintService->getAllComplaintsAvailable();
        Response::json($complaints);
    }
    public function getComplaintsCategoryByAdmin()
    {
        // response categories??
        $categories = $this->complaintService->getComplaintsCategory();
        Response::json($categories);
    }
    public function getResolutionStatusByAdmin()
    {
        // i dont know what to response here . . .
        // up: maybe the status of each complaint...
        $restat = $this->complaintService->getResolutionStatus();
        Response::json($restat);
    }
}