<?php

require_once 'BaseModel.php';

class UserModel extends BaseModel
{
    /**
     * Find user by id
     * @param int $id
     * @return array
     */
    public function findUserById($id)
    {
        $id = (int)$id;
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = self::$_connection->prepare($sql);
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        return $rows;
    }

    /**
     * Find users by keyword (search name or fullname)
     * @param string $keyword
     * @return array
     */
    public function findUser($keyword)
    {
        $sql = "SELECT * FROM users WHERE name LIKE ? OR fullname LIKE ?";
        $like = '%' . $keyword . '%';
        $stmt = self::$_connection->prepare($sql);
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param("ss", $like, $like);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        return $rows;
    }

    /**
     * Authentication user
     * - Supports modern password_hash + password_verify
     * - Backwards compatible with MD5: if DB stores MD5 and login succeeds, re-hash to password_hash
     * @param string $userName
     * @param string $password
     * @return array  (empty array if not found or invalid)
     */
    public function auth($userName, $password)
    {
        $sql = "SELECT * FROM users WHERE name = ?";
        $stmt = self::$_connection->prepare($sql);
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param("s", $userName);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result ? $result->fetch_assoc() : null;
        $stmt->close();

        if (!$user) {
            return [];
        }

        $stored = isset($user['password']) ? $user['password'] : '';

        // 1) Try password_verify (for password_hash)
        if (!empty($stored) && password_verify($password, $stored)) {
            return [$user];
        }

        // 2) Fallback: check MD5 legacy
        if (!empty($stored) && md5($password) === $stored) {
            // Migrate: update stored password to password_hash
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $updateSql = "UPDATE users SET password = ? WHERE id = ?";
            $uStmt = self::$_connection->prepare($updateSql);
            if ($uStmt) {
                $uStmt->bind_param("si", $newHash, $user['id']);
                $uStmt->execute();
                $uStmt->close();

                // update local $user array to reflect new hash (optional)
                $user['password'] = $newHash;
            }
            return [$user];
        }

        // invalid password
        return [];
    }

    /**
     * Delete user by id
     * @param int $id
     * @return bool
     */
    public function deleteUserById($id)
    {
        $id = (int)$id;
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = self::$_connection->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("i", $id);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    /**
     * Update user
     * - if password is empty, password won't be changed
     * @param array $input  expects at least ['id', 'name'] and optionally 'password'
     * @return bool
     */
    public function updateUser($input)
    {
        if (empty($input['id'])) {
            return false;
        }

        $id = (int)$input['id'];
        $name = isset($input['name']) ? $input['name'] : '';

        // If password provided -> hash it
        if (!empty($input['password'])) {
            $hashed = password_hash($input['password'], PASSWORD_DEFAULT);
            $sql = "UPDATE users SET name = ?, password = ? WHERE id = ?";
            $stmt = self::$_connection->prepare($sql);
            if (!$stmt) {
                return false;
            }
            $stmt->bind_param("ssi", $name, $hashed, $id);
        } else {
            // Only update name
            $sql = "UPDATE users SET name = ? WHERE id = ?";
            $stmt = self::$_connection->prepare($sql);
            if (!$stmt) {
                return false;
            }
            $stmt->bind_param("si", $name, $id);
        }

        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    /**
     * Insert user
     * @param array $input expects ['name', 'password']
     * @return bool
     */
    public function insertUser($input)
    {
        $name = isset($input['name']) ? $input['name'] : '';
        $password = isset($input['password']) ? $input['password'] : '';

        // Hash password using password_hash
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (name, password) VALUES (?, ?)";
        $stmt = self::$_connection->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("ss", $name, $hashed);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    /**
     * Search users
     * @param array $params
     * @return array
     */
    public function getUsers($params = [])
    {
        if (!empty($params['keyword'])) {
            // Search in name and fullname (adjust to your real column names)
            $sql = "SELECT * FROM users WHERE name LIKE ? OR fullname LIKE ?";
            $like = '%' . $params['keyword'] . '%';
            $stmt = self::$_connection->prepare($sql);
            if (!$stmt) {
                return [];
            }
            $stmt->bind_param("ss", $like, $like);
            $stmt->execute();
            $result = $stmt->get_result();
            $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
            $stmt->close();
            return $rows;
        } else {
            $sql = "SELECT * FROM users";
            // Use select helper from BaseModel for simple fetch-all
            return $this->select($sql);
        }
    }
}
