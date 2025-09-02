<?php
include "db.php";

// Enhanced security checks
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php?error=invalid_id");
    exit();
}

$id = intval($_GET['id']);

// Additional security: check if ID exists and is positive
if ($id <= 0) {
    header("Location: index.php?error=invalid_id");
    exit();
}

try {
    // Start transaction for data integrity
    $conn->autocommit(false);
    
    // Get file information and verify record exists before deletion
    $stmt = $conn->prepare("SELECT id, nama_mitra, file1, file2, file3 FROM tahapan_kerjasama WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Failed to prepare select statement: " . $conn->error);
    }
    
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $uploadDir = __DIR__ . "/upload/";
        
        // Delete associated physical files
        for ($i = 1; $i <= 3; $i++) {
            $fileField = "file$i";
            if (!empty($row[$fileField])) {
                $filePath = $uploadDir . $row[$fileField];
                if (file_exists($filePath)) {
                    if (!unlink($filePath)) {
                        // Log error but don't stop deletion process
                        error_log("Warning: Failed to delete file: " . $filePath);
                    }
                }
            }
        }
        
        // Delete database record
        $deleteStmt = $conn->prepare("DELETE FROM tahapan_kerjasama WHERE id = ?");
        if (!$deleteStmt) {
            throw new Exception("Failed to prepare delete statement: " . $conn->error);
        }
        
        $deleteStmt->bind_param("i", $id);
        
        if ($deleteStmt->execute()) {
            if ($deleteStmt->affected_rows > 0) {
                // Commit transaction
                $conn->commit();
                
                // Log successful deletion
                error_log("Successfully deleted mitra record: ID=$id, Name=" . $row['nama_mitra']);
                
                header("Location: index.php?success=deleted");
                exit();
            } else {
                throw new Exception("No rows were deleted - record may have been already removed");
            }
        } else {
            throw new Exception("Failed to execute delete statement: " . $deleteStmt->error);
        }
        
        $deleteStmt->close();
        
    } else {
        // Record not found
        $conn->rollback();
        header("Location: index.php?error=not_found");
        exit();
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    // Rollback transaction on any error
    $conn->rollback();
    
    // Log detailed error information
    error_log("Delete operation failed for ID=$id: " . $e->getMessage());
    
    // Redirect with appropriate error message
    if (strpos($e->getMessage(), 'not found') !== false || strpos($e->getMessage(), 'No rows') !== false) {
        header("Location: index.php?error=not_found");
    } else {
        header("Location: index.php?error=delete_failed");
    }
    exit();
    
} finally {
    // Restore autocommit
    $conn->autocommit(true);
}

// This should never be reached, but just in case
header("Location: index.php?error=unknown");
exit();
?>