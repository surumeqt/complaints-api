<?php

namespace Services;

use Models\ComplaintModel;

class ComplaintService
{
    private $complaintModel;
    public function __construct()
    {
        $this->complaintModel = new ComplaintModel();
    }
    public function addComplaint(int $id, array $data)
    {
        // add complaint probably . . .
        $categoryId = $this->complaintModel->insertToCategory($data['category']);
        $encryptDesc = encrypt_sensitive_fields($data, ['description']);
        unset($data['category']);
        $this->complaintModel->createComplaint($id, $categoryId, $encryptDesc);
    }
    public function getComplaints(int $id)
    {
        // get the complaint via complaint id
        return $this->complaintModel->getComplaintByComplaintId($id);
    }
    public function getComplaintsByUser(int $id)
    {
        // get the complaints of user via user id
        return $this->complaintModel->getComplaintsByUserUID($id);
    }
    public function updateComplaintStatus(int $id, int $adminId, array $data)
    {
        // update the status based on complaint id given
        $this->complaintModel->updateComplaintStatusByAdminRole($id, $adminId, $data);
    }
    public function sendResponseToComplaint(int $complaintId, int $adminId, array $data)
    {
        // send a response to complaint via complaint id
        $ecnryptedResponseMessage = encrypt_sensitive_fields($data, ['response']);
        $this->complaintModel->sendAdminResponseToComplaint($complaintId, $adminId, $ecnryptedResponseMessage);
    }
    public function getAllComplaintsAvailable()
    {
        // should return an array of arrays
        return $this->complaintModel->getAllComplaints();
    }
    public function getComplaintsCategory()
    {
        // should return the categories name ?
        return $this->complaintModel->getAllCategoriesName();
    }
    public function getResolutionStatus()
    {
        // should return the resolution history  for each complaint?
        return $this->complaintModel->getStatusForEachComplaint();
    }
}