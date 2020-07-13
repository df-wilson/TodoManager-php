<?php

namespace App\Http\Repository;

use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TodosRepository
{
    public function all(int $userId)
    {
        logger("TodosRepository::all - Enter.", ["User ID" => $userId]);

        $todos = DB::select("SELECT * FROM todos WHERE user_id = ?", [$userId]);

        return $todos;
    }

    public function create(int $userId, string $description, string $status, string $priority, string $dueAt)
    {
        logger("TodosRepository::create - Enter.", ["User ID" => $userId]);

        $id = DB::insert('INSERT INTO todos (user_id, description, status, priority, due_at, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)',
            [
                $userId,
                $description,
                $status,
                $priority,
                $dueAt,
                new \DateTime(),
                new \DateTime()
            ]);

        $id = DB::getPdo()->lastInsertId();

        logger("TodosRepository::create - Leave.", ["Todo ID" => $id]);

        return $id;
    }

    public function delete(int $userId, $todoId)
    {
        logger("TodosRepository::delete - Enter.", ["User ID" => $userId]);

        $deleted = DB::delete('DELETE FROM todos WHERE id = ? AND user_id = ?',
            [
                $todoId,
                $userId
            ]);

        logger("TodosRepository::delete - Leave.", ["Num Deleted" => $deleted]);

        return $deleted;
    }

    public function updatePriority(int $userId, int $todoId, string $priority)
    {
        logger("TodosRepository::updatePriority - Enter.", ["User ID" => $userId, "Todo Id" => $todoId, "Priority" => $priority]);

        $updated = 0;

        if($priority == "High"   ||
           $priority == "Medium" ||
           $priority == "Low")
        {
            $updated = DB::update('UPDATE todos SET priority = :priority, updated_at = :updated_at where id = :id AND user_id = :user_id',
                [
                    'priority' => $priority,
                    'updated_at' => new \DateTime(),
                    'id'       => $todoId,
                    'user_id'  => $userId
                ]);

        } else {
            Log::error("TodosRepository::updatePriority - Unknown priority.", ["Priority" => $priority]);
            $updated = -1;
        }

        logger("TodosRepository::updatePriority - Leave.", ["Num Updated" => $updated]);

        return $updated;
    }

    public function updateStatus(int $userId, int $todoId, string $status)
    {
        logger("TodosRepository::updateStatus - Enter.", ["User ID" => $userId, "Todo Id" => $todoId, "Status" => $status]);

        $updated = 0;

        if($status  == "Not Started" ||
            $status == "In Progress" ||
            $status == "Done")
        {
            $updated = DB::update('UPDATE todos SET status = :status, updated_at = :updated_at where id = :id AND user_id = :user_id',
                [
                    'status'  => $status,
                    'updated_at' => new \DateTime(),
                    'id'      => $todoId,
                    'user_id' => $userId
                ]);

        } else {
            Log::error("TodosRepository::updateStatus - Unknown status.", ["Status" => $status]);
            $updated = -1;
        }

        logger("TodosRepository::updateStatus - Leave.", ["Num Updated" => $updated]);

        return $updated;
    }

    public function updateDueDate(int $userId, int $todoId, string $dueDate)
    {
        logger("TodosRepository::updateDueDate - Enter.", ["User ID" => $userId, "Todo Id" => $todoId, "Due Date" => $dueDate]);

        $updated = 0;
        
        if (DateTime::createFromFormat('Y-m-d', $dueDate) !== false)
        {
            $updated = DB::update('UPDATE todos SET due_at = :due_at, updated_at = :updated_at where id = :id AND user_id = :user_id',
                [
                    'due_at'  => $dueDate,
                    'updated_at' => new \DateTime(),
                    'id'      => $todoId,
                    'user_id' => $userId
                ]);

        } else {
            Log::error("TodosRepository::updateDueDate - Invalid date.", ["Due Date" => $dueDate]);
            $updated = -1;
        }

        logger("TodosRepository::updateDueDate - Leave.", ["Num Updated" => $updated]);

        return $updated;
    }
}