<?php
// /api/handler.php
session_start();
header('Content-Type: application/json');

//for mailer
require_once __DIR__ . '/../vendor/autoload.php';

require_once '../config.php';
require_once '../src/Database.php';

// Add PHPMailer use statements at the top
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// use PHPMailer\PHPMailer\SMTP;


$pdo = Database::getInstance()->getConnection();

// =========================================================================
// --- Helper Functions ---
// =========================================================================
function sanitizeInput($data)
{
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}
function generateToken()
{
    return bin2hex(random_bytes(32));
}
function generateUUID()
{
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
}
function verifyToken($pdo, $userId, $token)
{
    if (!$userId || !$token) return false;
    $stmt = $pdo->prepare("SELECT token FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    return $user && hash_equals((string)$user['token'], $token);
}

// =========================================================================
// --- API Router & Middleware ---
// =========================================================================
$response = ['success' => false, 'message' => 'Invalid Request'];
$action = $_REQUEST['action'] ?? '';
$protectedActions = ['emails', 'logout', 'counts', 'search_users', 'forgot_password'];
if (in_array($action, $protectedActions)) {
    if ((!verifyToken($pdo, $_SESSION['user_id'] ?? null, $_SESSION['token'] ?? null)) && $action != 'forgot_password') {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
}

try {
    switch ($action) {
        // --- AUTHENTICATION ACTIONS ---
        case 'login':
            $email = sanitizeInput($_POST['email']);
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            if ($user && password_verify($_POST['password'], $user['password'])) {
                $token = generateToken();
                $pdo->prepare("UPDATE users SET token = ? WHERE id = ?")->execute([$token, $user['id']]);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['token'] = $token;
                $response = ['success' => true, 'user' => ['id' => $user['id'], 'name' => $user['name'], 'email' => $user['email']]];
            } else {
                $response['message'] = 'Invalid email or password.';
            }
            break;

        case 'signup':
            $name = sanitizeInput($_POST['name']);
            $email = sanitizeInput($_POST['email']);
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmtCheck->execute([$email]);
            if ($stmtCheck->fetch()) {
                $response['message'] = 'Email already registered.';
            } else {
                $token = generateToken();
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, token) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $email, $password, $token]);
                $userId = $pdo->lastInsertId();
                $_SESSION['user_id'] = $userId;
                $_SESSION['token'] = $token;
                $response = ['success' => true, 'user' => ['id' => $userId, 'name' => $name, 'email' => $email]];
            }
            break;

        case 'logout':
            if (isset($_SESSION['user_id'])) {
                $pdo->prepare("UPDATE users SET token = NULL WHERE id = ?")->execute([$_SESSION['user_id']]);
            }
            session_destroy();
            $response = ['success' => true];
            break;

        case 'counts':
            $userId = $_SESSION['user_id'];
            $stmt_unread = $pdo->prepare("SELECT COUNT(*) FROM emails WHERE recipient_id = ? AND folder = 'inbox' AND is_read = 0");
            $stmt_unread->execute([$userId]);
            $unreadCount = $stmt_unread->fetchColumn();
            $stmt_drafts = $pdo->prepare("SELECT COUNT(*) FROM emails WHERE sender_id = ? AND folder = 'drafts'");
            $stmt_drafts->execute([$userId]);
            $draftsCount = $stmt_drafts->fetchColumn();
            $response = ['success' => true, 'unread' => $unreadCount, 'drafts' => $draftsCount];
            break;

        case 'search_users':
            $term = sanitizeInput($_GET['term'] ?? '');
            $userId = $_SESSION['user_id'];
            if (strlen($term) < 1) {
                echo json_encode(['success' => true, 'users' => []]);
                exit;
            }
            $stmt = $pdo->prepare("SELECT id, name, email FROM users WHERE (name LIKE ? OR email LIKE ?) AND id != ? LIMIT 10");
            $stmt->execute(["%$term%", "%$term%", $userId]);
            $response = ['success' => true, 'users' => $stmt->fetchAll()];
            break;
        case 'forgot_password':
            $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
            if (!$email) {
                throw new Exception("Invalid email format provided.");
            }

            // 1. Check if the user's email exists
            $stmt = $pdo->prepare("SELECT id, name FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                // 2. Generate a secure, 6-digit OTP
                $otp = random_int(100000, 999999);
                $otp_expiry = new DateTime('+5 minutes'); // OTP will be valid for 5 minutes

                // 3. Securely store a hash of the OTP and its expiry in the database
                $stmt_update = $pdo->prepare("UPDATE users SET reset_otp = ?, reset_otp_expiry = ? WHERE id = ?");
                $stmt_update->execute([password_hash($otp, PASSWORD_DEFAULT), $otp_expiry->format('Y-m-d H:i:s'), $user['id']]);

                // 4. Send the OTP to the user's email using PHPMailer
                $mail = new PHPMailer(true);
                try {
                    // Server settings
                    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
                    $mail->isSMTP();
                    $mail->Host       = SMTP_HOST;
                    $mail->SMTPAuth   = true;
                    $mail->Username   = SMTP_USERNAME;
                    $mail->Password   = SMTP_PASSWORD;
                    $mail->SMTPSecure = SMTP_SECURE;
                    $mail->Port       = SMTP_PORT;


                    // Recipients
                    $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
                    $mail->addAddress($email, $user['name']); // Add a recipient

                    // Email Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Your Password Reset Code';
                    $mail->Body    = "
                                    <div style='font-family: sans-serif; padding: 20px; color: #333;'>
                                        <h2>Password Reset Request</h2>
                                        <p>Hi {$user['name']},</p>
                                        <p>Use the code below to reset your password. This code is valid for 5 minutes.</p>
                                        <p style='font-size: 24px; font-weight: bold; letter-spacing: 5px; background: #f0f0f0; padding: 10px 20px; border-radius: 5px; text-align: center;'>
                                            {$otp}
                                        </p>
                                        <p>If you did not request a password reset, you can safely ignore this email.</p>
                                    </div>
                                ";
                    $mail->AltBody = "Your password reset code is: {$otp}. It is valid for 15 minutes.";
                    $mail->send();
                    $response = ['success' => true, 'message' => 'A password reset code has been sent to your email.'];
                } catch (Exception $e) {
                    // Log the detailed error for debugging purposes, but show a generic error to the user
                    error_log("PHPMailer Error: " . $mail->ErrorInfo);
                    echo $mail->ErrorInfo;
                    throw new Exception(" The email could not be sent. Please try again later.");
                }
            } else {
                // For security, always return a success message to prevent user enumeration attacks.
                $response = ['success' => true, 'message' => 'If an account with that email exists, a reset code has been sent.'];
            }
            break;
        case 'reset_password':
            $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
            $otp = preg_replace('/[^0-9]/', '', $_POST['otp']); // Sanitize OTP
            $new_password = $_POST['new_password'];

            if (!$email || strlen($otp) !== 6 || empty($new_password)) {
                throw new Exception("Invalid data provided. Please check your inputs.");
            }

            // 1. Find the user and their stored OTP hash
            $stmt = $pdo->prepare("SELECT id, reset_otp, reset_otp_expiry FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user || !$user['reset_otp']) {
                throw new Exception("No pending password reset found for this email.");
            }

            // 2. Check if the OTP has expired
            $expiry_time = new DateTime($user['reset_otp_expiry']);
            $current_time = new DateTime();
            if ($current_time > $expiry_time) {
                // For security, clear the expired OTP from the database
                $pdo->prepare("UPDATE users SET reset_otp = NULL, reset_otp_expiry = NULL WHERE id = ?")->execute([$user['id']]);
                throw new Exception("The code has expired. Please request a new one.");
            }

            // 3. Verify the OTP against the stored hash
            if (!password_verify($otp, $user['reset_otp'])) {
                throw new Exception("The code you entered is incorrect.");
            }

            // 4. If all checks pass, hash the new password and update the database
            $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);

            // 5. Update password and, CRUCIALLY, clear the OTP fields to prevent reuse
            $stmt_update = $pdo->prepare("UPDATE users SET password = ?, reset_otp = NULL, reset_otp_expiry = NULL WHERE id = ?");
            $stmt_update->execute([$new_password_hash, $user['id']]);

            $response = ['success' => true, 'message' => 'Password has been reset successfully. You can now log in.'];
            break;





        // --- EMAIL ACTIONS ---
        case 'emails':
            $userId = $_SESSION['user_id'];

            // =========================================================================
            // --- HANDLE GET REQUESTS (FETCHING EMAILS FOR A FOLDER) ---
            // =========================================================================
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $folder = sanitizeInput($_GET['folder'] ?? 'inbox');

                switch ($folder) {
                    case 'drafts':
                        $stmt = $pdo->prepare("SELECT * FROM emails WHERE sender_id = ? AND folder = 'drafts' ORDER BY created_at DESC");
                        $stmt->execute([$userId]);
                        $emails = $stmt->fetchAll();

                        // "Hydrate" drafts with recipient names and IDs for the frontend to render pills
                        foreach ($emails as &$email) {
                            $to_ids = json_decode($email['to_ids_json']) ?? '[]';
                            $cc_ids = json_decode($email['cc_ids_json']) ?? '[]';


                            $all_recipient_ids = array_unique(array_merge($to_ids, $cc_ids));
                            if (empty($all_recipient_ids)) throw new Exception("At least one recipient is required.");

                            $placeholders = implode(',', array_fill(0, count($all_recipient_ids), '?'));
                            $stmt_names = $pdo->prepare("SELECT id, name FROM users WHERE id IN ($placeholders)");
                            $stmt_names->execute($all_recipient_ids);
                            $recipients_data = $stmt_names->fetchAll(PDO::FETCH_KEY_PAIR);

                            $to_str = implode(', ', array_intersect_key($recipients_data, array_flip($to_ids)));
                            $cc_str = implode(', ', array_intersect_key($recipients_data, array_flip($cc_ids)));

                            $email['to_recipients_data'] = $to_str;
                            $email['cc_recipients_data'] = $cc_str;
                        }
                        break;
                    case 'sent':
                        $stmt = $pdo->prepare("SELECT id, conversation_id, subject, content, to_ids_json,cc_ids_json, created_at FROM emails WHERE sender_id = ? AND folder = 'sent' ORDER BY created_at DESC");
                        $stmt->execute([$userId]);

                        $emails = $stmt->fetchAll();
                        foreach ($emails as &$email) {
                            $to_ids = json_decode($email['to_ids_json']) ?? [];
                            $cc_ids = json_decode($email['cc_ids_json']) ?? [];


                            $all_recipient_ids = array_unique(array_merge($to_ids, $cc_ids));
                            if (empty($all_recipient_ids)) throw new Exception("At least one recipient is required.");

                            $placeholders = implode(',', array_fill(0, count($all_recipient_ids), '?'));
                            $stmt_names = $pdo->prepare("SELECT id, name FROM users WHERE id IN ($placeholders)");
                            $stmt_names->execute($all_recipient_ids);
                            $recipients_data = $stmt_names->fetchAll(PDO::FETCH_KEY_PAIR);

                            $to_str = implode(', ', array_intersect_key($recipients_data, array_flip($to_ids)));
                            $cc_str = implode(', ', array_intersect_key($recipients_data, array_flip($cc_ids)));

                            $email['to_recipients_data'] = $to_str;
                            $email['cc_recipients_data'] = $cc_str;
                        }
                        // var_dump($email);

                        break;
                    case 'trash':
                        // A more complex query to get relevant names for the trash folder
                        $stmt = $pdo->prepare("
                            SELECT 
                                e.*, 
                                u_from.name as from_name, 
                                (SELECT GROUP_CONCAT(u.name) FROM users u WHERE FIND_IN_SET(u.id, e.to_recipients)) as to_names
                            FROM emails e 
                            LEFT JOIN users u_from ON e.sender_id = u_from.id 
                            WHERE (e.sender_id = ? OR e.recipient_id = ?) AND e.folder = 'trash' 
                            ORDER BY e.created_at DESC
                        ");
                        $stmt->execute([$userId, $userId]);
                        $emails = $stmt->fetchAll();
                        break;
                    default: // inbox
                        $stmt = $pdo->prepare("SELECT e.*, 
                        u.name AS from_name, 
                        u.email AS from_email
                        FROM emails e
                        JOIN users u ON e.sender_id = u.id
                        WHERE (JSON_CONTAINS(e.to_ids_json, JSON_QUOTE('$userId')) OR JSON_CONTAINS(e.to_ids_json, JSON_QUOTE('$userId'))) AND e.folder = 'sent'
                        ORDER BY e.created_at DESC");
                        $stmt->execute();
                        $emails = $stmt->fetchAll();
                }

                // This loop runs for all folders to fetch their attachments
                foreach ($emails as &$email) {
                    $stmt_att = $pdo->prepare("SELECT * FROM attachments WHERE email_id = ?");
                    $stmt_att->execute([$email['id']]);
                    $email['attachments'] = $stmt_att->fetchAll();
                }
                $response = ['success' => true, 'emails' => $emails];
            }

            // =========================================================================
            // --- HANDLE POST REQUESTS (SEND, SAVE, DELETE, ETC.) ---
            // =========================================================================
            elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $sub_action = $_POST['sub_action'];
                switch ($sub_action) {
                    case 'send':

                        $pdo->beginTransaction();
                        $draft_id = $_POST['draft_id'] ?? null;

                        $to_ids = json_encode($_POST['to_ids']) ?? [];
                        $cc_ids = (isset($_POST['cc_ids'])) ? json_encode($_POST['cc_ids']) ?? '[]' : '[]';
                        // $all_recipient_ids = array_unique(array_merge($to_ids, $cc_ids));
                        // if (empty($all_recipient_ids)) throw new Exception("At least one recipient is required.");

                        // $placeholders = implode(',', array_fill(0, count($all_recipient_ids), '?'));
                        // $stmt_names = $pdo->prepare("SELECT id, name FROM users WHERE id IN ($placeholders)");
                        // $stmt_names->execute($all_recipient_ids);
                        // $recipients_data = $stmt_names->fetchAll(PDO::FETCH_KEY_PAIR);

                        // $to_str = implode(', ', array_intersect_key($recipients_data, array_flip($to_ids)));
                        // $cc_str = implode(', ', array_intersect_key($recipients_data, array_flip($cc_ids)));

                        $conversation_id = generateUUID();
                        $subject = sanitizeInput($_POST['subject'] ?? '');
                        $content = sanitizeInput($_POST['content'] ?? '');

                        $stmt_sent = $pdo->prepare("INSERT INTO emails (conversation_id, sender_id, to_ids_json, cc_ids_json, subject, content, folder) VALUES (?, ?, ?, ?, ?, ?, 'sent')");
                        $stmt_sent->execute([$conversation_id, $userId, $to_ids, $cc_ids, $subject, $content]);
                        $sent_email_id = $pdo->lastInsertId();

                        // $stmt_inbox = $pdo->prepare("INSERT INTO emails (conversation_id, sender_id, recipient_id, to_recipients, cc_recipients, subject, content, folder) VALUES (?, ?, ?, ?, ?, ?, ?, 'inbox')");
                        // foreach ($all_recipient_ids as $recipient_id) {
                        //     if ($recipient_id != $userId) $stmt_inbox->execute([$conversation_id, $userId, $recipient_id, $to_str, $cc_str, $subject, $content]);
                        // }

                        // Copy existing attachments from a draft if it exists
                        if ($draft_id) {
                            $stmt_get_atts = $pdo->prepare("SELECT * FROM attachments WHERE email_id = ?");
                            $stmt_get_atts->execute([$draft_id]);
                            $stmt_copy_att = $pdo->prepare("INSERT INTO attachments (email_id, file_name, file_path, file_size, file_type) VALUES (?, ?, ?, ?, ?)");
                            foreach ($stmt_get_atts->fetchAll() as $att) {
                                $stmt_copy_att->execute([$sent_email_id, $att['file_name'], $att['file_path'], $att['file_size'], $att['file_type']]);
                            }
                        }

                        // Handle any NEW attachments uploaded with this email
                        if (!empty($_FILES['attachments']['tmp_name'][0])) {
                            $uploadDir = '../uploads/';
                            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                            foreach ($_FILES['attachments']['tmp_name'] as $key => $tmpName) {
                                if ($_FILES['attachments']['error'][$key] !== UPLOAD_ERR_OK || empty($tmpName)) continue;
                                $originalFileName = basename($_FILES['attachments']['name'][$key]);
                                $newFilePath = uniqid() . '_' . $originalFileName;
                                if (move_uploaded_file($tmpName, $uploadDir . $newFilePath)) {
                                    $stmt_att = $pdo->prepare("INSERT INTO attachments (email_id, file_name, file_path, file_size, file_type) VALUES (?,?,?,?,?)");
                                    $stmt_att->execute([$sent_email_id, $originalFileName, $newFilePath, $_FILES['attachments']['size'][$key], $_FILES['attachments']['type'][$key]]);
                                }
                            }
                        }

                        // Finally, delete the draft after it has been sent
                        if ($draft_id) {
                            $pdo->prepare("DELETE FROM emails WHERE id = ? AND sender_id = ? AND folder = 'drafts'")->execute([$draft_id, $userId]);
                            // Also delete attachments associated only with the draft
                            $pdo->prepare("DELETE FROM attachments WHERE email_id = ?")->execute([$draft_id]);
                        }

                        $pdo->commit();
                        $response = ['success' => true];
                        break;

                    case 'save_draft':
                        $pdo->beginTransaction();
                        $draft_id = $_POST['draft_id'] ?? null;

                        $sql = $draft_id ? "UPDATE emails SET subject=?, content=?, to_ids_json=?, cc_ids_json=? WHERE id=? AND sender_id=? AND folder='drafts'" : "INSERT INTO emails (sender_id, subject, content, to_ids_json, cc_ids_json, folder) VALUES (?,?,?,?,?, 'drafts')";
                        $params = [sanitizeInput($_POST['subject'] ?? ''), sanitizeInput($_POST['content'] ?? ''), json_encode($_POST['to_ids'] ?? []), json_encode($_POST['cc_ids'] ?? [])];
                        if ($draft_id) array_push($params, $draft_id, $userId);
                        else array_unshift($params, $userId);

                        $stmt = $pdo->prepare($sql);
                        $stmt->execute($params);
                        $email_id = $draft_id ?: $pdo->lastInsertId();

                        if (!empty($_FILES['attachments']['tmp_name'][0])) {
                            $uploadDir = '../uploads/';
                            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                            foreach ($_FILES['attachments']['tmp_name'] as $key => $tmpName) {
                                if ($_FILES['attachments']['error'][$key] !== UPLOAD_ERR_OK || empty($tmpName)) continue;
                                $fileName = basename($_FILES['attachments']['name'][$key]);
                                $filePath = uniqid() . '_' . $fileName;
                                if (move_uploaded_file($tmpName, $uploadDir . $filePath)) {
                                    $stmt_att = $pdo->prepare("INSERT INTO attachments (email_id, file_name, file_path, file_size, file_type) VALUES (?,?,?,?,?)");
                                    $stmt_att->execute([$email_id, $fileName, $filePath, $_FILES['attachments']['size'][$key], $_FILES['attachments']['type'][$key]]);
                                }
                            }
                        }
                        $pdo->commit();
                        $response = ['success' => true];
                        break;

                    case 'delete_attachment':
                        $attachment_id = $_POST['attachment_id'] ?? null;
                        $email_id = $_POST['email_id'] ?? null;
                        if (!$attachment_id || !$email_id) throw new Exception("Missing attachment or email ID.");

                        $stmt_check = $pdo->prepare("SELECT sender_id FROM emails WHERE id = ?");
                        $stmt_check->execute([$email_id]);
                        if ($stmt_check->fetchColumn() != $userId) throw new Exception("You do not have permission to delete this attachment.", 403);

                        $stmt_find = $pdo->prepare("SELECT file_path FROM attachments WHERE id = ? AND email_id = ?");
                        $stmt_find->execute([$attachment_id, $email_id]);
                        $file_path = $stmt_find->fetchColumn();

                        $stmt_delete = $pdo->prepare("DELETE FROM attachments WHERE id = ?");
                        $stmt_delete->execute([$attachment_id]);

                        if ($file_path && file_exists('../uploads/' . $file_path)) {
                            unlink('../uploads/' . $file_path);
                        }

                        $response = ['success' => true];
                        break;

                    case 'mark_read':
                        $stmt = $pdo->prepare("UPDATE emails SET is_read=1 WHERE id=? AND recipient_id=?");
                        $stmt->execute([sanitizeInput($_POST['email_id']), $userId]);
                        $response = ['success' => $stmt->rowCount() > 0];
                        break;

                    case 'delete':
                        $stmt = $pdo->prepare("UPDATE emails SET folder='trash' WHERE id=? AND (sender_id=? OR recipient_id=?)");
                        $stmt->execute([sanitizeInput($_POST['email_id']), $userId, $userId]);
                        $response = ['success' => $stmt->rowCount() > 0];
                        break;
                }
            }
            break;
    }
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    http_response_code(500);
    $response = ['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()];
}

echo json_encode($response);
exit;
