<?php
/**
 * Media Service
 * Handles file uploads and media management
 * Omniwallet CMS
 */

namespace App\Services;

use App\Models\Media;

class MediaService
{
    private Media $mediaModel;
    private string $uploadDir;
    private string $baseUrl;

    public function __construct()
    {
        $this->mediaModel = new Media();
        $this->uploadDir = __DIR__ . '/../../public/uploads';
        $this->baseUrl = '/uploads';

        // Ensure upload directory exists
        $this->ensureDirectoryExists($this->uploadDir);
    }

    /**
     * Upload a file
     */
    public function upload(array $file): array
    {
        // Validate file
        $validation = $this->validateFile($file);
        if (!$validation['valid']) {
            return ['success' => false, 'error' => $validation['error']];
        }

        // Generate unique filename
        $extension = Media::ALLOWED_TYPES[$file['type']] ?? pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $this->generateUniqueFilename($extension);

        // Determine subdirectory based on type
        $subdir = $this->getSubdirectory($file['type']);
        $targetDir = $this->uploadDir . '/' . $subdir;
        $this->ensureDirectoryExists($targetDir);

        // Move file
        $targetPath = $targetDir . '/' . $filename;
        $relativePath = $subdir . '/' . $filename;

        // Ensure the full directory path exists (including date subdirectories)
        $fullDir = dirname($targetPath);
        $this->ensureDirectoryExists($fullDir);

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            return ['success' => false, 'error' => 'Error al mover el archivo'];
        }

        // Get image dimensions if applicable
        $width = null;
        $height = null;
        if (strpos($file['type'], 'image/') === 0 && $file['type'] !== 'image/svg+xml') {
            $dimensions = getimagesize($targetPath);
            if ($dimensions) {
                $width = $dimensions[0];
                $height = $dimensions[1];
            }
        }

        // Store in database
        $mediaId = $this->mediaModel->create([
            'filename' => $filename,
            'original_filename' => $file['name'],
            'filepath' => $relativePath,
            'filetype' => $file['type'],
            'filesize' => $file['size'],
            'width' => $width,
            'height' => $height
        ]);

        if (!$mediaId) {
            // Remove file if database insert failed
            @unlink($targetPath);
            return ['success' => false, 'error' => 'Error al guardar en base de datos'];
        }

        $media = $this->mediaModel->find($mediaId);

        return [
            'success' => true,
            'media' => $media
        ];
    }

    /**
     * Delete a media file
     */
    public function delete(int $id): bool
    {
        $media = $this->mediaModel->find($id);

        if (!$media) {
            return false;
        }

        // Delete physical file
        $filePath = $this->uploadDir . '/' . ($media['filepath'] ?? '');
        if (file_exists($filePath)) {
            @unlink($filePath);
        }

        // Delete from database
        return $this->mediaModel->delete($id);
    }

    /**
     * Update media metadata
     */
    public function updateMetadata(int $id, array $data): bool
    {
        $allowedFields = ['alt_text', 'title', 'caption'];
        $updateData = array_intersect_key($data, array_flip($allowedFields));

        return $this->mediaModel->update($id, $updateData);
    }

    /**
     * Validate uploaded file
     */
    private function validateFile(array $file): array
    {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors = [
                UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño máximo permitido',
                UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño máximo del formulario',
                UPLOAD_ERR_PARTIAL => 'El archivo se subió parcialmente',
                UPLOAD_ERR_NO_FILE => 'No se seleccionó ningún archivo',
                UPLOAD_ERR_NO_TMP_DIR => 'Falta la carpeta temporal',
                UPLOAD_ERR_CANT_WRITE => 'Error al escribir el archivo',
                UPLOAD_ERR_EXTENSION => 'Extensión no permitida'
            ];
            return ['valid' => false, 'error' => $errors[$file['error']] ?? 'Error de subida'];
        }

        // Check file size
        if ($file['size'] > Media::MAX_SIZE) {
            return ['valid' => false, 'error' => 'El archivo excede el tamaño máximo de ' . Media::formatSize(Media::MAX_SIZE)];
        }

        // Check mime type
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!isset(Media::ALLOWED_TYPES[$mimeType])) {
            return ['valid' => false, 'error' => 'Tipo de archivo no permitido: ' . $mimeType];
        }

        // Update file type with actual mime type
        $file['type'] = $mimeType;

        return ['valid' => true, 'file' => $file];
    }

    /**
     * Generate unique filename
     */
    private function generateUniqueFilename(string $extension): string
    {
        return date('Y/m/') . uniqid() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
    }

    /**
     * Get subdirectory based on mime type
     */
    private function getSubdirectory(string $mimeType): string
    {
        if (strpos($mimeType, 'image/') === 0) {
            return 'images';
        }
        if (strpos($mimeType, 'video/') === 0) {
            return 'videos';
        }
        if ($mimeType === 'application/pdf') {
            return 'documents';
        }
        return 'files';
    }

    /**
     * Ensure directory exists
     */
    private function ensureDirectoryExists(string $path): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }

    /**
     * Get upload directory
     */
    public function getUploadDir(): string
    {
        return $this->uploadDir;
    }

    /**
     * Get base URL
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }
}
