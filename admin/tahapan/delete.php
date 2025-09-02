<?php
include "db.php";

// Security: Check if ID is provided and is valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php?error=invalid_id");
    exit();
}

$id = intval($_GET['id']);

try {
    // Start transaction for data integrity
    $conn->begin_transaction();
    
    // Get file information before deletion
    $stmt = $conn->prepare("SELECT file1, file2, file3 FROM tahapan_kerjasama WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Delete physical files
        for ($i = 1; $i <= 3; $i++) {
            $fileField = "file$i";
            if (!empty($row[$fileField])) {
                $filePath = __DIR__ . "/upload/" . $row[$fileField];
                if (file_exists($filePath)) {
                    if (!unlink($filePath)) {
                        error_log("Failed to delete file: " . $filePath);
                    }
                }
            }
        }
        
        // Delete database record
        $deleteStmt = $conn->prepare("DELETE FROM tahapan_kerjasama WHERE id = ?");
        $deleteStmt->bind_param("i", $id);
        
        if ($deleteStmt->execute()) {
            // Commit transaction
            $conn->commit();
            header("Location: index.php?success=deleted");
            exit();
        } else {
            throw new Exception("Failed to delete record from database");
        }
        
    } else {
        // Record not found
        $conn->rollback();
        header("Location: index.php?error=not_found");
        exit();
    }
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    error_log("Delete operation failed: " . $e->getMessage());
    header("Location: index.php?error=delete_failed");
    exit();
}
?>