<?php

require_once 'BaseModel.php';

class UserModel extends BaseModel
{

    public function findUserById($id)
    {
        $sql = 'SELECT * FROM users WHERE id = ?';
        $stmt = self::$_connection->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();
        return $rows;
    }

    public function findUser($keyword)
    {
        $keyword = "%$keyword%";
        $sql = 'SELECT * FROM users WHERE name LIKE ? OR email LIKE ?';
        $stmt = self::$_connection->prepare($sql);
        $stmt->bind_param('ss', $keyword, $keyword);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();
        return $rows;
    }

    /**
     * Authentication user
     * @param $userName
     * @param $password
     * @return array
     */
    public function auth($userName, $password)
    {
        $sql = 'SELECT * FROM users WHERE name = ?';
        $stmt = self::$_connection->prepare($sql);
        $stmt->bind_param('s', $userName);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password'])) {
            return [$user];
        }
        return [];
    }

    /**
     * Delete user by id
     * @param $id
     * @return bool
     */
    public function deleteUserById($id)
    {
        $sql = 'DELETE FROM users WHERE id = ?';
        $stmt = self::$_connection->prepare($sql);
        $stmt->bind_param('i', $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Update user
     * @param $input
     * @return bool
     */
    public function updateUser($input)
    {
        $hashedPassword = !empty($input['password']) ? password_hash($input['password'], PASSWORD_DEFAULT) : null;
        $sql = 'UPDATE users SET name = ?';
        $params = 's';
        $bindValues = [&$input['name']];

        if ($hashedPassword) {
            $sql .= ', password = ?';
            $params .= 's';
            $bindValues[] = &$hashedPassword;
        }

        $sql .= ' WHERE id = ?';
        $params .= 'i';
        $bindValues[] = &$input['id'];

        $stmt = self::$_connection->prepare($sql);
        $stmt->bind_param($params, ...$bindValues);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Insert user
     * @param $input
     * @return bool
     */
    public function insertUser($input)
    {
        $hashedPassword = password_hash($input['password'], PASSWORD_DEFAULT);
        $sql = 'INSERT INTO users (name, password) VALUES (?, ?)';
        $stmt = self::$_connection->prepare($sql);
        $stmt->bind_param('ss', $input['name'], $hashedPassword);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Search users
     * @param array $params
     * @return array
     */
    public function getUsers($params = [])
    {
        if (!empty($params['keyword'])) {
            $keyword = '%' . $params['keyword'] . '%';
            $sql = 'SELECT * FROM users WHERE name LIKE ?';
            $stmt = self::$_connection->prepare($sql);
            $stmt->bind_param('s', $keyword);
            $stmt->execute();
            $result = $stmt->get_result();
            $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            $stmt->close();
            return $users;
        } else {
            $sql = 'SELECT * FROM users';
            $stmt = self::$_connection->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            $stmt->close();
            return $users;
        }
    }
}
