<?php

namespace Models;

use Exception;

class ComplaintModel extends BaseModel
{
    public function createComplaint($userId, $cateegoryId, $data)
    {
        // insert to complaint table
        $sql = "
            INSERT INTO complaints (id, user_id, category_id, title, description_ciphertext, description_iv, description_tag)
            VALUES (:id, :user_id, :category_id, :title, :description_ciphertext, :description_iv, :description_tag)
        ";
        $this->execute($sql, [
            ':id' => generateComplaintId(),
            ':user_id' => $userId,
            ':category_id' => $cateegoryId,
            ':title' => $data['title'],
            ':description_ciphertext' => $data['description']['cipher'],
            ':description_iv' => $data['description']['iv'],
            ':description_tag' => $data['description']['tag']
        ]);
    }
    public function insertToCategory($category)
    {
        $categoryId = generateCategoryId();
        $sql = "INSERT INTO complaint_categories (id, name) VALUES (:id, :name)";
        $this->execute($sql, [
            ':id' => $categoryId,
            ':name' => $category
        ]);
        return $categoryId;
    }
    public function getAllComplaints()
    {
        $sql = "SELECT * FROM complaints";
        $complaints = $this->fetchAllNoParam($sql);
        $result = [];
        foreach ($complaints as $complaint) {
            $result[] = [
                'user' => $this->fromTableGetThis('users', 'name', 'id', $complaint['user_id']),
                'title' => $complaint['title'],
                'description' => $this->decryptDescription($complaint),
                'category' => $this->getCategoryByCategoryId($complaint['category_id']),
                'status' => $complaint['status'],
                'created_at' => $complaint['created_at']
            ];
        }
        return $result;
    }
    public function getAllCategoriesName()
    {
        $sql = "SELECT id, name FROM complaint_categories";
        $categories = $this->fetchAllNoParam($sql);
        $result = [];
        foreach ($categories as $category) {
            $result[] = [
                'category_id' => $category['id'],
                'category' => $category['name']
            ];
        }
        return $result;
    }
    public function getStatusForEachComplaint()
    {
        $sql = "SELECT * FROM complaints";
        $complaints = $this->fetchAllNoParam($sql);
        $result = [];
        foreach ($complaints as $complaint)
        {
            $result[] = [
                'complaint_id' => $complaint['id'],
                'complaint_by' => $this->fromTableGetThis('users', 'name', 'id', $complaint['user_id']),
                'title' => $complaint['title'],
                'description' => $this->decryptDescription($complaint),
                'complaint_response' => $this->getResponseByComplaintId($complaint['id']),
                'response_by' => $this->fromTableGetThis('complaint_responses', 'admin_id', 'complaint_id', $complaint['id']),
                'complaint_status' => $complaint['status']
            ];
        }
        return $result;
    }
    public function getComplaintByComplaintId($id)
    {
        // function name
        $sql = "SELECT * FROM complaints WHERE id = :id";
        $complaint = $this->fetchOne($sql, [':id' => $id ]);
        if (!$complaint) return ['message' => 'complaint doesnt exist!'];
        return [
            'title' => $complaint['title'],
            'description' => $this->decryptDescription($complaint),
            'category' => $this->getCategoryByCategoryId($complaint['category_id']),
            'status' => $complaint['status']
        ];
    }
    public function getComplaintsByUserUID($id)
    {
        // get the complaints of user based on user unique id
        $sql = "SELECT * FROM complaints WHERE user_id = :user_id";
        $complaintsByUser = $this->fetchAll($sql, [':user_id' => $id]);
        if (!$complaintsByUser) return ['message' => 'user doesnt have complaints yet!'];
        $result = [];
        foreach ($complaintsByUser as $complaint) {
            $result[] = [
                'user' => $this->fromTableGetThis('users', 'name', 'id', $id),
                'title' => $complaint['title'],
                'description' => $this->decryptDescription($complaint),
                'category' => $this->getCategoryByCategoryId($complaint['category_id']),
                'status' => $complaint['status'],
                'created_at' => $complaint['created_at']
            ];
        }

        return $result;
    }
    public function getCategoryByCategoryId($id)
    {
        $sql = "SELECT name FROM complaint_categories WHERE id = :id";
        $result = $this->fetchOne($sql, [':id' => $id]);
        return $result['name'];
    }
    public function updateComplaintStatusByAdminRole($id, $adminId, $data)
    {
        // should update the status only by admin
        $this->execute("SET @changed_by = :admin", [
            ':admin' => $adminId
        ]);
        $sql = "UPDATE complaints
                SET status = :status
                WHERE id = :id";
        return $this->execute($sql, [
            ':status' => $data['status'],
            ':id' => $id
        ]);
    }
    public function sendAdminResponseToComplaint($complaintId, $adminId, $data)
    {
        // give response to complaint
        $sql = "INSERT INTO complaint_responses
                (id, complaint_id, admin_id, message_ciphertext, message_iv, message_tag)
                VALUES (:id, :complaint_id, :admin_id, :message_ciphertext, :message_iv, :message_tag)";
        return $this->execute($sql, [
            ':id' => generateResponseId(),
            ':complaint_id' => $complaintId,
            ':admin_id' => $adminId,
            ':message_ciphertext' => $data['response']['cipher'],
            ':message_iv' => $data['response']['iv'],
            ':message_tag' => $data['response']['tag'],
        ]);
    }
    public function getResponseByComplaintId($id)
    {
        $sql = "SELECT message_ciphertext, message_iv, message_tag FROM complaint_responses WHERE complaint_id = :id";
        $response = $this->fetchOne($sql, [':id' => $id]);
        return $this->decryptResponseMessage($response);
    }
}