<?php
/**
 * Media Controller
 * Omniwallet CMS Admin
 */

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Media;
use App\Services\MediaService;

class MediaController extends Controller
{
    private Media $media;
    private MediaService $mediaService;

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->media = new Media();
        $this->mediaService = new MediaService();
    }

    /**
     * Media library index
     */
    public function index(): void
    {
        $page = (int)($_GET['page'] ?? 1);
        $type = $_GET['type'] ?? null;
        $search = $_GET['search'] ?? null;
        $format = $_GET['format'] ?? null;

        // Filter for images specifically
        if ($type === 'images') {
            $type = 'image';
        }

        $result = $this->media->getPaginated($page, 50, $type, $search);

        // Return JSON if requested
        if ($format === 'json') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'media' => array_map(function($item) {
                    $item['formatted_size'] = Media::formatSize($item['filesize'] ?? 0);
                    return $item;
                }, $result['items']),
                'pagination' => [
                    'total' => $result['total'],
                    'pages' => $result['pages'],
                    'current_page' => $result['current_page']
                ]
            ]);
            return;
        }

        $statistics = $this->media->getStatistics();

        $this->renderAdmin('media/index', [
            'title' => 'Media Library',
            'items' => $result['items'],
            'pagination' => [
                'total' => $result['total'],
                'pages' => $result['pages'],
                'current_page' => $result['current_page']
            ],
            'statistics' => $statistics,
            'currentType' => $type,
            'search' => $search,
            'flash' => $this->getFlash()
        ]);
    }

    /**
     * Upload file via AJAX
     */
    public function upload(): void
    {
        header('Content-Type: application/json');

        if (!isset($_FILES['file'])) {
            echo json_encode(['success' => false, 'error' => 'No se recibió ningún archivo']);
            return;
        }

        $result = $this->mediaService->upload($_FILES['file']);

        echo json_encode($result);
    }

    /**
     * Update media metadata
     */
    public function update(int $id): void
    {
        $this->validateCsrf();

        $media = $this->media->find($id);

        if (!$media) {
            $this->flash('error', 'Archivo no encontrado');
            $this->redirect('/admin/media');
        }

        $data = [
            'alt_text' => trim($_POST['alt_text'] ?? ''),
            'title' => trim($_POST['title'] ?? ''),
            'caption' => trim($_POST['caption'] ?? '')
        ];

        if ($this->mediaService->updateMetadata($id, $data)) {
            $this->flash('success', 'Archivo actualizado');
        } else {
            $this->flash('error', 'Error al actualizar');
        }

        $this->redirect('/admin/media');
    }

    /**
     * Delete media file
     */
    public function delete(int $id): void
    {
        $this->validateCsrf();

        if ($this->mediaService->delete($id)) {
            $this->flash('success', 'Archivo eliminado');
        } else {
            $this->flash('error', 'Error al eliminar el archivo');
        }

        $this->redirect('/admin/media');
    }

    /**
     * Get media info via AJAX
     */
    public function info(int $id): void
    {
        header('Content-Type: application/json');

        $media = $this->media->find($id);

        if (!$media) {
            echo json_encode(['success' => false, 'error' => 'Archivo no encontrado']);
            return;
        }

        $media['formatted_size'] = Media::formatSize($media['filesize'] ?? 0);

        echo json_encode(['success' => true, 'media' => $media]);
    }

    /**
     * Media picker modal (for embedding in other pages)
     */
    public function picker(): void
    {
        $type = $_GET['type'] ?? 'image';
        $result = $this->media->getPaginated(1, 50, $type);

        // Render picker without layout (for modal embedding)
        $this->view->render('admin/media/picker', [
            'items' => $result['items'],
            'type' => $type
        ]);
    }

    /**
     * Browse media (AJAX endpoint for media picker)
     */
    public function browse(): void
    {
        header('Content-Type: application/json');

        $page = (int)($_GET['page'] ?? 1);
        $type = $_GET['type'] ?? null;
        $search = $_GET['search'] ?? null;

        $result = $this->media->getPaginated($page, 24, $type, $search);

        echo json_encode([
            'success' => true,
            'items' => array_map(function($item) {
                $item['formatted_size'] = Media::formatSize($item['filesize'] ?? 0);
                return $item;
            }, $result['items']),
            'pagination' => [
                'total' => $result['total'],
                'pages' => $result['pages'],
                'current_page' => $result['current_page']
            ]
        ]);
    }
}
